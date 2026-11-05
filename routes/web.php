<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CustomerOrderController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderManagementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes - Skripsi Sekarsa Coffee
| Judul: Perancangan Sistem Pemesanan Digital dan Notifikasi Pengambilan Pesanan 
|        Berbasis Web dengan Algoritma First In First Out (FIFO) pada Sekarsa Coffee
|--------------------------------------------------------------------------
*/

// Redirect root ke halaman pemesanan pelanggan (akan diverifikasi oleh QR Gate)
Route::get('/', function () {
    return redirect()->route('customer.order');
});

/*
|--------------------------------------------------------------------------
| A. Halaman Pelanggan (Tanpa Perlu Login)
|--------------------------------------------------------------------------
*/
// Gerbang Validasi Scan QR Code di Meja
Route::get('/scan', [CustomerOrderController::class, 'scanQr'])->name('customer.scan');
Route::get('/akses-qr-diperlukan', [CustomerOrderController::class, 'qrRequired'])->name('customer.qr_required');

// 1. Halaman Pemesanan (Hanya dapat diakses setelah scan QR)
Route::get('/pesan', [CustomerOrderController::class, 'index'])->name('customer.order');
Route::get('/api/menu-status', [CustomerOrderController::class, 'menuStatusApi'])->name('customer.menu_status_api');
Route::post('/api/verify-location', [CustomerOrderController::class, 'verifyLocationApi'])->name('customer.verify_location');
Route::post('/pesan/kirim', [CustomerOrderController::class, 'submitOrder'])->name('customer.submit_order');

// 2. Halaman Konfirmasi Pesanan
Route::get('/pesanan/{token}/konfirmasi', [CustomerOrderController::class, 'confirmation'])->name('customer.confirmation');

// 3. Halaman Pelacakan Pesanan & 4. Notifikasi Suara Pengambilan
Route::get('/pesanan/{token}', [CustomerOrderController::class, 'track'])->name('customer.track');
Route::get('/api/pesanan/{token}/status', [CustomerOrderController::class, 'trackStatusApi'])->name('customer.status_api');

/*
|--------------------------------------------------------------------------
| Otentikasi Petugas (Admin & Pegawai / Barista)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| C. Dashboard Pegawai / Barista (Pusat Operasional Algoritma FIFO)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pegawai,admin'])->prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/api/antrean', [PegawaiDashboardController::class, 'queueApi'])->name('queue_api');
    
    // Alur Status Pesanan FIFO
    Route::post('/pesanan/{order}/proses', [PegawaiDashboardController::class, 'processOrder'])->name('order.process');
    Route::post('/pesanan/{order}/siap', [PegawaiDashboardController::class, 'readyOrder'])->name('order.ready');
    Route::post('/pesanan/{order}/selesai', [PegawaiDashboardController::class, 'completeOrder'])->name('order.complete');
    Route::post('/pesanan/{order}/batal', [PegawaiDashboardController::class, 'cancelOrder'])->name('order.cancel');
    
    // Notifikasi WhatsApp
    Route::get('/pesanan/{order}/whatsapp', [PegawaiDashboardController::class, 'sendWhatsApp'])->name('order.whatsapp');
});

/*
|--------------------------------------------------------------------------
| Manajemen Menu & Kategori (Bisa diakses Admin & Kasir/Pegawai)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,pegawai'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');
    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
});

/*
|--------------------------------------------------------------------------
| B. Dashboard Khusus Admin/Owner & D. Pengaturan Sistem
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Laporan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/live-stats', [DashboardController::class, 'liveStatsApi'])->name('live_stats');
    Route::get('/laporan', [DashboardController::class, 'reports'])->name('reports');
    Route::get('/laporan/cetak', [DashboardController::class, 'printReport'])->name('reports.print');

    // Manajemen Pesanan & Riwayat Transaksi
    Route::get('/pesanan', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::get('/riwayat-pesanan', [OrderManagementController::class, 'history'])->name('orders.history');

    // Manajemen Pengguna (Admin & Pegawai)
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // Pengaturan Sistem & Kode QR Pemesanan (Pakai Selamanya)
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/pengaturan/qr/cetak', [SettingController::class, 'printPermanentQr'])->name('settings.qr_print');
});
