<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\WebLoginController;
use App\Livewire\BaristaDashboard;
use App\Livewire\RiwayatPesanan;
use App\Livewire\PosMenu;
use App\Livewire\AnalyticsDashboard;
use App\Livewire\AdminDashboard;
use App\Livewire\OrderManagement;
use App\Livewire\MenuManagement;
use App\Livewire\CustomerManagement;
use App\Livewire\VoucherPromoManagement;
use App\Livewire\LoyaltyProgram;
use App\Livewire\NewsContentManagement;
use App\Livewire\PromoBannerManagement;
use App\Livewire\MejaManagement;




Route::get('/', function () {
    return redirect('/login');
});

// --- RUTE UNTUK TAMU (BELUM LOGIN) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [WebLoginController::class, 'create'])->name('login');
    Route::post('/login', [WebLoginController::class, 'store']);
});

// --- RUTE UNTUK PENGGUNA YANG SUDAH LOGIN ---
Route::middleware('auth')->group(function () {

    // Rute Logout
    Route::post('/logout', [WebLoginController::class, 'destroy'])->name('logout');

    // Rute Khusus Kasir (Admin juga bisa akses)
    Route::middleware('role:kasir,admin')->group(function () {

        Route::get('/kasir/pos', function () {
            return view('kasir.dashboard');
        })->name('kasir.pos');

        // Rute Proses Pembayaran Kasir
        Route::get('/kasir/pembayaran/{id}', function ($id) {
            return view('kasir.pembayaran', ['id' => $id]);
        })->name('kasir.pembayaran');

        // Rute untuk Panel Antrean Barista/Dapur
        Route::get('/barista/antrean', function () {
            return view('barista.antrean'); // Mengarah ke file blade pembungkus
        })->name('barista.orders');
        // Nanti rute untuk memproses pesanan kasir bisa ditambahkan di dalam sini juga

        Route::get('/dapur/antrean', function () {
            return view('dapur.antrean'); // Kita bisa gunakan file pembungkus layout yang sama seperti barista
        })->name('dapur.orders');

        Route::get('/kasir/riwayat', function () {
            return view('kasir.riwayat-wrapper');
        })->name('kasir.riwayat');



        Route::get('/kasir/menu', function () {
            return view('kasir.menu-wrapper'); // Menggunakan wrapper layaknya halaman lain
        })->name('kasir.menu');

        // Ubah rute analitik menjadi seperti ini
        Route::get('/kasir/analytics', function () {
            return view('kasir.analytics-wrapper');
        })->name('kasir.analytics');



        // Rute Admin Dashboard dengan pengunci (Hanya yang sudah Login & ber-Role 'admin')
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard-wrapper');
        })->name('admin.dashboard')->middleware(['auth', 'role:admin']);

        Route::get('/admin/orders', function () {
            return view('admin.orders-wrapper');
        })->name('admin.orders')->middleware(['auth', 'role:admin']);

        Route::get('/admin/menus', function () {
            return view('admin.menus-wrapper');
        })->name('admin.menus')->middleware(['auth', 'role:admin']);

        Route::get('/admin/customers', function () {
            return view('admin.customers-wrapper');
        })->name('admin.customers')->middleware(['auth', 'role:admin']);

        Route::get('/admin/vouchers', function () {
            return view('admin.vouchers-wrapper');
        })->name('admin.vouchers')->middleware(['auth', 'role:admin']);

        Route::get('/admin/loyalty', function () {
            return view('admin.loyalty-wrapper');
        })->name('admin.loyalty')->middleware(['auth', 'role:admin']);

        Route::get('/admin/news', function () {
            return view('admin.news-wrapper');
        })->name('admin.news')->middleware(['auth', 'role:admin']);

        Route::get('/admin/promos', function () {
            return view('admin.promos-wrapper');
        })->name('admin.promos')->middleware(['auth', 'role:admin']);


        Route::get('/admin/mejas', function () {
            return view('admin.mejas-wrapper');
        })->name('admin.mejas')->middleware(['auth', 'role:admin']);

    });
});