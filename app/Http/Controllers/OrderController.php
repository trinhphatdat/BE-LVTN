<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Services\GhnService;
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

            // Tính phí vận chuyển từ GHN
            $shippingFee = 15000; // Mặc định

            if ($request->district_id && $request->ward_id) {
                try {
                    $ghnService = app(GhnService::class);
                    $response = $ghnService->calculateShippingFee([
                        'to_district_id' => $request->district_id,
                        'to_ward_code' => $request->ward_id,
                        'insurance_value' => $itemsTotal,
                        'weight' => $cartItems->count() * 200,
                    ]);

                    if ($response->successful() && $response->json()['code'] === 200) {
                        $shippingFee = $response->json()['data']['total'];
                    }
                } catch (\Exception $e) {
                    // Giữ phí mặc định nếu có lỗi
                    \Log::error('Calculate shipping fee error: ' . $e->getMessage());
                }
            }

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
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
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

            // Load relationships để trả về
            $order->load([
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.size',
                'orderDetails.productVariant.color',
                'promotion'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công',
                'data' => [
                    'order' => $order
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
    public function getOrders()
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

    /**
     * Admin: Lấy danh sách tất cả đơn hàng
     */
    public function adminGetOrders(Request $request)
    {
        $query = Order::with([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.size',
            'orderDetails.productVariant.color',
            'promotion'
        ]);

        // Lọc theo trạng thái
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('order_status', $request->status);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('fullname', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Thêm thông tin thống kê
        $statistics = [
            'total' => Order::count(),
            'pending' => Order::where('order_status', 'pending')->count(),
            'confirmed' => Order::where('order_status', 'confirmed')->count(),
            'shipping' => Order::where('order_status', 'shipping')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $orders,
            'statistics' => $statistics
        ]);
    }

    /**
     * Admin: Lấy chi tiết đơn hàng
     */
    public function adminGetOrderDetail($id)
    {
        $order = Order::with([
            'user',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.size',
            'orderDetails.productVariant.color',
            'promotion'
        ])->find($id);

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
     * Admin: Cập nhật trạng thái đơn hàng
     */
    public function adminUpdateOrderStatus(Request $request, $id)
    {
        try {
            $request->validate(
                [
                    'order_status' => 'required|in:pending,confirmed,shipping,completed,cancelled'
                ],
                [
                    'order_status.required' => 'Trạng thái đơn hàng là bắt buộc',
                    'order_status.in' => 'Trạng thái không hợp lệ'
                ]
            );

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            DB::beginTransaction();

            // Nếu hủy đơn, hoàn lại tồn kho
            if ($request->order_status === 'cancelled' && $order->order_status !== 'cancelled') {
                foreach ($order->orderDetails as $detail) {
                    $variant = ProductVariant::find($detail->product_variant_id);
                    $variant->increment('stock', $detail->quantity);
                }

                // Hoàn lại số lần sử dụng mã giảm giá
                if ($order->promotion_id) {
                    $promotion = Promotion::find($order->promotion_id);
                    if ($promotion) {
                        $promotion->decrement('used_count');
                    }
                }

                $order->cancelled_at = now();
            }

            // Cập nhật trạng thái shipping tương ứng
            $shippingStatus = match ($request->order_status) {
                'pending' => 'pending',
                'confirmed' => 'preparing',
                'shipping' => 'shipping',
                'completed' => 'delivered',
                'cancelled' => 'failed',
                default => $order->shipping_status
            };

            if ($request->order_status === 'completed') {
                $order->payment_status = 'paid';
                $order->paid_at = now();
                $order->shipped_at = now();
                $order->completed_at = now();
            }
            if ($request->order_status === 'cancelled') {
                $order->cancelled_at = now();
            }

            $order->update([
                'order_status' => $request->order_status,
                'shipping_status' => $shippingStatus
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'data' => $order->load(['orderDetails.productVariant.product', 'promotion'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật trạng thái thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Xóa đơn hàng
     */
    public function adminDeleteOrder($id)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Chỉ cho phép xóa đơn đã hủy hoặc hoàn thành
            if (!in_array($order->order_status, ['cancelled', 'completed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể xóa đơn hàng đã hủy hoặc hoàn thành'
                ], 400);
            }

            // Xóa chi tiết đơn hàng
            OrderDetail::where('order_id', $order->id)->delete();

            // Xóa đơn hàng
            $order->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Xóa đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xóa đơn hàng thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
