<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    protected QrCodeService $qrService;

    public function __construct(QrCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * D. Pengaturan Sistem & Tampilan Website
     */
    public function index()
    {
        $this->ensureSchemaUpToDate();

        try {
            $setting = Setting::get();
            $permanentUrl = $this->qrService->getPermanentOrderUrl();
            $qrSvg = $this->qrService->generatePermanentSvg();
            $qrDataUri = $this->qrService->generatePermanentDataUri();

            return view('admin.settings.index', compact('setting', 'permanentUrl', 'qrSvg', 'qrDataUri'));
        } catch (\Throwable $e) {
            Log::error('SettingController@index error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            
            // Fallback jika ada komponen yang gagal
            $setting = Setting::first() ?? new Setting();
            $permanentUrl = url('/pesan');
            $qrSvg = '';
            $qrDataUri = '';

            return view('admin.settings.index', compact('setting', 'permanentUrl', 'qrSvg', 'qrDataUri'))
                ->with('error', 'Terjadi penyesuaian sistem: ' . $e->getMessage());
        }
    }

    /**
     * Update Pengaturan (Termasuk Kustomisasi Konten & Upload Logo / Banner)
     */
    public function update(Request $request)
    {
        $this->ensureSchemaUpToDate();

        $data = $request->validate([
            'cafe_name' => 'required|string|max:100',
            'cafe_tagline' => 'nullable|string|max:200',
            'hero_title' => 'nullable|string|max:150',
            'hero_subtitle' => 'nullable|string|max:500',
            'cafe_opening_hours' => 'nullable|string|max:150',
            'announcement_text' => 'nullable|string|max:500',
            'cafe_address' => 'nullable|string',
            'cafe_phone' => 'nullable|string|max:50',
            'cafe_instagram' => 'nullable|string|max:100',
            'footer_text' => 'nullable|string|max:200',
            'whatsapp_notification_template' => 'required|string',
            'estimated_minutes_per_queue' => 'required|integer|min:1|max:30',
            'qr_permanent_url' => 'required|url',
            'qr_access_key' => 'nullable|string|max:64',
            'alarm_repeat_interval' => 'nullable|integer|min:1|max:15',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:3072',
            'banner_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $setting = Setting::get();

            // Handle upload logo baru
            if ($request->hasFile('logo_file')) {
                $logoFile = $request->file('logo_file');
                $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
                $targetDir = public_path('images');
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                $logoFile->move($targetDir, $logoName);
                $data['cafe_logo'] = 'images/' . $logoName;
            }

            // Handle upload banner kafe baru
            if ($request->hasFile('banner_file')) {
                $bannerFile = $request->file('banner_file');
                $bannerName = 'banner_' . time() . '.' . $bannerFile->getClientOriginalExtension();
                $targetDir = public_path('images');
                if (!file_exists($targetDir)) {
                    @mkdir($targetDir, 0755, true);
                }
                $bannerFile->move($targetDir, $bannerName);
                $data['cafe_banner'] = 'images/' . $bannerName;
            }

            // Proteksi QR Gate di Meja selalu aktif
            $data['require_qr_scan'] = true;
            // GPS dinonaktifkan sepenuhnya
            $data['enable_gps_radius'] = false;
            $data['demo_mode_enabled'] = false;
            $data['alarm_sound_enabled'] = $request->boolean('alarm_sound_enabled', true);

            if (empty($data['qr_access_key'])) {
                $data['qr_access_key'] = 'sekarsa_qr_cafe_2026';
            }

            $setting->update($data);

            return back()->with('success', 'Pengaturan tampilan & sistem berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('SettingController@update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * Otomatis sinkronisasi skema tabel settings di database langsung via SQL
     * Aman dari masalah doctrine/dbal dan kompatibel 100% dengan MySQL / MariaDB InfinityFree.
     */
    private function ensureSchemaUpToDate(): void
    {
        $sqls = [
            "ALTER TABLE `settings` ADD `cafe_logo` VARCHAR(255) NULL AFTER `cafe_name`",
            "ALTER TABLE `settings` ADD `cafe_banner` VARCHAR(255) NULL AFTER `cafe_logo`",
            "ALTER TABLE `settings` ADD `hero_title` VARCHAR(150) NULL DEFAULT 'Selamat Datang di Sekarsa Coffee' AFTER `cafe_tagline`",
            "ALTER TABLE `settings` ADD `hero_subtitle` TEXT NULL AFTER `hero_title`",
            "ALTER TABLE `settings` ADD `cafe_opening_hours` VARCHAR(150) NULL DEFAULT 'Setiap Hari: 08:00 - 23:00 WIB' AFTER `cafe_address`",
            "ALTER TABLE `settings` ADD `announcement_text` TEXT NULL AFTER `hero_subtitle`",
            "ALTER TABLE `settings` ADD `footer_text` VARCHAR(200) NULL DEFAULT '© 2026 Sekarsa Coffee & Space. All rights reserved.' AFTER `cafe_instagram`",
            "ALTER TABLE `settings` ADD `alarm_sound_enabled` TINYINT(1) DEFAULT 1 AFTER `whatsapp_notification_template`",
            "ALTER TABLE `settings` ADD `alarm_repeat_interval` INT DEFAULT 3 AFTER `alarm_sound_enabled`",
            "ALTER TABLE `settings` ADD `gps_tolerance_meters` INT DEFAULT 50",
            "ALTER TABLE `settings` ADD `demo_mode_enabled` TINYINT(1) DEFAULT 0",
            "ALTER TABLE `settings` ADD `demo_mode_label` VARCHAR(100) NULL DEFAULT 'Mode Demo Sidang Skripsi'",
        ];

        foreach ($sqls as $sql) {
            try {
                DB::statement($sql);
            } catch (\Throwable $e) {
                // Abaikan error 1060 (duplicate column name) jika kolom sudah ada
            }
        }
    }

    /**
     * Halaman Cetak Kartu Standee Kode QR Permanen (Dipakai Selamanya)
     */
    public function printPermanentQr(Request $request)
    {
        $setting = Setting::get();
        $tableNumber = $request->query('meja');

        if ($tableNumber) {
            $qrSvg = $this->qrService->generateTableSvg($tableNumber);
            $orderUrl = $this->qrService->getTableOrderUrl($tableNumber);
        } else {
            $qrSvg = $this->qrService->generatePermanentSvg();
            $orderUrl = $this->qrService->getPermanentOrderUrl();
        }

        return view('admin.settings.qr-print', compact('setting', 'qrSvg', 'orderUrl', 'tableNumber'));
    }
}
