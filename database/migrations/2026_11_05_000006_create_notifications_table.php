<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->enum('type', ['web_sound', 'whatsapp', 'push', 'system'])->default('web_sound');
            $table->string('recipient'); // Nama pelanggan atau nomor WhatsApp
            $table->text('message');     // Isi teks notifikasi
            $table->enum('status', ['sent', 'pending', 'failed'])->default('sent');
            $table->dateTime('sent_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

