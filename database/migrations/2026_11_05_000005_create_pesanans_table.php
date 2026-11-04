<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pesanan')->unique(); // e.g. A001, A002
            $table->foreignId('meja_id')->constrained('mejas')->onDelete('cascade');
            $table->string('nama_pelanggan');
            $table->text('catatan_umum')->nullable();
            
            // Financials
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->foreignId('promo_id')->nullable()->constrained('promos')->onDelete('set null');
            $table->decimal('diskon_promo', 12, 2)->default(0);
            $table->decimal('total_akhir', 12, 2)->default(0);

            // Two distinct statuses as required by skripsi design
            $table->enum('status_pesanan', ['menunggu', 'diproses', 'selesai', 'diambil', 'dibatalkan'])->default('menunggu')->index();
            $table->enum('status_pembayaran', ['belum_dibayar', 'menunggu_konfirmasi', 'lunas', 'dibatalkan'])->default('belum_dibayar')->index();
            
            // Payment details
            $table->enum('metode_pembayaran', ['tunai', 'qris'])->nullable();
            $table->decimal('jumlah_bayar', 12, 2)->nullable();
            $table->decimal('kembalian', 12, 2)->nullable();
            $table->string('bukti_qris')->nullable();

            // FIFO timestamps & audit trail
            $table->dateTime('waktu_masuk')->index(); // Basis utama algoritma antrean FIFO
            $table->dateTime('waktu_diproses')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->dateTime('waktu_diambil')->nullable();
            $table->dateTime('waktu_bayar')->nullable();

            // Cashier/Admin who handled payment or transaction
            $table->foreignId('kasir_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Session token to link order securely to customer's mobile browser without requiring login
            $table->string('session_token', 64)->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
