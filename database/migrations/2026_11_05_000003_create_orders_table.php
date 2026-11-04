<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // contoh: ORD-20261105-001
            $table->string('queue_number');           // contoh: 001, 002, 003 (Nomor Antrean FIFO harian)
            $table->string('customer_name');          // Nama Pelanggan
            $table->string('customer_phone')->nullable(); // Nomor WhatsApp untuk notifikasi
            $table->string('table_number')->nullable();   // Nomor Meja (jika ada)
            $table->text('customer_notes')->nullable();   // Catatan pesanan
            $table->decimal('total_price', 12, 2)->default(0);

            // 4 Status Utama Sesuai Penelitian Skripsi (+ dibatalkan jika ada kendala)
            $table->enum('status', ['menunggu', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'])
                  ->default('menunggu')
                  ->index();

            // Timestamp pencatatan waktu untuk algoritma FIFO & pengujian skripsi
            $table->dateTime('received_at')->index(); // Waktu pesanan diterima server (Basis Utama FIFO)
            $table->dateTime('processed_at')->nullable(); // Waktu mulai diproses pegawai
            $table->dateTime('ready_at')->nullable();     // Waktu siap diambil (trigger notifikasi)
            $table->dateTime('completed_at')->nullable(); // Waktu pesanan diambil / selesai

            $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('tracking_token', 64)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

