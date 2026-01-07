<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\OrderDetail;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Xử lý date range từ request
     */
    private function getDateRange(Request $request)
    {
        $filterType = $request->input('filter_type', 'all'); // all, today, yesterday, this_week, last_week, this_month, last_month, custom
        $startDate = null;
        $endDate = null;

        switch ($filterType) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;

            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;

            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;

            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;

            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;

            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;

            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
                break;

            case 'all':
            default:
                // Không lọc theo thời gian
                break;
        }

        return [
            'filter_type' => $filterType,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Lấy thống kê tổng quan
     */
    public function getStatistics(Request $request)
    {
        try {
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            //  Tổng doanh thu THỰC TẾ (sau khi trừ hoàn trả)
            $totalRevenueQuery = Order::where('order_status', 'delivered')
                ->where('payment_status', 'paid');
            $totalRevenueQuery = $this->applyDateFilter($totalRevenueQuery, $startDate, $endDate);
            $totalRevenue = $totalRevenueQuery->sum('total_money');

            $totalRefundedQuery = Order::where('order_status', 'delivered');
            $totalRefundedQuery = $this->applyDateFilter($totalRefundedQuery, $startDate, $endDate);
            $totalRefunded = $totalRefundedQuery->sum('refunded_amount');

            $actualRevenue = $totalRevenue - $totalRefunded;

            // Tổng đơn hàng
            $totalOrdersQuery = Order::query();
            $totalOrdersQuery = $this->applyDateFilter($totalOrdersQuery, $startDate, $endDate);
            $totalOrders = $totalOrdersQuery->count();

            // Tổng sản phẩm (không lọc theo thời gian)
            $totalProducts = Product::count();

            // Tổng khách hàng (có thể lọc theo thời gian đăng ký)
            $totalCustomersQuery = User::where('role_id', 2);
            $totalCustomersQuery = $this->applyDateFilter($totalCustomersQuery, $startDate, $endDate);
            $totalCustomers = $totalCustomersQuery->count();

            // Đơn hàng chờ xử lý
            $pendingOrdersQuery = Order::where('order_status', 'pending');
            $pendingOrdersQuery = $this->applyDateFilter($pendingOrdersQuery, $startDate, $endDate);
            $pendingOrders = $pendingOrdersQuery->count();

            // Sản phẩm sắp hết hàng (< 10) - không lọc theo thời gian
            $lowStockProducts = ProductVariant::where('stock', '<', 10)->count();

            //  Doanh thu kỳ hiện tại
            $currentPeriodRevenue = $actualRevenue;

            //  Doanh thu kỳ trước (để tính tăng trưởng)
            $previousPeriodRevenue = $this->getPreviousPeriodRevenue($dateRange);

            // Tính tỷ lệ tăng trưởng
            $revenueGrowth = 0;
            if ($previousPeriodRevenue > 0) {
                $revenueGrowth = (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100;
            } elseif ($currentPeriodRevenue > 0) {
                $revenueGrowth = 100;
            }

            //  Thống kê đơn trả hàng
            $totalReturnRequestsQuery = ReturnRequest::query();
            $totalReturnRequestsQuery = $this->applyDateFilter($totalReturnRequestsQuery, $startDate, $endDate);
            $totalReturnRequests = $totalReturnRequestsQuery->count();

            $pendingReturnRequestsQuery = ReturnRequest::where('status', 'pending');
            $pendingReturnRequestsQuery = $this->applyDateFilter($pendingReturnRequestsQuery, $startDate, $endDate);
            $pendingReturnRequests = $pendingReturnRequestsQuery->count();

            $completedReturnRequestsQuery = ReturnRequest::where('status', 'refunded');
            $completedReturnRequestsQuery = $this->applyDateFilter($completedReturnRequestsQuery, $startDate, $endDate);
            $completedReturnRequests = $completedReturnRequestsQuery->count();

            $totalRefundedAmountQuery = ReturnRequest::where('status', 'refunded');
            $totalRefundedAmountQuery = $this->applyDateFilter($totalRefundedAmountQuery, $startDate, $endDate);
            $totalRefundedAmount = $totalRefundedAmountQuery->sum('refund_amount');

            //  Đơn hàng có hoàn trả
            $ordersWithRefundsQuery = Order::where('refunded_amount', '>', 0);
            $ordersWithRefundsQuery = $this->applyDateFilter($ordersWithRefundsQuery, $startDate, $endDate);
            $ordersWithRefunds = $ordersWithRefundsQuery->count();

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
                    'currentMonthRevenue' => $currentPeriodRevenue,
                    'revenueGrowth' => round($revenueGrowth, 2),
                    'totalReturnRequests' => $totalReturnRequests,
                    'pendingReturnRequests' => $pendingReturnRequests,
                    'completedReturnRequests' => $completedReturnRequests,
                    'totalRefundedAmount' => $totalRefundedAmount,
                    'ordersWithRefunds' => $ordersWithRefunds,
                    'filter_info' => [
                        'filter_type' => $dateRange['filter_type'],
                        'start_date' => $startDate ? $startDate->format('Y-m-d H:i:s') : null,
                        'end_date' => $endDate ? $endDate->format('Y-m-d H:i:s') : null,
                    ]
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
     * Tính doanh thu kỳ trước để so sánh
     */
    private function getPreviousPeriodRevenue($dateRange)
    {
        $filterType = $dateRange['filter_type'];
        $startDate = $dateRange['start_date'];
        $endDate = $dateRange['end_date'];

        if ($filterType === 'all' || !$startDate || !$endDate) {
            return 0;
        }

        $daysDiff = $startDate->diffInDays($endDate) + 1;
        $previousStart = $startDate->copy()->subDays($daysDiff);
        $previousEnd = $endDate->copy()->subDays($daysDiff);

        $previousRevenue = Order::where('order_status', 'delivered')
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total_money');

        $previousRefunded = Order::where('order_status', 'delivered')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('refunded_amount');

        return $previousRevenue - $previousRefunded;
    }

    /**
     *  Lấy doanh thu theo tháng trong năm (bao gồm gross/net revenue)
     */
    public function getMonthlyRevenue(Request $request)
    {
        try {
            $year = $request->input('year', Carbon::now()->year);
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            $query = Order::where('order_status', 'delivered')
                ->where('payment_status', 'paid');

            // Nếu có lọc theo thời gian, áp dụng filter
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } else {
                // Nếu không có filter, lọc theo năm
                $query->whereYear('created_at', $year);
            }

            $monthlyData = $query->selectRaw('MONTH(created_at) as month')
                ->selectRaw('SUM(total_money) as gross_revenue')
                ->selectRaw('SUM(refunded_amount) as refunded_amount')
                ->selectRaw('SUM(total_money - COALESCE(refunded_amount, 0)) as net_revenue')
                ->selectRaw('COUNT(*) as order_count')
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
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            $query = OrderDetail::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->where('order_status', 'delivered');
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            });

            $topProducts = $query->with(['productVariant.product'])
                ->selectRaw('product_variant_id, SUM(quantity) as total_sold, SUM(total_price) as total_revenue')
                ->groupBy('product_variant_id')
                ->orderByDesc('total_sold')
                ->limit($limit)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->productVariant->product->id,
                        'title' => $item->productVariant->product->title,
                        'thumbnail' => $item->productVariant->product->thumbnail,
                        'total_sold' => $item->total_sold,
                        'total_revenue' => $item->total_revenue,
                    ];
                })
                ->unique('id')
                ->values()
                ->take($limit);

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
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            $query = Order::with(['user', 'orderDetails.productVariant.product']);
            $query = $this->applyDateFilter($query, $startDate, $endDate);

            $recentOrders = $query->orderBy('created_at', 'desc')
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
    public function getOrderStatusStatistics(Request $request)
    {
        try {
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            $query = Order::query();
            $query = $this->applyDateFilter($query, $startDate, $endDate);

            $statistics = $query->selectRaw('order_status, count(*) as count')
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
     *  Lấy yêu cầu trả hàng gần đây
     */
    public function getRecentReturnRequests(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            $query = ReturnRequest::with(['user', 'order']);
            $query = $this->applyDateFilter($query, $startDate, $endDate);

            $recentReturns = $query->orderBy('created_at', 'desc')
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

    /**
     *  Lấy danh sách chi tiết sản phẩm sắp hết hàng
     */
    public function getLowStockProducts(Request $request)
    {
        try {
            $threshold = $request->input('threshold', 10);
            $limit = $request->input('limit', 50);

            $lowStockProducts = ProductVariant::with(['product', 'size', 'color'])
                ->where('stock', '<', $threshold)
                ->whereHas('product')
                ->whereHas('size')
                ->whereHas('color')
                ->orderBy('stock', 'asc')
                ->limit($limit)
                ->get()
                ->map(function ($variant) {
                    return [
                        'product_name' => $variant->product->title ?? 'N/A',
                        'product_type' => $variant->product->product_type ?? 'N/A',
                        'size' => $variant->size->name ?? 'N/A',
                        'color' => $variant->color->name ?? 'N/A',
                        'stock' => $variant->stock,
                        'price' => $variant->price,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $lowStockProducts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách sản phẩm sắp hết hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê sản phẩm theo loại (male, female, couple)
     */
    public function getProductTypeStatistics(Request $request)
    {
        try {
            $dateRange = $this->getDateRange($request);
            $startDate = $dateRange['start_date'];
            $endDate = $dateRange['end_date'];

            // Query với điều kiện thời gian
            $query = OrderDetail::whereHas('order', function ($q) use ($startDate, $endDate) {
                $q->where('order_status', 'delivered');
                if ($startDate && $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }
            });

            // Lấy thống kê theo loại sản phẩm
            $statistics = $query->with(['productVariant.product'])
                ->get()
                ->groupBy(function ($item) {
                    return $item->productVariant->product->product_type ?? 'unknown';
                })
                ->map(function ($items, $type) {
                    return [
                        'product_type' => $type,
                        'quantity_sold' => $items->sum('quantity'),
                        'total_revenue' => $items->sum('total_price'),
                        'order_count' => $items->pluck('order_id')->unique()->count(),
                    ];
                })
                ->values();

            // Đảm bảo có đầy đủ 3 loại (male, female, couple)
            $types = ['male', 'female', 'couple'];
            $result = [];

            foreach ($types as $type) {
                $stat = $statistics->firstWhere('product_type', $type);
                $result[$type] = [
                    'product_type' => $type,
                    'quantity_sold' => $stat ? $stat['quantity_sold'] : 0,
                    'total_revenue' => $stat ? $stat['total_revenue'] : 0,
                    'order_count' => $stat ? $stat['order_count'] : 0,
                ];
            }

            // Tính tổng
            $totalQuantity = array_sum(array_column($result, 'quantity_sold'));
            $totalRevenue = array_sum(array_column($result, 'total_revenue'));

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $result,
                    'summary' => [
                        'total_quantity' => $totalQuantity,
                        'total_revenue' => $totalRevenue,
                        'total_orders' => array_sum(array_column($result, 'order_count')),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê theo loại sản phẩm',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
