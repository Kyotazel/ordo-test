<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'do_login']);

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'do_register']);

    Route::get('/test_midtrans', function() {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = 'SB-Mid-server-yHGHYOFp54L0Yrf8-sgmL6iH';
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' => 10000,
            ),
            'customer_details' => array(
                'first_name' => 'budi',
                'last_name' => 'pratama',
                'email' => 'budi.pra@example.com',
                'phone' => '08111222333',
            ),
        );
        
        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        dd($paymentUrl);
    });
});

Route::middleware('auth')->group(function() {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::post('/category/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::post('/category/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/sellers', [SellerController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
});

