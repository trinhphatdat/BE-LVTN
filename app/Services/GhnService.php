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
     * 
     * @param array $data
     * - to_district_id: ID quận/huyện người nhận
     * - to_ward_code: Mã phường/xã người nhận
     * - weight: Cân nặng (gram)
     * - length: Chiều dài (cm)
     * - width: Chiều rộng (cm)
     * - height: Chiều cao (cm)
     * - insurance_value: Giá trị đơn hàng (để tính phí bảo hiểm)
     * - service_type_id: Loại dịch vụ (2 = Express, 5 = Standard)
     */
    public function calculateShippingFee($data)
    {
        $endpoint = str_replace('/master-data', '/v2/shipping-order/fee', $this->baseUrl);

        $payload = [
            'service_type_id' => (int)($data['service_type_id'] ?? 2),
            'from_district_id' => (int)config('services.ghn.from_district_id'), // Cast sang int
            'from_ward_code' => (string)config('services.ghn.from_ward_code'), // Cast sang string
            'to_district_id' => (int)$data['to_district_id'], // Cast sang int
            'to_ward_code' => (string)$data['to_ward_code'], // Cast sang string
            'height' => (int)($data['height'] ?? 15),
            'length' => (int)($data['length'] ?? 20),
            'weight' => (int)($data['weight'] ?? 200),
            'width' => (int)($data['width'] ?? 20),
            'insurance_value' => (int)($data['insurance_value'] ?? 0),
        ];

        return $this->makeRequest('/v2/shipping-order/fee', $payload, 'POST', $endpoint);
    }

    private function makeRequest($endpoint, $params = [], $method = 'GET', $customUrl = null)
    {
        try {
            $url = $customUrl ?? $this->baseUrl . $endpoint;

            $headers = [
                'Token' => $this->token,
                'Content-Type' => 'application/json'
            ];

            // Thêm ShopId nếu cần (cho API tính phí)
            if (strpos($endpoint, 'shipping-order/fee') !== false) {
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
