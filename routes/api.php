<?php

use App\Http\Controllers\seller\AuthController as SellerAuthController;
use App\Http\Controllers\seller\ProductController as SellerProductController;
use App\Http\Controllers\user\AuthController as UserAuthController;
use App\Http\Controllers\user\ProductController as UserProductController;
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

    });
});

Route::prefix('/user')->group(function() {
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::post('/login', [UserAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'user'])->group(function() {
        Route::get('/logout', [SellerAuthController::class, 'logout']);

        Route::get('/products', [UserProductController::class, 'index']);
        Route::get('/product/{id}', [UserProductController::class, 'show']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
