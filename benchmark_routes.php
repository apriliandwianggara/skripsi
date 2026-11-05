<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test internal execution time of controllers directly
$tests = [
    'Login View' => fn() => app(App\Http\Controllers\Auth\AuthController::class)->showLogin(),
    'Order View' => fn() => app(App\Http\Controllers\Customer\CustomerOrderController::class)->index(new Illuminate\Http\Request()),
    'Menu Status API' => fn() => app(App\Http\Controllers\Customer\CustomerOrderController::class)->menuStatusApi(),
    'Admin Dashboard' => function() {
        auth()->loginUsingId(1);
        return app(App\Http\Controllers\Admin\DashboardController::class)->index();
    },
    'Admin Menus' => function() {
        auth()->loginUsingId(1);
        return app(App\Http\Controllers\Admin\MenuController::class)->index(new Illuminate\Http\Request());
    },
    'Admin Categories' => function() {
        auth()->loginUsingId(1);
        return app(App\Http\Controllers\Admin\CategoryController::class)->index();
    },
    'Admin Orders' => function() {
        auth()->loginUsingId(1);
        return app(App\Http\Controllers\Admin\OrderManagementController::class)->index(new Illuminate\Http\Request());
    },
    'Admin Reports' => function() {
        auth()->loginUsingId(1);
        return app(App\Http\Controllers\Admin\DashboardController::class)->reports(new Illuminate\Http\Request());
    },
    'Admin Settings' => function() {
        auth()->loginUsingId(1);
        return app(App\Http\Controllers\Admin\SettingController::class)->index();
    },
    'Pegawai Dashboard' => function() {
        auth()->loginUsingId(2);
        return app(App\Http\Controllers\Pegawai\PegawaiDashboardController::class)->dashboard();
    },
    'Pegawai Queue API' => function() {
        auth()->loginUsingId(2);
        return app(App\Http\Controllers\Pegawai\PegawaiDashboardController::class)->queueApi();
    },
];

echo "=== BENCHMARK CONTROLLER INTERNAL EXECUTION ===" . PHP_EOL;
foreach ($tests as $name => $fn) {
    $t1 = microtime(true);
    try {
        $res = $fn();
        if (is_object($res) && method_exists($res, 'render')) {
            $res->render();
        }
        $ms = round((microtime(true) - $t1) * 1000, 1);
        echo sprintf("%-20s : %6.1f ms [OK]\n", $name, $ms);
    } catch (\Throwable $e) {
        $ms = round((microtime(true) - $t1) * 1000, 1);
        echo sprintf("%-20s : %6.1f ms [FAIL: %s]\n", $name, $ms, $e->getMessage());
    }
}

