<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('cafe_logo')->nullable()->after('cafe_name');
            $table->string('cafe_banner')->nullable()->after('cafe_logo');
            $table->string('hero_title')->nullable()->default('Selamat Datang di Sekarsa Coffee')->after('cafe_tagline');
            $table->text('hero_subtitle')->nullable()->after('hero_title');
            $table->string('cafe_opening_hours')->nullable()->default('Setiap Hari: 08:00 - 23:00 WIB')->after('cafe_address');
            $table->text('announcement_text')->nullable()->after('hero_subtitle');
            $table->string('footer_text')->nullable()->default('© 2026 Sekarsa Coffee & Space. All rights reserved.')->after('cafe_instagram');
            $table->integer('gps_tolerance_meters')->default(50)->after('cafe_radius_meters');
            $table->boolean('alarm_sound_enabled')->default(true)->after('whatsapp_notification_template');
            $table->integer('alarm_repeat_interval')->default(3)->after('alarm_sound_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'cafe_logo',
                'cafe_banner',
                'hero_title',
                'hero_subtitle',
                'cafe_opening_hours',
                'announcement_text',
                'footer_text',
                'gps_tolerance_meters',
                'alarm_sound_enabled',
                'alarm_repeat_interval',
            ]);
        });
    }
};
