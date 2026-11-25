<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Kecualikan endpoint pemesanan pelanggan, konfirmasi bayar, logout & API publik dari CSRF
        // agar pemesanan, pembayaran, dan logout dari berbagai HP / sesi idle tidak terblokir Token Mismatch / 419 Expired
        $middleware->validateCsrfTokens(except: [
            'logout',
            'pesan/kirim',
            'pesanan/*/konfirmasi-bayar',
            'pesanan/*/midtrans-*',
            'pesanan/*/qris/*',
            'pesanan/*/simulasi-qris/*',
            'api/*',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tangani jika terjadi sesi kedaluwarsa (419 Page Expired) agar otomatis dialihkan ke login secara mulus
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->is('logout') || $request->routeIs('logout')) {
                return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
            }
            return redirect()->route('login')->with('warning', 'Sesi Anda telah kedaluwarsa. Silakan masuk kembali.');
        });
    })->create();
