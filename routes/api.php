<?php

use App\Http\Controllers\seller\AuthController as SellerAuthController;
use App\Http\Controllers\seller\ProductController as SellerProductController;
use App\Http\Controllers\seller\OrderController as SellerOrderController;
use App\Http\Controllers\user\AuthController as UserAuthController;
use App\Http\Controllers\user\ProductController as UserProductController;
use App\Http\Controllers\user\CartController as UserCartController;
use App\Http\Controllers\user\OrderController as UserOrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/seller')->group(function() {
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::post('/login', [SellerAuthController::class, 'login']);
    
    Route::middleware(['auth:sanctum', 'seller'])->group(function() {
        Route::get('/logout', [SellerAuthController::class, 'logout']);

        Route::get('/products', [SellerProductController::class, 'index']);
        Route::get('/product/{id}', [SellerProductController::class, 'show']);
        Route::post('/product/store', [SellerProductController::class, 'store']);
        Route::put('/product/{id}', [SellerProductController::class, 'update']);
        Route::delete('/product/{id}', [SellerProductController::class, 'destroy']);
        Route::patch('/product/quantity/{id}', [SellerProductController::class, 'update_quantity']);

        Route::get('/order/resume', [SellerOrderController::class, 'resume']);
        Route::get('/order/detail/{id}', [SellerOrderController::class, 'resume']);

    });
});

Route::prefix('/user')->group(function() {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'user'])->group(function() {
        Route::get('/logout', [SellerAuthController::class, 'logout']);

        Route::get('/products', [UserProductController::class, 'index']);
        Route::get('/product/{id}', [UserProductController::class, 'show']);

        Route::get('/carts', [UserCartController::class, 'index']);
        Route::post('/cart/detail', [UserCartController::class, 'show']);
        Route::post('/cart/add', [UserCartController::class, 'store']);
        Route::patch('/cart', [UserCartController::class, 'update']);
        Route::delete('/cart', [UserCartController::class, 'destroy']);

        Route::get('/order/checkout', [UserOrderController::class, 'order']);
        Route::get('/order/confirm_payment', [UserOrderController::class, 'confirm_payment']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
