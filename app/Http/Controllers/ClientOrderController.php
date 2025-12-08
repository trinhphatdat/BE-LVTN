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
use App\Services\GhnSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientOrderController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->first();
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
                        'message' => "Sản phẩm {$variant->product->title} chỉ còn {$variant->stock} sản phẩm"
                    ], 400);
                }
            }

            $itemsTotal = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // Tính phí ship
            $shippingFee = 15000;
            if ($request->district_id && $request->ward_id) {
                try {
                    $ghnService = app(GhnService::class);
                    $response = $ghnService->calculateShippingFee([
                        'to_district_id' => $request->district_id,
                        'to_ward_code' => $request->ward_id,
                        'insurance_value' => $itemsTotal,
                        'weight' => $cartItems->sum('quantity') * 200,
                    ]);

                    if ($response->successful() && $response->json()['code'] === 200) {
                        $shippingFee = $response->json()['data']['total'];
                    }
                } catch (\Exception $e) {
                    // \Log::error('Calculate shipping fee error: ' . $e->getMessage());
                }
            }

            // Xử lý promotion
            $promotionDiscount = 0;
            $promotionId = null;
            $shippingDiscount = 0;

            if ($request->promotion_id) {
                $promotion = Promotion::find($request->promotion_id);

                if ($promotion) {
                    if ($promotion->discount_type === 'percentage') {
                        $promotionDiscount = ($itemsTotal * $promotion->discount_value) / 100;
                    } elseif ($promotion->discount_type === 'fixed_amount') {
                        $promotionDiscount = $promotion->discount_value;
                    } elseif ($promotion->discount_type === 'free_shipping') {
                        $shippingDiscount = $shippingFee;
                        $shippingFee = 0;
                    }

                    $promotionId = $promotion->id;
                }
            }

            $totalMoney = round($itemsTotal + $shippingFee - $promotionDiscount);

            if ($totalMoney <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền đơn hàng không hợp lệ'
                ], 400);
            }

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'promotion_id' => $promotionId,
                'fullname' => $request->fullname,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'text_note' => $request->text_note,
                'text_custom_couple' => $request->text_custom_couple,
                'order_status' => 'pending',
                'items_total' => round($itemsTotal),
                'shipping_fee' => round($shippingFee),
                'shipping_discount' => round($shippingDiscount),
                'promotion_discount' => round($promotionDiscount),
                'total_money' => $totalMoney,
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
                'payment_expires_at' => Carbon::now()->addDays(2),
            ]);

            // ⭐ Tạo chi tiết đơn hàng + TRỪ STOCK NGAY (cả COD và VNPay)
            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->price * $item->quantity,
                ]);

                // TRỪ STOCK NGAY
                $variant = ProductVariant::find($item->product_variant_id);
                $variant->decrement('stock', $item->quantity);
            }

            if ($promotionId) {
                Promotion::find($promotionId)->increment('used_count');
            }

            // ✅ Nếu COD → Tạo đơn GHN ngay
            if ($request->payment_method === 'cod') {
                $this->createGhnOrder($order, $cartItems);
            }

            // Xóa giỏ hàng
            CartItem::where('cart_id', $cart->id)->delete();

            DB::commit();

            $order->load([
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.size',
                'orderDetails.productVariant.color',
                'promotion'
            ]);

            // ✅ Nếu VNPay → Chuyển đến trang thanh toán
            if ($request->payment_method === 'vnpay') {
                $paymentController = new PaymentController();
                $paymentRequest = new Request([
                    'order_id' => $order->id,
                    'bankCode' => $request->bankCode ?? ''
                ]);

                $paymentResponse = $paymentController->vnpay_payment($paymentRequest);
                $paymentData = $paymentResponse->getData();

                return response()->json([
                    'success' => true,
                    'message' => 'Đặt hàng thành công. Vui lòng thanh toán trong vòng 2 ngày',
                    'data' => [
                        'order' => $order,
                        'payment_url' => $paymentData->data->payment_url
                    ]
                ], 201);
            }

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
    //Lấy danh sách đơn hàng của user hiện tại
    public function getUserOrders(Request $request)
    {
        try {
            $user = Auth::user();

            $query = Order::where('user_id', $user->id)
                ->with([
                    'orderDetails.productVariant.product',
                    'orderDetails.productVariant.size',
                    'orderDetails.productVariant.color',
                    'promotion'
                ]);

            // Lọc theo trạng thái nếu có
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('order_status', $request->status);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Lấy chi tiết đơn hàng của user
    public function getUserOrderDetail($id)
    {
        try {
            $user = Auth::user();

            $order = Order::where('user_id', $user->id)
                ->where('id', $id)
                ->with([
                    'orderDetails.productVariant.product',
                    'orderDetails.productVariant.size',
                    'orderDetails.productVariant.color',
                    'promotion'
                ])
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải chi tiết đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Thanh toán lại đơn hàng VNPay chưa thanh toán
    public function retryPayment($id)
    {
        try {
            $user = Auth::user();

            $order = Order::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            // Kiểm tra điều kiện thanh toán lại
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã được thanh toán'
                ], 400);
            }

            if ($order->order_status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã bị hủy'
                ], 400);
            }

            if ($order->payment_method !== 'vnpay') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ áp dụng cho đơn hàng thanh toán qua VNPay'
                ], 400);
            }

            // Kiểm tra hết hạn
            if ($order->payment_expires_at && Carbon::now()->gt($order->payment_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã hết hạn thanh toán'
                ], 400);
            }

            // Tạo link thanh toán mới
            $paymentController = new PaymentController();
            $paymentRequest = new Request([
                'order_id' => $order->id,
                'bankCode' => ''
            ]);

            $paymentResponse = $paymentController->vnpay_payment($paymentRequest);
            $paymentData = $paymentResponse->getData();

            return response()->json([
                'success' => true,
                'message' => 'Tạo link thanh toán thành công',
                'data' => [
                    'payment_url' => $paymentData->data->payment_url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo link thanh toán',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //Method riêng để tạo đơn GHN
    private function createGhnOrder(Order $order, $cartItems)
    {
        try {
            $ghnService = app(GhnService::class);

            // Chuẩn bị items cho GHN
            $ghnItems = [];
            foreach ($cartItems as $item) {
                $ghnItems[] = [
                    'name' => $item->productVariant->product->title,
                    'code' => $item->productVariant->sku ?? '',
                    'quantity' => $item->quantity,
                    'price' => (int)$item->price,
                    'length' => 40,
                    'width' => 28,
                    'height' => 1,
                    'weight' => 200,
                ];
            }

            $ghnOrderData = [
                'client_order_code' => 'ORD' . $order->id,
                'payment_type_id' => $order->payment_method === 'cod' ? 2 : 1,
                'note' => $order->text_note ?? '',
                'required_note' => 'KHONGCHOXEMHANG',
                'to_name' => $order->fullname,
                'to_phone' => $order->phone_number,
                'to_address' => $order->address,
                'to_ward_code' => $order->ward_id,
                'to_district_id' => $order->district_id,
                'cod_amount' => $order->payment_method === 'cod' ? (int)($order->total_money - $order->shipping_fee - $order->shipping_discount) : 0,
                'content' => 'Thời trang',
                'weight' => count($ghnItems) * 200,
                'length' => 40,
                'width' => 28,
                'height' => count($ghnItems) * 1,
                'insurance_value' => (int)$order->items_total,
                'service_type_id' => 2,
                'items' => $ghnItems,
            ];

            $ghnResponse = $ghnService->createOrder($ghnOrderData);

            if ($ghnResponse->successful() && $ghnResponse->json()['code'] === 200) {
                $ghnData = $ghnResponse->json()['data'];

                $order->update([
                    'ghn_order_code' => $ghnData['order_code'],
                    'ghn_sort_code' => $ghnData['sort_code'] ?? null,
                    'ghn_expected_delivery_time' => $ghnData['expected_delivery_time'] ?? null,
                    'ghn_total_fee' => $ghnData['total_fee'] ?? $order->shipping_fee,
                    'order_status' => 'confirmed',
                ]);

                // \Log::info('Created GHN order successfully', [
                //     'order_id' => $order->id,
                //     'ghn_order_code' => $ghnData['order_code']
                // ]);

                return true;
            } else {
                // \Log::error('Failed to create GHN order', [
                //     'order_id' => $order->id,
                //     'response' => $ghnResponse->json()
                // ]);
                return false;
            }
        } catch (\Exception $e) {
            // \Log::error('GHN API Error: ' . $e->getMessage(), [
            //     'order_id' => $order->id
            // ]);
            return false;
        }
    }

    //Hủy đơn hàng
    public function cancelOrder($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->with('orderDetails.productVariant')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            if (!in_array($order->order_status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể hủy đơn hàng này'
                ], 400);
            }

            // ✅ Hủy đơn hàng trên GHN (nếu có)
            if ($order->ghn_order_code) {
                try {
                    $ghnService = app(GhnService::class);
                    $ghnResponse = $ghnService->cancelOrder([$order->ghn_order_code]);

                    if (!$ghnResponse->successful()) {
                        // \Log::warning('Failed to cancel GHN order', [
                        //     'order_id' => $order->id,
                        //     'ghn_order_code' => $order->ghn_order_code,
                        //     'response' => $ghnResponse->json()
                        // ]);
                    }
                } catch (\Exception $e) {
                    // \Log::error('GHN cancel error: ' . $e->getMessage());
                }
            }

            // ⭐ Hoàn lại số lượng tồn kho
            foreach ($order->orderDetails as $detail) {
                $variant = ProductVariant::find($detail->product_variant_id);
                if ($variant) {
                    $variant->increment('stock', $detail->quantity);
                }
            }

            // Hoàn lại promotion
            if ($order->promotion_id) {
                $promotion = Promotion::find($order->promotion_id);
                if ($promotion) {
                    $promotion->decrement('used_count');
                }
            }

            $order->update([
                'order_status' => 'cancelled',
                'cancelled_at' => now(),
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
