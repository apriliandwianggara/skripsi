<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Kasir\KasirController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\MejaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AktivitasLogController;
use App\Http\Controllers\Admin\PengaturanController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Pemesanan QR Code Sekarsa Coffee (Skripsi)
|--------------------------------------------------------------------------
*/

// Redirect root to customer order page
Route::get('/', function () {
    return redirect()->route('customer.order');
});

// Quick link meja: /meja/01
Route::get('/meja/{nomor_meja}', function ($nomor_meja) {
    return redirect()->route('customer.order', ['meja' => $nomor_meja]);
})->name('customer.meja_direct');

/*
|--------------------------------------------------------------------------
| Alur 1, 2, 5, 7, 8, 9: Tampilan & Fitur Pelanggan (Tanpa Perlu Login)
|--------------------------------------------------------------------------
*/
Route::prefix('pesan')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('customer.order');
    Route::post('/pilih-meja', [OrderController::class, 'setMeja'])->name('customer.set_meja');
    Route::post('/cek-promo', [OrderController::class, 'checkPromo'])->name('customer.check_promo');
    Route::post('/kirim', [OrderController::class, 'submitOrder'])->name('customer.submit_order');
});

Route::get('/pesanan/{nomor_pesanan}', [OrderController::class, 'trackOrder'])->name('customer.track');
Route::get('/api/pesanan/{nomor_pesanan}/status', [OrderController::class, 'statusApi'])->name('customer.status_api');
Route::post('/pesanan/{nomor_pesanan}/upload-qris', [OrderController::class, 'uploadQris'])->name('customer.upload_qris');

/*
|--------------------------------------------------------------------------
| Otentikasi Petugas (Admin & Kasir)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Tampilan & Fitur Kasir (Antrean FIFO, Proses, Selesai, Pembayaran)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/api/antrean', [KasirController::class, 'queueApi'])->name('queue_api');
    
    // Alur Status Pesanan
    Route::post('/pesanan/{pesanan}/proses', [KasirController::class, 'processOrder'])->name('order.process');
    Route::post('/pesanan/{pesanan}/selesai', [KasirController::class, 'completeOrder'])->name('order.complete');
    Route::post('/pesanan/{pesanan}/ambil', [KasirController::class, 'pickupOrder'])->name('order.pickup');
    Route::post('/pesanan/{pesanan}/batal', [KasirController::class, 'cancelOrder'])->name('order.cancel');
    
    // Alur Pembayaran (Tunai / QRIS)
    Route::post('/pesanan/{pesanan}/bayar', [KasirController::class, 'processPayment'])->name('order.payment');
    Route::get('/pesanan/{pesanan}/struk', [KasirController::class, 'printReceipt'])->name('order.receipt');
    
    // Quick Toggle Stok Menu
    Route::post('/menu/{menu}/toggle-stok', [KasirController::class, 'toggleMenuStock'])->name('menu.toggle_stock');
});

/*
|--------------------------------------------------------------------------
| Modul Pengelolaan Menu, Kategori & Promo (Bisa diakses Kasir & Admin dengan Audit Log)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,kasir'])->prefix('kelola')->group(function () {
    // Menu
    Route::resource('menus', MenuController::class)->names('admin.menus');
    Route::post('menus/{menu}/toggle-stok', [MenuController::class, 'toggleStock'])->name('admin.menus.toggle_stock');

    // Kategori
    Route::resource('kategoris', KategoriController::class)->names('admin.kategoris')->except(['create', 'show', 'edit']);

    // Promo
    Route::resource('promos', PromoController::class)->names('admin.promos')->except(['create', 'show', 'edit']);
});

/*
|--------------------------------------------------------------------------
| Tampilan & Fitur Admin/Owner
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/laporan', [AdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/laporan/cetak', [AdminDashboardController::class, 'printReport'])->name('reports.print');

    // Manajemen Meja & QR Code Generator
    Route::resource('mejas', MejaController::class)->except(['create', 'show', 'edit']);
    Route::post('mejas/{meja}/reset', [MejaController::class, 'resetStatus'])->name('mejas.reset');
    Route::get('mejas/{meja}/qr', [MejaController::class, 'showQr'])->name('mejas.qr');
    Route::get('mejas/cetak-semua/qr', [MejaController::class, 'printAllQr'])->name('mejas.print_all_qr');

    // Kelola Akun Pengguna
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // Log Aktivitas Petugas
    Route::get('/logs', [AktivitasLogController::class, 'index'])->name('logs.index');

    // Pengaturan Cafe & QRIS
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
});
