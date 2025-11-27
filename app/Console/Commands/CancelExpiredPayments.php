<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
    protected $description = 'Hủy các đơn hàng quá hạn thanh toán';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredOrders = Order::where('payment_status', 'unpaid')
            ->where('order_status', '!=', 'cancelled')
            ->where('payment_expires_at', '<', Carbon::now())
            ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            try {
                DB::beginTransaction();

                // Hoàn lại tồn kho
                foreach ($order->orderDetails as $detail) {
                    $variant = ProductVariant::find($detail->product_variant_id);
                    $variant->increment('stock', $detail->quantity);
                }

                // Hoàn lại promotion
                if ($order->promotion_id) {
                    $promotion = Promotion::find($order->promotion_id);
                    if ($promotion) {
                        $promotion->decrement('used_count');
                    }
                }

                // Hủy đơn hàng
                $order->update([
                    'order_status' => 'cancelled',
                    'cancelled_at' => now()
                ]);

                DB::commit();
                $count++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error cancelling order #{$order->id}: " . $e->getMessage());
            }
        }

        $this->info("Đã hủy {$count} đơn hàng quá hạn thanh toán.");
    }
}
