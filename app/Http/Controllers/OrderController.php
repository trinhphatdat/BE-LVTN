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

class OrderController extends Controller
{
    /**
     * ✅ Lấy danh sách đơn hàng của user hiện tại
     */
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

    /**
     * ✅ Lấy chi tiết đơn hàng của user
     */
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

    /**
     * ✅ Thanh toán lại đơn hàng VNPay chưa thanh toán
     */
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

    /**
     * ✅ Method riêng để tạo đơn GHN
     */
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
                    'length' => 20,
                    'width' => 20,
                    'height' => 5,
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
                'cod_amount' => $order->payment_method === 'cod' ? (int)$order->total_money : 0,
                'content' => 'Thời trang',
                'weight' => count($ghnItems) * 200,
                'length' => 30,
                'width' => 30,
                'height' => 20,
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

                \Log::info('Created GHN order successfully', [
                    'order_id' => $order->id,
                    'ghn_order_code' => $ghnData['order_code']
                ]);

                return true;
            } else {
                \Log::error('Failed to create GHN order', [
                    'order_id' => $order->id,
                    'response' => $ghnResponse->json()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('GHN API Error: ' . $e->getMessage(), [
                'order_id' => $order->id
            ]);
            return false;
        }
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
                        \Log::warning('Failed to cancel GHN order', [
                            'order_id' => $order->id,
                            'ghn_order_code' => $order->ghn_order_code,
                            'response' => $ghnResponse->json()
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('GHN cancel error: ' . $e->getMessage());
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

    /**
     * Admin: Lấy danh sách tất cả đơn hàng
     */
    public function adminGetOrders(Request $request)
    {
        try {
            $query = Order::with([
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.size',
                'orderDetails.productVariant.color',
                'promotion',
                'user'
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
                        ->orWhere('ghn_order_code', 'like', "%{$search}%");
                });
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            // Tính thống kê
            $statistics = [
                'total' => Order::count(),
                'pending' => Order::where('order_status', 'pending')->count(),
                'confirmed' => Order::where('order_status', 'confirmed')->count(),
                'processing' => Order::where('order_status', 'processing')->count(),
                'delivering' => Order::where('order_status', 'delivering')->count(),
                'delivered' => Order::where('order_status', 'delivered')->count(),
                'cancelled' => Order::where('order_status', 'cancelled')->count(),
                'returning' => Order::where('order_status', 'returning')->count(),
                'returned' => Order::where('order_status', 'returned')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $orders,
                'statistics' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tải danh sách đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
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
    /**
     * Admin: Đồng bộ trạng thái đơn hàng từ GHN (thủ công)
     */
    public function adminSyncGhnStatus($id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy đơn hàng'
                ], 404);
            }

            if (!$order->ghn_order_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng chưa có mã GHN'
                ], 400);
            }

            $ghnSyncService = app(GhnSyncService::class);
            $result = $ghnSyncService->syncOrderStatus($order);

            if ($result) {
                $order->refresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Đồng bộ trạng thái thành công',
                    'data' => $order
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể đồng bộ trạng thái từ GHN'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin: Đồng bộ tất cả đơn hàng đang active
     */
    public function adminSyncAllGhnStatus()
    {
        try {
            $ghnSyncService = app(GhnSyncService::class);
            $results = $ghnSyncService->syncAllActiveOrders();

            return response()->json([
                'success' => true,
                'message' => 'Đồng bộ hoàn tất',
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
