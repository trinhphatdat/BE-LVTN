<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics(Request $request)
    {
        try {
            // ✅ Tổng doanh thu THỰC TẾ (sau khi trừ hoàn trả)
            $totalRevenue = Order::where('order_status', 'delivered')
                ->where('payment_status', 'paid')
                ->sum('total_money');

            $totalRefunded = Order::where('order_status', 'delivered')
                ->sum('refunded_amount');

            $actualRevenue = $totalRevenue - $totalRefunded;

            // Tổng đơn hàng
            $totalOrders = Order::count();

            // Tổng sản phẩm
            $totalProducts = Product::count();

            // Tổng khách hàng
            $totalCustomers = User::where('role_id', 2)->count();

            // Đơn hàng chờ xử lý
            $pendingOrders = Order::where('order_status', 'pending')->count();

            // Sản phẩm sắp hết hàng (< 10)
            $lowStockProducts = DB::table('product_variants')
                ->where('stock', '<', 10)
                ->count();

            // ✅ Doanh thu tháng này (thực tế)
            $currentMonthOrders = Order::where('order_status', 'delivered')
                ->where('payment_status', 'paid')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month);

            $currentMonthRevenue = $currentMonthOrders->sum('total_money');
            $currentMonthRefunded = $currentMonthOrders->sum('refunded_amount');
            $actualCurrentMonthRevenue = $currentMonthRevenue - $currentMonthRefunded;

            // ✅ Doanh thu tháng trước (thực tế)
            $lastMonthOrders = Order::where('order_status', 'delivered')
                ->where('payment_status', 'paid')
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month);

            $lastMonthRevenue = $lastMonthOrders->sum('total_money');
            $lastMonthRefunded = $lastMonthOrders->sum('refunded_amount');
            $actualLastMonthRevenue = $lastMonthRevenue - $lastMonthRefunded;

            // Tính tỷ lệ tăng trưởng
            $revenueGrowth = 0;
            if ($actualLastMonthRevenue > 0) {
                $revenueGrowth = (($actualCurrentMonthRevenue - $actualLastMonthRevenue) / $actualLastMonthRevenue) * 100;
            } elseif ($actualCurrentMonthRevenue > 0) {
                $revenueGrowth = 100;
            }

            // ✅ Thống kê đơn trả hàng
            $totalReturnRequests = ReturnRequest::count();
            $pendingReturnRequests = ReturnRequest::where('status', 'pending')->count();
            $completedReturnRequests = ReturnRequest::where('status', 'refunded')->count();
            $totalRefundedAmount = ReturnRequest::where('status', 'refunded')->sum('refund_amount');

            // ✅ Đơn hàng có hoàn trả
            $ordersWithRefunds = Order::where('refunded_amount', '>', 0)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'totalRevenue' => $totalRevenue,
                    'totalRefunded' => $totalRefunded,
                    'actualRevenue' => $actualRevenue,
                    'totalOrders' => $totalOrders,
                    'totalProducts' => $totalProducts,
                    'totalCustomers' => $totalCustomers,
                    'pendingOrders' => $pendingOrders,
                    'lowStockProducts' => $lowStockProducts,
                    'currentMonthRevenue' => $actualCurrentMonthRevenue,
                    'revenueGrowth' => round($revenueGrowth, 2),
                    'totalReturnRequests' => $totalReturnRequests,
                    'pendingReturnRequests' => $pendingReturnRequests,
                    'completedReturnRequests' => $completedReturnRequests,
                    'totalRefundedAmount' => $totalRefundedAmount,
                    'ordersWithRefunds' => $ordersWithRefunds,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Lấy doanh thu theo tháng trong năm (bao gồm gross/net revenue)
     */
    public function getMonthlyRevenue(Request $request)
    {
        try {
            $year = $request->input('year', Carbon::now()->year);

            $monthlyData = Order::where('order_status', 'delivered')
                ->where('payment_status', 'paid')
                ->whereYear('created_at', $year)
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(total_money) as gross_revenue'),
                    DB::raw('SUM(refunded_amount) as refunded_amount'),
                    DB::raw('SUM(total_money - COALESCE(refunded_amount, 0)) as net_revenue'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Tạo mảng đầy đủ 12 tháng
            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthData = $monthlyData->firstWhere('month', $i);
                $result[] = [
                    'month' => 'T' . $i,
                    'gross_revenue' => $monthData ? (float) $monthData->gross_revenue : 0,
                    'refunded_amount' => $monthData ? (float) $monthData->refunded_amount : 0,
                    'net_revenue' => $monthData ? (float) $monthData->net_revenue : 0,
                    'order_count' => $monthData ? $monthData->order_count : 0,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy doanh thu theo tháng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy top sản phẩm bán chạy
     */
    public function getTopSellingProducts(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $topProducts = DB::table('order_details')
                ->join('orders', 'order_details.order_id', '=', 'orders.id')
                ->join('product_variants', 'order_details.product_variant_id', '=', 'product_variants.id')
                ->join('products', 'product_variants.product_id', '=', 'products.id')
                ->where('orders.order_status', 'delivered')
                ->select(
                    'products.id',
                    'products.title',
                    'products.thumbnail',
                    DB::raw('SUM(order_details.quantity) as total_sold'),
                    DB::raw('SUM(order_details.total_price) as total_revenue')
                )
                ->groupBy('products.id', 'products.title', 'products.thumbnail')
                ->orderByDesc('total_sold')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $topProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy sản phẩm bán chạy',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy đơn hàng gần đây
     */
    public function getRecentOrders(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $recentOrders = Order::with(['user', 'orderDetails.productVariant.product'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $recentOrders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy đơn hàng gần đây',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê trạng thái đơn hàng
     */
    public function getOrderStatusStatistics()
    {
        try {
            $statistics = Order::select('order_status', DB::raw('count(*) as count'))
                ->groupBy('order_status')
                ->get()
                ->pluck('count', 'order_status');

            return response()->json([
                'success' => true,
                'data' => [
                    'pending' => $statistics['pending'] ?? 0,
                    'confirmed' => $statistics['confirmed'] ?? 0,
                    'processing' => $statistics['processing'] ?? 0,
                    'delivering' => $statistics['delivering'] ?? 0,
                    'delivered' => $statistics['delivered'] ?? 0,
                    'cancelled' => $statistics['cancelled'] ?? 0,
                    'returning' => $statistics['returning'] ?? 0,
                    'returned' => $statistics['returned'] ?? 0,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê trạng thái đơn hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Lấy yêu cầu trả hàng gần đây
     */
    public function getRecentReturnRequests(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);

            $recentReturns = ReturnRequest::with(['user', 'order'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $recentReturns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy yêu cầu trả hàng gần đây',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
