<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-heal skema database untuk hosting tanpa terminal (InfinityFree)
        try {
            if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'payment_status')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `orders` ADD `payment_method` VARCHAR(20) DEFAULT 'qris' AFTER `total_price`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `orders` ADD `payment_status` VARCHAR(30) DEFAULT 'menunggu_pembayaran' AFTER `payment_method`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `orders` ADD `paid_at` DATETIME NULL AFTER `payment_status`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `orders` ADD `payment_proof` VARCHAR(255) NULL AFTER `paid_at`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `orders` ADD `payment_note` TEXT NULL AFTER `payment_proof`");
            }
            if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'qris_dana_number')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `qris_dana_number` VARCHAR(30) NULL DEFAULT '087811139210'");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `qris_merchant_name` VARCHAR(100) NULL DEFAULT 'Sekarsa Coffee'");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `enable_qris_payment` TINYINT(1) DEFAULT 1");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `enable_cash_payment` TINYINT(1) DEFAULT 1");
            }
            if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'cafe_wifi')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `cafe_wifi` VARCHAR(100) NULL DEFAULT 'Sekarsa-Guest' AFTER `cafe_address`");
            }
            if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'is_cafe_open')) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `is_cafe_open` TINYINT(1) DEFAULT 1 AFTER `cafe_opening_hours`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `cafe_opening_time` VARCHAR(10) NULL DEFAULT '08:00' AFTER `is_cafe_open`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `cafe_closing_time` VARCHAR(10) NULL DEFAULT '23:00' AFTER `cafe_opening_time`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `auto_schedule_enabled` TINYINT(1) DEFAULT 0 AFTER `cafe_closing_time`");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `settings` ADD `cafe_closed_message` TEXT NULL AFTER `auto_schedule_enabled`");
            }
        } catch (\Throwable $e) {
            // Lanjutkan jika koneksi DB belum siap
        }

        // Bagikan setting sistem & identitas kafe ke seluruh blade view secara otomatis
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $setting = Setting::get();
                    $view->with('globalSetting', $setting);
                    if (!$view->offsetExists('setting')) {
                        $view->with('setting', $setting);
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan jika migrasi belum siap
            }
        });
    }
}
