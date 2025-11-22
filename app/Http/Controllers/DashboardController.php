<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderDetail;
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
            // Tổng doanh thu từ đơn hàng hoàn thành
            $totalRevenue = Order::where('order_status', 'completed')
                ->sum('total_money');

            // Tổng đơn hàng
            $totalOrders = Order::count();

            // Tổng sản phẩm
            $totalProducts = Product::count();

            // Tổng khách hàng
            $totalCustomers = User::where('role_id', 2)->count(); // role_id = 2 là customer

            // Đơn hàng chờ xử lý
            $pendingOrders = Order::where('order_status', 'pending')->count();

            // Sản phẩm sắp hết hàng (< 10)
            $lowStockProducts = DB::table('product_variants')
                ->where('stock', '<', 10)
                ->count();

            // Doanh thu tháng này
            $currentMonthRevenue = Order::where('order_status', 'completed')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('total_money');

            // Doanh thu tháng trước
            $lastMonthRevenue = Order::where('order_status', 'completed')
                ->whereYear('created_at', Carbon::now()->subMonth()->year)
                ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                ->sum('total_money');

            // Tính tỷ lệ tăng trưởng
            $revenueGrowth = 0;
            if ($lastMonthRevenue > 0) {
                $revenueGrowth = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'totalRevenue' => $totalRevenue,
                    'totalOrders' => $totalOrders,
                    'totalProducts' => $totalProducts,
                    'totalCustomers' => $totalCustomers,
                    'pendingOrders' => $pendingOrders,
                    'lowStockProducts' => $lowStockProducts,
                    'currentMonthRevenue' => $currentMonthRevenue,
                    'revenueGrowth' => round($revenueGrowth, 2),
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
     * Lấy doanh thu theo tháng trong năm
     */
    public function getMonthlyRevenue(Request $request)
    {
        try {
            $year = $request->input('year', Carbon::now()->year);

            $monthlyRevenue = Order::where('order_status', 'completed')
                ->whereYear('created_at', $year)
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(total_money) as revenue'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            // Tạo mảng đầy đủ 12 tháng
            $result = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthData = $monthlyRevenue->firstWhere('month', $i);
                $result[] = [
                    'month' => 'T' . $i,
                    'revenue' => $monthData ? (float) $monthData->revenue : 0,
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
                ->where('orders.order_status', 'completed')
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
                    'shipping' => $statistics['shipping'] ?? 0,
                    'completed' => $statistics['completed'] ?? 0,
                    'cancelled' => $statistics['cancelled'] ?? 0,
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
}
