<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Kecualikan endpoint pemesanan pelanggan, konfirmasi bayar & API publik dari CSRF
        // agar pemesanan dan pembayaran dari berbagai HP / QR scan tidak terblokir Token Mismatch / 419 Expired
        $middleware->validateCsrfTokens(except: [
            'pesan/kirim',
            'pesanan/*/konfirmasi-bayar',
            'api/*',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
