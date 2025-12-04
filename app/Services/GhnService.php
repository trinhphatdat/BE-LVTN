<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GhnService
{
    private $token;
    private $baseUrl;
    private $shopId;

    public function __construct()
    {
        $this->token = config('services.ghn.token');
        $this->baseUrl = config('services.ghn.url');
        $this->shopId = config('services.ghn.shop_id');
    }

    public function getProvinces()
    {
        return $this->makeRequest('/province');
    }

    public function getDistricts($provinceId)
    {
        return $this->makeRequest('/district', ['province_id' => $provinceId]);
    }

    public function getWards($districtId)
    {
        return $this->makeRequest('/ward', ['district_id' => $districtId]);
    }

    /**
     * Tính phí vận chuyển
     */
    public function calculateShippingFee($data)
    {
        $endpoint = str_replace('/master-data', '/v2/shipping-order/fee', $this->baseUrl);

        $payload = [
            'service_type_id' => (int)($data['service_type_id'] ?? 2),
            'from_district_id' => (int)config('services.ghn.from_district_id'),
            'from_ward_code' => (string)config('services.ghn.from_ward_code'),
            'to_district_id' => (int)$data['to_district_id'],
            'to_ward_code' => (string)$data['to_ward_code'],
            'height' => (int)($data['height'] ?? 1),
            'length' => (int)($data['length'] ?? 40),
            'weight' => (int)($data['weight'] ?? 200),
            'width' => (int)($data['width'] ?? 28),
            'insurance_value' => (int)($data['insurance_value'] ?? 0),
        ];

        return $this->makeRequest('/v2/shipping-order/fee', $payload, 'POST', $endpoint);
    }

    /**
     * Tạo đơn hàng GHN
     */
    public function createOrder($data)
    {
        $endpoint = str_replace('/master-data', '/v2/shipping-order/create', $this->baseUrl);

        $payload = [
            'payment_type_id' => (int)($data['payment_type_id'] ?? 2),
            'note' => $data['note'] ?? '',
            'required_note' => $data['required_note'] ?? 'KHONGCHOXEMHANG',
            'return_phone' => config('services.ghn.return_phone'),
            'return_address' => config('services.ghn.return_address'),
            'return_district_id' => (int)config('services.ghn.from_district_id'),
            'return_ward_code' => (string)config('services.ghn.from_ward_code'),
            'client_order_code' => $data['client_order_code'] ?? '',
            'to_name' => $data['to_name'],
            'to_phone' => $data['to_phone'],
            'to_address' => $data['to_address'],
            'to_ward_code' => (string)$data['to_ward_code'],
            'to_district_id' => (int)$data['to_district_id'],
            'cod_amount' => (int)($data['cod_amount'] ?? 0),
            'content' => $data['content'] ?? 'Thời trang',
            'weight' => (int)($data['weight'] ?? 200),
            'length' => (int)($data['length'] ?? 40),
            'width' => (int)($data['width'] ?? 28),
            'height' => (int)($data['height'] ?? 1),
            'insurance_value' => (int)($data['insurance_value'] ?? 0),
            'service_type_id' => (int)($data['service_type_id'] ?? 2),
            'items' => $data['items'] ?? [],
        ];

        return $this->makeRequest('/v2/shipping-order/create', $payload, 'POST', $endpoint);
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function getOrderDetail($orderCode)
    {
        $endpoint = str_replace('/master-data', '/v2/shipping-order/detail', $this->baseUrl);

        return $this->makeRequest('/v2/shipping-order/detail', [
            'order_code' => $orderCode
        ], 'POST', $endpoint);
    }

    /**
     * Hủy đơn hàng GHN
     */
    public function cancelOrder($orderCodes)
    {
        $endpoint = str_replace('/master-data', '/v2/switch-status/cancel', $this->baseUrl);

        $payload = [
            'order_codes' => is_array($orderCodes) ? $orderCodes : [$orderCodes]
        ];

        return $this->makeRequest('/v2/switch-status/cancel', $payload, 'POST', $endpoint);
    }

    private function makeRequest($endpoint, $params = [], $method = 'GET', $customUrl = null)
    {
        try {
            $url = $customUrl ?? $this->baseUrl . $endpoint;

            $headers = [
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ];

            // Thêm ShopId cho các endpoint cần thiết
            if (
                strpos($endpoint, 'shipping-order/fee') !== false ||
                strpos($endpoint, 'shipping-order/create') !== false ||
                strpos($endpoint, 'shipping-order/detail') !== false ||
                strpos($endpoint, 'switch-status/cancel') !== false
            ) {
                $headers['ShopId'] = $this->shopId;
            }

            $request = Http::withHeaders($headers);

            if ($method === 'POST') {
                $response = $request->post($url, $params);
            } else {
                $response = $request->get($url, $params);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('GHN API Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
