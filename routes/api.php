<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Models\ActivityLog;

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
    | PROFILE
    */
    Route::get('/profile', function () {
        return response()->json(Auth::guard('api')->user());
    });

    /*
    |--------------------------------------------------------------------------
    | ACTIVITY LOG (SEMUA USER LOGIN BISA LIHAT)
    |--------------------------------------------------------------------------
    */

    Route::middleware('auth:api')->group(function () {

     // USER → lihat log sendiri
    Route::get('/my-activity-logs', [ActivityLogController::class, 'myLogs']);

    // ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index']);
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show']);
        Route::delete('/activity-logs/{id}', [ActivityLogController::class, 'destroy']);
    });

});


    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // Categories (FULL CRUD)
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{id}', [CategoryController::class, 'show']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Products (FULL CRUD)
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Update order (pengembalian)
        Route::put('/orders/{id}', [OrderController::class, 'update']);
    });

    /*
    |--------------------------------------------------------------------------
    | USER ONLY
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:user')->group(function () {


     // USER
    Route::middleware('role:user')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
        Route::post('/orders/{id}/checkout', [OrderController::class, 'checkout']);
    });

    // ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])
        ->middleware(['auth:api', 'role:admin']);

    });

// ORDER ITEMS
    Route::get('/order-items', [OrderItemController::class, 'index']);        // READ ALL (admin)
    Route::get('/order-items/{id}', [OrderItemController::class, 'show']);   // READ ONE
    Route::post('/order-items', [OrderItemController::class, 'store']);      // CREATE
    Route::put('/order-items/{id}', [OrderItemController::class, 'update']); // UPDATE
    Route::delete('/order-items/{id}', [OrderItemController::class, 'destroy']); // DELETE

        // Checkout
        Route::post('/orders/{id}/checkout', [OrderController::class, 'checkout']);
    });

});
