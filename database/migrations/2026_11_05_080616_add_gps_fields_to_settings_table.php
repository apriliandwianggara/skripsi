<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('enable_gps_radius')->default(false)->after('require_qr_scan');
            $table->decimal('cafe_latitude', 10, 7)->default(-7.7588000)->after('enable_gps_radius');
            $table->decimal('cafe_longitude', 10, 7)->default(110.3855000)->after('cafe_latitude');
            $table->integer('cafe_radius_meters')->default(100)->after('cafe_longitude'); // Radius dalam meter
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'enable_gps_radius',
                'cafe_latitude',
                'cafe_longitude',
                'cafe_radius_meters',
            ]);
        });
    }
};
