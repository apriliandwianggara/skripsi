<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('demo_mode_enabled')->default(false)->after('gps_tolerance_meters');
            $table->string('demo_mode_label')->nullable()->default('Mode Demo Sidang Skripsi')->after('demo_mode_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['demo_mode_enabled', 'demo_mode_label']);
        });
    }
};
