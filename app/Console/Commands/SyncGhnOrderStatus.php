<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\GhnService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncGhnOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync-ghn-status {--order_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đồng bộ trạng thái đơn hàng từ Giao Hàng Nhanh';

    private $ghnService;

    public function __construct(GhnService $ghnService)
    {
        parent::__construct();
        $this->ghnService = $ghnService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Bắt đầu đồng bộ trạng thái đơn hàng từ GHN...');

        // Nếu có order_id cụ thể
        if ($this->option('order_id')) {
            $order = Order::find($this->option('order_id'));
            if ($order && $order->ghn_order_code) {
                $this->syncOrder($order);
            } else {
                $this->error('❌ Không tìm thấy đơn hàng hoặc đơn hàng chưa có mã GHN');
            }
            return 0;
        }

        // Lấy tất cả đơn hàng có mã GHN và chưa hoàn thành
        $orders = Order::whereNotNull('ghn_order_code')
            ->whereNotIn('order_status', ['delivered', 'cancelled', 'returned'])
            ->get();

        $this->info("📦 Tìm thấy {$orders->count()} đơn hàng cần đồng bộ");

        if ($orders->isEmpty()) {
            $this->info('✅ Không có đơn hàng nào cần đồng bộ');
            return 0;
        }

        $successCount = 0;
        $failCount = 0;
        $unchangedCount = 0;

        foreach ($orders as $order) {
            $result = $this->syncOrder($order);

            if ($result === 'success') {
                $successCount++;
            } elseif ($result === 'unchanged') {
                $unchangedCount++;
            } else {
                $failCount++;
            }

            // Delay để tránh rate limit
            usleep(200000); // 0.2 giây
        }

        $this->newLine();
        $this->info("📊 Kết quả đồng bộ:");
        $this->info("   ✅ Cập nhật thành công: {$successCount}");
        $this->info("   ℹ️  Không thay đổi: {$unchangedCount}");

        if ($failCount > 0) {
            $this->error("   ❌ Thất bại: {$failCount}");
        }

        $this->info("🎉 Hoàn thành đồng bộ!");

        return 0;
    }

    private function syncOrder(Order $order)
    {
        try {
            $this->comment("🔍 Đồng bộ đơn hàng #{$order->id} - GHN: {$order->ghn_order_code}");

            $response = $this->ghnService->getOrderDetail($order->ghn_order_code);

            if (!$response->successful() || $response->json()['code'] !== 200) {
                $this->error("   ❌ Không thể lấy thông tin từ GHN");
                Log::error('Failed to get GHN order detail', [
                    'order_id' => $order->id,
                    'ghn_order_code' => $order->ghn_order_code,
                    'response' => $response->json()
                ]);
                return 'failed';
            }

            $ghnData = $response->json()['data'];
            $oldStatus = $order->order_status;
            $newGhnStatus = $ghnData['status'];

            DB::beginTransaction();

            $updateData = [
                'ghn_status' => $newGhnStatus,
                'ghn_sort_code' => $ghnData['sort_code'] ?? $order->ghn_sort_code,
                'ghn_expected_delivery_time' => $ghnData['expected_delivery_time'] ?? $order->ghn_expected_delivery_time,
                'ghn_cod_amount' => $ghnData['cod_amount'] ?? $order->ghn_cod_amount,
                'ghn_note' => $ghnData['note'] ?? $order->ghn_note,
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

            // ⭐ Mapping trạng thái GHN sang order_status
            $newOrderStatus = Order::mapGhnStatusToOrderStatus($newGhnStatus);

            if ($newOrderStatus !== $oldStatus) {
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

                $this->info("   ✅ Cập nhật trạng thái: {$oldStatus} → {$newOrderStatus}");
            } else {
                $this->comment("   ℹ️  Trạng thái không đổi: {$oldStatus}");
            }

            $order->update($updateData);

            Log::info('Synced order status from GHN', [
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newOrderStatus,
                'ghn_status' => $newGhnStatus,
                'ghn_status_text' => $updateData['ghn_status_text'] ?? null,
            ]);

            DB::commit();

            return $newOrderStatus !== $oldStatus ? 'success' : 'unchanged';
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("   ❌ Lỗi: " . $e->getMessage());

            Log::error('Failed to sync GHN order status', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 'failed';
        }
    }
}
