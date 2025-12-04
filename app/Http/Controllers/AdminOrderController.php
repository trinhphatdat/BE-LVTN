<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\Promotion;
use App\Services\GhnService;
use App\Services\GhnSyncService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminOrderController extends Controller
{
    // Lấy danh sách tất cả đơn hàng
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

    //Lấy chi tiết đơn hàng
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

    //Cập nhật trạng thái đơn hàng

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

    //Xóa đơn hàng
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
    //Đồng bộ trạng thái đơn hàng từ GHN (thủ công)

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

    //Đồng bộ tất cả đơn hàng đang active
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
