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
use App\Http\Controllers\OrderController;

//API authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/profile', [AuthController::class, 'profile']);

//API dành cho quyền admin
Route::prefix('admin')->middleware([/*'auth:api', /*'is_admin'*/])->group(function () {

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

    //product
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    // Route::get('products/all', [ProductController::class, 'all']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
});

// Client Product
Route::prefix('client')->group(function () {
    Route::get('/products/search', [ClientProductController::class, 'search']);
    Route::get('products', [ClientProductController::class, 'index']);
    Route::get('products/{id}', [ClientProductController::class, 'show']);
});

// Thao tác với products phía client
Route::get('products/category/{categoryId}', [ProductController::class, 'getByCategory']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::get('/products', [ProductController::class, 'getProductsByPriceRange']);
// Route::apiResource('products', ProductController::class);

// Update profile
Route::middleware('auth:api')->put('/edit-user', [ProfileController::class, 'update']);

// Cart
Route::middleware('auth:api')->get('/cart', [CartController::class, 'getCartByUser']);

// Cart Items
Route::apiResource('cartItems', CartItemController::class);
Route::middleware('auth:api')->patch('/cartItems/increment/{id}', [CartItemController::class, 'increment']);
Route::middleware('auth:api')->patch('/cartItems/decrement/{id}', [CartItemController::class, 'decrement']);

// Orders
// Route::middleware('auth:sanctum')->group(function () {
Route::post('/orders/checkout', [OrderController::class, 'checkout']);
Route::get('/orders', [OrderController::class, 'getOrders']);
Route::get('/orders/{id}', [OrderController::class, 'getOrderDetail']);
Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelOrder']);
// });
