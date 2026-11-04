<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('cafe_name')->default('Sekarsa Coffee');
            $table->string('cafe_tagline')->default('Warmth, Taste & Harmony in Every Sip');
            $table->text('cafe_address')->nullable();
            $table->string('cafe_phone')->nullable();
            $table->string('cafe_instagram')->nullable();
            $table->text('whatsapp_notification_template')->nullable();
            $table->integer('estimated_minutes_per_queue')->default(4); // Estimasi pengerjaan per antrean FIFO
            $table->string('qr_permanent_url')->default('http://localhost/skripsi/public/pesan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};

