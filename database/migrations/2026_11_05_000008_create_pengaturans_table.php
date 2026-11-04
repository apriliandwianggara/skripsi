<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cafe')->default('Sekarsa Coffee');
            $table->string('tagline')->default('Warmth, Taste & Harmony in Every Sip');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('instagram')->nullable();
            $table->text('pesan_struk')->nullable();
            $table->string('qris_image')->nullable();
            $table->integer('estimasi_menit_per_antrean')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
