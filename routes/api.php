<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ActivityLogController;

/*
|--------------------------------------------------------------------------
| AUTH (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| AUTHENTICATED (JWT REQUIRED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', function () {
        return response()->json(Auth::guard('api')->user());
    });

    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOG
    |--------------------------------------------------------------------------
    */
    Route::get('/my-activity-logs', [ActivityLogController::class, 'myLogs']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show']);
        Route::delete('/activity-logs/{id}', [ActivityLogController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // Categories
        Route::apiResource('categories', CategoryController::class);

        // Products
        Route::apiResource('products', ProductController::class);

        // Orders (admin view & update)
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);

        // ⚠️ HAPUS kalau destroy() belum ada
        // Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | USER ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')->group(function () {

        // Orders
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::post('/orders/{id}/checkout', [OrderController::class, 'checkout']);

        // Order Items
        Route::get('/order-items', [OrderItemController::class, 'index']);
        Route::get('/order-items/{id}', [OrderItemController::class, 'show']);
        Route::post('/order-items', [OrderItemController::class, 'store']);
        Route::put('/order-items/{id}', [OrderItemController::class, 'update']);
        Route::delete('/order-items/{id}', [OrderItemController::class, 'destroy']);
    });
});