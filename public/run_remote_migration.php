<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

header('Content-Type: text/plain; charset=utf-8');

echo "=== SEKARSA COFFEE SELF-MIGRATION RUNNER ===\n\n";

// 1. Jalankan ALTER TABLE manual langsung via DB statement (paling andal di cPanel / InfinityFree)
$sqls = [
    // Tabel orders
    "ALTER TABLE `orders` ADD `payment_method` VARCHAR(20) DEFAULT 'qris' AFTER `total_price`",
    "ALTER TABLE `orders` ADD `payment_status` VARCHAR(30) DEFAULT 'menunggu_pembayaran' AFTER `payment_method`",
    "ALTER TABLE `orders` ADD `paid_at` DATETIME NULL AFTER `payment_status`",
    "ALTER TABLE `orders` ADD `payment_proof` VARCHAR(255) NULL AFTER `paid_at`",
    "ALTER TABLE `orders` ADD `payment_note` TEXT NULL AFTER `payment_proof`",

    // Tabel settings
    "ALTER TABLE `settings` ADD `qris_dana_number` VARCHAR(30) NULL DEFAULT '087811139210'",
    "ALTER TABLE `settings` ADD `qris_merchant_name` VARCHAR(100) NULL DEFAULT 'Sekarsa Coffee'",
    "ALTER TABLE `settings` ADD `enable_qris_payment` TINYINT(1) DEFAULT 1",
    "ALTER TABLE `settings` ADD `enable_cash_payment` TINYINT(1) DEFAULT 1",

    // Update pesanan yang sudah selesai/diproses agar berstatus lunas
    "UPDATE `orders` SET `payment_status` = 'lunas' WHERE `status` IN ('diproses', 'siap_diambil', 'selesai') AND (`payment_status` IS NULL OR `payment_status` = 'menunggu_pembayaran')",
];

foreach ($sqls as $sql) {
    try {
        DB::statement($sql);
        echo "[OK] " . substr($sql, 0, 50) . "...\n";
    } catch (\Throwable $e) {
        echo "[INFO/EXISTS] " . substr($sql, 0, 50) . "... (" . $e->getMessage() . ")\n";
    }
}

// 2. Jalankan artisan migrate --force
try {
    Artisan::call('migrate', ['--force' => true]);
    echo "\n=== ARTISAN MIGRATE OUTPUT ===\n";
    echo Artisan::output();
} catch (\Throwable $e) {
    echo "\nArtisan migrate notice: " . $e->getMessage() . "\n";
}

// 3. Clear cache
try {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "\nCache view dan config berhasil dibersihkan.\n";
} catch (\Throwable $e) {
    //
}

echo "\nSELESAI! Silakan akses kembali dashboard pegawai.\n";

