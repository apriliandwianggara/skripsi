<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah field pembayaran pada tabel orders
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 20)->default('qris')->after('total_price'); // 'qris' atau 'cash'
            $table->string('payment_status', 30)->default('menunggu_pembayaran')->after('payment_method')->index(); // 'menunggu_pembayaran', 'lunas', 'batal'
            $table->dateTime('paid_at')->nullable()->after('payment_status');
            $table->string('payment_proof')->nullable()->after('paid_at');
            $table->text('payment_note')->nullable()->after('payment_proof');
        });

        // 2. Tambah konfigurasi pembayaran pada tabel settings
        Schema::table('settings', function (Blueprint $table) {
            $table->string('qris_dana_number', 30)->default('087811139210')->nullable()->after('demo_mode_label');
            $table->string('qris_merchant_name', 100)->default('Sekarsa Coffee')->nullable()->after('qris_dana_number');
            $table->boolean('enable_qris_payment')->default(true)->after('qris_merchant_name');
            $table->boolean('enable_cash_payment')->default(true)->after('enable_qris_payment');
        });

        // 3. Update pesanan lama yang sudah diproses/siap/selesai agar otomatis lunas
        DB::table('orders')->whereIn('status', ['diproses', 'siap_diambil', 'selesai'])->update([
            'payment_status' => 'lunas',
            'paid_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'paid_at', 'payment_proof', 'payment_note']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['qris_dana_number', 'qris_merchant_name', 'enable_qris_payment', 'enable_cash_payment']);
        });
    }
};

