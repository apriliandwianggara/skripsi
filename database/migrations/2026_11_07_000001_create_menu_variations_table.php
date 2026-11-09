<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->string('name'); // contoh: 'S', 'M', 'L' atau 'Dingin', 'Panas'
            $table->decimal('price', 12, 2);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('menu_variation_id')->nullable()->after('menu_id')->constrained('menu_variations')->onDelete('set null');
            $table->string('variation_name')->nullable()->after('menu_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['menu_variation_id']);
            $table->dropColumn(['menu_variation_id', 'variation_name']);
        });

        Schema::dropIfExists('menu_variations');
    }
};

