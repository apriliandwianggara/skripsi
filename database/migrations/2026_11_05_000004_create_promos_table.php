<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('kode_promo')->unique();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['persen', 'nominal']);
            $table->decimal('nilai_potongan', 12, 2);
            $table->decimal('min_pembelian', 12, 2)->default(0);
            $table->decimal('maksimal_potongan', 12, 2)->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
