<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Tạo đơn hàng mới
     */
    public function checkout(CheckoutRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Lấy giỏ hàng của user
            $cart = Cart::where('user_id', $user->id)->first();

            // Lấy các sản phẩm trong giỏ hàng
            $cartItems = CartItem::where('cart_id', $cart->id)
                ->with('productVariant.product')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng trống'
                ], 400);
            }

            // Kiểm tra tồn kho
            foreach ($cartItems as $item) {
                $variant = $item->productVariant;
                if ($variant->stock < $item->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Sản phẩm {$variant->product->title} không đủ số lượng trong kho"
                    ], 400);
                }
            }

            // Tính tổng tiền items
            $itemsTotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // Phí vận chuyển
            $shippingFee = 15000;

            // Xử lý mã giảm giá (nếu có)
            $promotionDiscount = 0;
            $promotionId = null;

            if ($request->promotion_id) {
                $promotion = Promotion::find($request->promotion_id);

                if ($promotion) {
                    // Kiểm tra promotion còn hiệu lực
                    if (!$promotion->status) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Mã giảm giá không còn hiệu lực'
                        ], 400);
                    }

                    // Kiểm tra thời gian áp dụng
                    $now = Carbon::now();
                    if ($now->lt(Carbon::parse($promotion->start_date)) || $now->gt(Carbon::parse($promotion->end_date))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Mã giảm giá đã hết hạn hoặc chưa đến thời gian áp dụng'
                        ], 400);
                    }

                    // Kiểm tra số lần sử dụng
                    if ($promotion->usage_limit && $promotion->used_count >= $promotion->usage_limit) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Mã giảm giá đã hết lượt sử dụng'
                        ], 400);
                    }

                    // Kiểm tra giá trị đơn hàng tối thiểu
                    if ($itemsTotal < $promotion->min_order_value) {
                        return response()->json([
                            'success' => false,
                            'message' => "Đơn hàng phải có giá trị tối thiểu " . number_format($promotion->min_order_value) . "₫ để áp dụng mã giảm giá"
                        ], 400);
                    }

                    // Tính giảm giá
                    if ($promotion->discount_type === 'percentage') {
                        $promotionDiscount = ($itemsTotal * $promotion->discount_value) / 100;
                    } elseif ($promotion->discount_type === 'fixed_amount') {
                        $promotionDiscount = $promotion->discount_value;
                    } elseif ($promotion->discount_type === 'free_shipping') {
                        $shippingFee = 0; // Miễn phí vận chuyển
                    }

                    $promotionId = $promotion->id;

                    // Tăng số lần sử dụng
                    $promotion->increment('used_count');
                }
            }

            // Tổng tiền cuối cùng
            $totalMoney = $itemsTotal + $shippingFee - $promotionDiscount;

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'promotion_id' => $promotionId,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'text_note' => $request->text_note,
                'order_status' => 'pending',
                'shipping_status' => 'pending',
                'items_total' => $itemsTotal,
                'shipping_fee' => $shippingFee,
                'promotion_discount' => $promotionDiscount,
                'total_money' => $totalMoney,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'is_custom_order' => false,
            ]);

            // Tạo chi tiết đơn hàng
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->price * $item->quantity,
                ]);

                // Giảm số lượng tồn kho
                $variant = ProductVariant::find($item->product_variant_id);
                $variant->decrement('stock', $item->quantity);
            }

            // Xóa các item trong giỏ hàng
            CartItem::where('cart_id', $cart->id)->delete();

            DB::commit();

            // Nếu thanh toán online, tạo link thanh toán
            // if ($request->payment_method === 'momo') {
            //     // TODO: Tích hợp MoMo API
            //     return response()->json([
            //         'success' => true,
            //         'message' => 'Đơn hàng đã được tạo',
            //         'data' => [
            //             'order' => $order->load('orderDetails.productVariant.product'),
            //             'payment_url' => null // URL thanh toán MoMo
            //         ]
            //     ], 201);
            // }

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order' => $order->load('orderDetails.productVariant.product')
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đặt hàng thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách đơn hàng của user
     */
    public function getOrders(Request $request)
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with(['orderDetails.productVariant.product', 'promotion'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Lấy chi tiết đơn hàng
     */
    public function getOrderDetail($id)
    {
        $user = Auth::user();

        $order = Order::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['orderDetails.productVariant.product', 'promotion'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Hủy đơn hàng
     */
    public function cancelOrder($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Chỉ cho phép hủy đơn hàng pending hoặc confirmed
            if (!in_array($order->order_status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng này'
                ], 400);
            }

            // Hoàn lại số lượng tồn kho
            foreach ($order->orderDetails as $detail) {
                $variant = ProductVariant::find($detail->product_variant_id);
                $variant->increment('stock', $detail->quantity);
            }

            // Hoàn lại số lần sử dụng mã giảm giá (nếu có)
            if ($order->promotion_id) {
                $promotion = Promotion::find($order->promotion_id);
                if ($promotion) {
                    $promotion->decrement('used_count');
                }
            }

            // Cập nhật trạng thái
            $order->update([
                'order_status' => 'cancelled',
                'cancelled_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Hủy đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Hủy đơn hàng thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
