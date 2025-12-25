<?php
// filepath: c:\Workspace\LVTN\BE-LVTN\app\Services\GhnSyncService.php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GhnSyncService
{
    private $ghnService;

    public function __construct(GhnService $ghnService)
    {
        $this->ghnService = $ghnService;
    }

    /**
     * Đồng bộ trạng thái một đơn hàng
     */
    public function syncOrderStatus(Order $order)
    {
        if (!$order->ghn_order_code) {
            return false;
        }

        try {
            $response = $this->ghnService->getOrderDetail($order->ghn_order_code);

            if (!$response->successful() || $response->json()['code'] !== 200) {
                Log::error('Failed to get GHN order detail', [
                    'order_id' => $order->id,
                    'ghn_order_code' => $order->ghn_order_code,
                    'response' => $response->json()
                ]);
                return false;
            }

            $ghnData = $response->json()['data'];

            return $this->updateOrderFromGhnData($order, $ghnData);
        } catch (\Exception $e) {
            Log::error('Error syncing GHN order status', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Cập nhật order từ dữ liệu GHN
     */
    public function updateOrderFromGhnData(Order $order, array $ghnData)
    {
        DB::beginTransaction();

        try {
            $updateData = [
                'ghn_status' => $ghnData['status'] ?? null,
                'ghn_sort_code' => $ghnData['sort_code'] ?? null,
                'ghn_expected_delivery_time' => $ghnData['expected_delivery_time'] ?? null,
                'ghn_cod_amount' => $ghnData['cod_amount'] ?? 0,
                'ghn_note' => $ghnData['note'] ?? null,
                'ghn_last_sync_at' => Carbon::now(),
            ];

            // Lấy status text từ log (nếu có)
            if (isset($ghnData['log']) && is_array($ghnData['log']) && count($ghnData['log']) > 0) {
                $latestLog = end($ghnData['log']);
                $updateData['ghn_status_text'] = $latestLog['status_name'] ?? null;
                $updateData['ghn_log'] = $ghnData['log'];
            }

            // Tính tổng phí (nếu có)
            if (isset($ghnData['fee'])) {
                $updateData['ghn_total_fee'] = $ghnData['fee']['main_service'] ?? $order->ghn_total_fee;
            }

            // Mapping trạng thái GHN sang order_status
            if (isset($ghnData['status'])) {
                $newOrderStatus = Order::mapGhnStatusToOrderStatus($ghnData['status']);
                $updateData['order_status'] = $newOrderStatus;

                // Cập nhật thời gian giao hàng thành công
                if ($newOrderStatus === 'delivered' && !$order->delivered_at) {
                    $updateData['delivered_at'] = Carbon::now();

                    // Nếu COD thì đánh dấu đã thanh toán
                    if ($order->payment_method === 'cod' && $order->payment_status === 'unpaid') {
                        $updateData['payment_status'] = 'paid';
                        $updateData['paid_at'] = Carbon::now();
                    }
                }

                // Cập nhật thời gian hủy
                if ($newOrderStatus === 'cancelled' && !$order->cancelled_at) {
                    $updateData['cancelled_at'] = Carbon::now();
                }
            }

            $order->update($updateData);

            Log::info('Synced order status from GHN', [
                'order_id' => $order->id,
                'ghn_status' => $ghnData['status'],
                'order_status' => $updateData['order_status']
            ]);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Đồng bộ tất cả đơn hàng đang active
     */
    public function syncAllActiveOrders()
    {
        $orders = Order::whereNotNull('ghn_order_code')
            ->whereNotIn('order_status', ['delivered', 'cancelled', 'returned'])
            ->get();

        $results = [
            'total' => $orders->count(),
            'success' => 0,
            'failed' => 0,
        ];

        foreach ($orders as $order) {
            if ($this->syncOrderStatus($order)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }

            // Delay để tránh rate limit
            usleep(200000); // 0.2 giây
        }

        return $results;
    }
}
