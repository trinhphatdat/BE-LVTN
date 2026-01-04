<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientProductController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SizeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\ClientOrderController;
use App\Http\Controllers\AdminOrderController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReturnRequestController;
use App\Http\Controllers\Api\ConsultationController;

//API authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/profile', [AuthController::class, 'profile']);

//API dành cho quyền admin
Route::prefix('admin')/*->middleware(['is_admin'])*/->group(function () {

    Route::get('/roles', [RoleController::class, 'index']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::get('/roles/{id}', [RoleController::class, 'show']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

    Route::apiResource('sizes', SizeController::class);

    Route::apiResource('colors', ColorController::class);

    Route::apiResource('brands', BrandController::class);

    Route::apiResource('promotions', PromotionController::class);

    Route::apiResource('users', UserController::class);

    // Products
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    // Route::get('products/all', [ProductController::class, 'all']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);

    // Orders 
    Route::get('orders', [AdminOrderController::class, 'adminGetOrders']);
    Route::get('orders/{id}', [AdminOrderController::class, 'adminGetOrderDetail']);
    Route::put('orders/{id}/status', [AdminOrderController::class, 'adminUpdateOrderStatus']);
    Route::delete('orders/{id}', [AdminOrderController::class, 'adminDeleteOrder']);

    // Đồng bộ trạng thái GHN
    Route::post('/orders/{id}/sync-ghn', [AdminOrderController::class, 'adminSyncGhnStatus']);
    Route::post('/orders/sync-all-ghn', [AdminOrderController::class, 'adminSyncAllGhnStatus']);

    // Dashboard
    Route::get('dashboard/statistics', [DashboardController::class, 'getStatistics']);
    Route::get('dashboard/monthly-revenue', [DashboardController::class, 'getMonthlyRevenue']);
    Route::get('dashboard/top-selling-products', [DashboardController::class, 'getTopSellingProducts']);
    Route::get('dashboard/recent-orders', [DashboardController::class, 'getRecentOrders']);
    Route::get('dashboard/order-status-statistics', [DashboardController::class, 'getOrderStatusStatistics']);
    Route::get('dashboard/low-stock-products', [DashboardController::class, 'getLowStockProducts']);

    // Yêu cầu trả hàng
    Route::get('return-requests', [ReturnRequestController::class, 'adminGetRequests']);
    Route::get('return-requests/{id}', [ReturnRequestController::class, 'adminGetRequestDetail']);
    Route::post('return-requests/{id}/approve', [ReturnRequestController::class, 'adminApproveRequest']);
    Route::post('return-requests/{id}/reject', [ReturnRequestController::class, 'adminRejectRequest']);
    Route::post('return-requests/{id}/received', [ReturnRequestController::class, 'adminConfirmReceived']);
    Route::post('return-requests/{id}/refund', [ReturnRequestController::class, 'adminRefund']);

    // Route::get('/consultations', [ConsultationController::class, 'adminIndex']);
    // Route::post('/consultations/{id}/answer', [ConsultationController::class, 'answer']);
});

// Client Product
Route::prefix('client')->group(function () {
    Route::get('products/all-shirts', [ClientProductController::class, 'getAllShirts']);

    Route::get('/products/search', [ClientProductController::class, 'search']);
    Route::get('products', [ClientProductController::class, 'index']);
    Route::get('products/{id}', [ClientProductController::class, 'show']);

    Route::get('promotions', [PromotionController::class, 'index']);
});

//Comment sản phẩm
Route::get('productReviews/{productId}', [ProductReviewController::class, 'show']);
Route::post('productReviews', [ProductReviewController::class, 'store']);
Route::put('productReviews/{id}', [ProductReviewController::class, 'update']);
Route::delete('productReviews/{id}', [ProductReviewController::class, 'destroy']);

// Thao tác với products phía client
Route::get('products/category/{categoryId}', [ProductController::class, 'getByCategory']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('/products', [ProductController::class, 'getProductsByPriceRange']);
// Route::apiResource('products', ProductController::class);

// Update profile
Route::put('/edit-user', [ProfileController::class, 'update']);

// Cart
Route::get('/cart', [CartController::class, 'getCartByUser']);

// Cart Items
Route::apiResource('cartItems', CartItemController::class);
Route::patch('/cartItems/increment/{id}', [CartItemController::class, 'increment']);
Route::patch('/cartItems/decrement/{id}', [CartItemController::class, 'decrement']);
Route::post('/cartItems/couple', [CartItemController::class, 'storeCouple']);

// Check khuyến mãi
Route::post('/promotions/check', [PromotionController::class, 'checkPromotionCode']);

// Orders
Route::post('/orders/checkout', [ClientOrderController::class, 'checkout']);
Route::get('/orders', [ClientOrderController::class, 'getUserOrders']);
Route::get('/orders/{id}', [ClientOrderController::class, 'getUserOrderDetail']);
Route::post('/orders/{id}/cancel', [ClientOrderController::class, 'cancelOrder']);
Route::post('/orders/{id}/retry-payment', [ClientOrderController::class, 'retryPayment']);

// Lấy api địa chỉ
Route::get('/provinces', [LocationController::class, 'getProvinces']);
Route::get('/districts', [LocationController::class, 'getDistricts']);
Route::get('/wards', [LocationController::class, 'getWards']);
Route::post('/calculate-shipping-fee', [LocationController::class, 'calculateShippingFee']);

// Thanh toán VNPay
Route::post('/vnpay/payment', [PaymentController::class, 'vnpay_payment']);
Route::get('/vnpay/callback', [PaymentController::class, 'vnpay_callback']);

// Yêu cầu trả hàng
Route::post('/return-requests', [ReturnRequestController::class, 'store']);
Route::get('/return-requests', [ReturnRequestController::class, 'getUserRequests']);
Route::get('/return-requests/check/{orderId}', [ReturnRequestController::class, 'checkOrderHasReturnRequest']);

// // Routes cho khách hàng (consultations)
// Route::get('/consultations', [ConsultationController::class, 'index']);
// Route::post('/consultations', [ConsultationController::class, 'store']);
// Route::get('/consultations/{id}', [ConsultationController::class, 'show']);
// Route::post('/consultations/{id}/close', [ConsultationController::class, 'close']);
