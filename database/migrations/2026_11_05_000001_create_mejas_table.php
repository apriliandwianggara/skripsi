<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_meja')->unique();
            $table->integer('kapasitas')->default(4);
            $table->string('lokasi')->default('Indoor (Non-Smoking)');
            $table->enum('status', ['tersedia', 'terisi', 'reservasi'])->default('tersedia');
            $table->string('qr_token', 64)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
