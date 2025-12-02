<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hủy các đơn hàng VNPay quá hạn thanh toán và hoàn lại stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Đang kiểm tra đơn hàng quá hạn thanh toán...');

        // Lấy đơn VNPay chưa thanh toán, đang pending
        $expiredOrders = Order::where('payment_method', 'vnpay')
            ->where('payment_status', 'unpaid')
            ->where('order_status', 'pending')
            ->where('payment_expires_at', '<', Carbon::now())
            ->with('orderDetails.productVariant')
            ->get();

        $this->info("📦 Tìm thấy {$expiredOrders->count()} đơn hàng quá hạn thanh toán");

        if ($expiredOrders->isEmpty()) {
            $this->info('✅ Không có đơn hàng nào cần hủy');
            return 0;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($expiredOrders as $order) {
            try {
                DB::beginTransaction();

                // ⭐ HOÀN LẠI STOCK
                foreach ($order->orderDetails as $detail) {
                    $variant = ProductVariant::find($detail->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $detail->quantity);

                        $this->comment("  → Hoàn lại {$detail->quantity} sản phẩm cho variant #{$variant->id}");

                        Log::info('Stock restored', [
                            'order_id' => $order->id,
                            'variant_id' => $variant->id,
                            'quantity_restored' => $detail->quantity,
                            'new_stock' => $variant->stock
                        ]);
                    }
                }

                // ✅ Hoàn lại promotion
                if ($order->promotion_id) {
                    $promotion = Promotion::find($order->promotion_id);
                    if ($promotion && $promotion->used_count > 0) {
                        $promotion->decrement('used_count');
                        $this->comment("  → Hoàn lại promotion #{$promotion->id}");
                    }
                }

                // ✅ Cập nhật trạng thái đơn hàng
                $order->update([
                    'order_status' => 'cancelled',
                    'payment_status' => 'failed',
                    'cancelled_at' => now(),
                ]);

                DB::commit();

                $successCount++;
                $this->info("✅ Đã hủy đơn hàng #{$order->id} - {$order->fullname}");

                Log::info('Auto cancelled expired order with stock restoration', [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'total_money' => $order->total_money,
                    'expired_at' => $order->payment_expires_at,
                    'items_count' => $order->orderDetails->count(),
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                $failCount++;

                $this->error("❌ Lỗi khi hủy đơn hàng #{$order->id}: " . $e->getMessage());

                Log::error('Failed to cancel expired order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->newLine();
        $this->info("📊 Kết quả:");
        $this->info("   ✅ Thành công: {$successCount}");

        if ($failCount > 0) {
            $this->error("   ❌ Thất bại: {$failCount}");
        }

        $this->info("🎉 Hoàn thành!");

        return 0;
    }
}
