<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('qr_access_key', 64)->default('sekarsa_qr_cafe_2026')->after('qr_permanent_url');
            $table->boolean('require_qr_scan')->default(true)->after('qr_access_key');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['qr_access_key', 'require_qr_scan']);
        });
    }
};
