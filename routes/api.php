<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AlamatController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\XenditWebhookController;
// Jika kamu membuat Controller untuk Promo & Artikel, kamu bisa use di sini:
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\ArticleController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// --- Rute Lupa Password (Public / Tidak perlu login) ---
Route::post('/forgot-password/send-otp', [\App\Http\Controllers\Api\ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [\App\Http\Controllers\Api\ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset-password', [\App\Http\Controllers\Api\ForgotPasswordController::class, 'resetPassword']);

Route::post('/xendit/webhook', [XenditWebhookController::class, 'handle']);

// Rute untuk mengecek status pembayaran Xendit
Route::get('/transaksi/status/{orderId}', [TransactionController::class, 'cekStatusPembayaran']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Wajib Header Token)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // 1. Katalog & Etalase Menu
    Route::get('/categories', [MenuController::class, 'getCategories']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/menus', [MenuController::class, 'getMenus']);
    Route::get('/menus/{id}', [MenuController::class, 'getMenuDetail']);

    // 2. Transaksi & Pemesanan (Checkout & Struk)
    Route::post('/checkout', [TransactionController::class, 'checkout']);
    Route::get('/transaksi/struk/{id}', [TransactionController::class, 'getReceipt']);
    Route::get('/receipt/{id}', [TransactionController::class, 'getReceipt']);

    // 3. Riwayat Transaksi Pelanggan
    Route::get('/orders', [OrderController::class, 'index']);

    // 4. Sistem Loyalitas / Reward Poin
    Route::get('/rewards', [RewardController::class, 'index']);
    Route::post('/rewards/redeem', [RewardController::class, 'redeem']);
    Route::get('/vouchers/me', [RewardController::class, 'myVouchers']);
    Route::get('/tax-rate', [RewardController::class, 'getTaxRate']);

    // 5. Profil, Alamat & Keamanan
    Route::get('/user', function (Request $request) {
        return response()->json(['success' => true, 'data' => $request->user()]);
    });
    Route::get('/user/notifications', [\App\Http\Controllers\Api\NotifikasiController::class, 'index']);
    Route::put('/user/notifications/read', [\App\Http\Controllers\Api\NotifikasiController::class, 'markAllAsRead']);

    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/user/upload-photo', [ProfileController::class, 'uploadPhoto']);
    Route::put('/user/notification-settings', [ProfileController::class, 'updateNotificationSettings']);
    Route::apiResource('/user/addresses', AlamatController::class)->except(['show']);

    // Keamanan Akun
    Route::put('/user/change-password', [ProfileController::class, 'changePassword']);
    Route::get('/user/login-history', [ProfileController::class, 'getLoginHistory']); // Duplikat sudah dihapus
    Route::post('/user/toggle-2fa', [ProfileController::class, 'toggle2FA']);
    Route::post('/user/logout-other-devices', [ProfileController::class, 'logoutOtherDevices']);

    // 6. Promo & Artikel Berita
    Route::get('/promos', [\App\Http\Controllers\Api\PromoController::class, 'index']);
    Route::get('/articles', [\App\Http\Controllers\Api\ArticleController::class, 'index']);
});