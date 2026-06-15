<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RewardController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);


// Rute untuk mengecek status pembayaran Xendit
Route::get('/transaksi/status/{orderId}', [TransactionController::class, 'cekStatusPembayaran']);
/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib Header Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // 1. Katalog & Etalase Menu
    Route::get('/categories', [MenuController::class, 'getCategories']);
    Route::get('/menus', [MenuController::class, 'getMenus']);
    Route::get('/menus/{id}', [MenuController::class, 'getMenuDetail']);

    // 2. Transaksi & Pemesanan (Checkout & Struk)
    Route::post('/checkout', [TransactionController::class, 'checkout']);
    Route::get('/receipt/{id}', [TransactionController::class, 'getReceipt']);

    // 3. Riwayat Transaksi Pelanggan
    Route::get('/orders', [OrderController::class, 'index']);

    // 4. Sistem Loyalitas / Reward Poin
    Route::get('/rewards', [RewardController::class, 'index']);
    Route::post('/rewards/redeem', [RewardController::class, 'redeem']);

    // 5. Profil, Alamat & Keamanan
    Route::put('/user/notification-settings', [ProfileController::class, 'updateNotificationSettings']);
    Route::put('/user/change-password', [ProfileController::class, 'changePassword']);
    Route::get('/user/login-history', [ProfileController::class, 'getLoginHistory']);
    Route::apiResource('/user/addresses', AlamatController::class)->except(['show']);
    
    Route::post('/checkout', [TransactionController::class, 'checkout']);
    
    Route::get('/transaksi/struk/{id}', [TransactionController::class, 'getReceipt']);
});