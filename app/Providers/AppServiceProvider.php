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
