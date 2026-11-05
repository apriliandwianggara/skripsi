<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\QrCodeService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected QrCodeService $qrService;

    public function __construct(QrCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * D. Pengaturan Sistem
     */
    public function index()
    {
        $setting = Setting::get();
        $permanentUrl = $this->qrService->getPermanentOrderUrl();
        $qrSvg = $this->qrService->generatePermanentSvg();
        $qrDataUri = $this->qrService->generatePermanentDataUri();

        return view('admin.settings.index', compact('setting', 'permanentUrl', 'qrSvg', 'qrDataUri'));
    }

    /**
     * Update Pengaturan (Termasuk Kustomisasi Konten & Upload Gambar)
     */
    public function update(Request $request)
    {
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
            'cafe_latitude' => 'nullable|numeric|between:-90,90',
            'cafe_longitude' => 'nullable|numeric|between:-180,180',
            'cafe_radius_meters' => 'nullable|integer|min:10|max:5000',
            'gps_tolerance_meters' => 'nullable|integer|min:0|max:500',
            'alarm_repeat_interval' => 'nullable|integer|min:1|max:15',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:3072',
            'banner_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $setting = Setting::get();

        // Handle upload logo baru
        if ($request->hasFile('logo_file')) {
            $logoFile = $request->file('logo_file');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $targetDir = public_path('images');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
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
                mkdir($targetDir, 0755, true);
            }
            $bannerFile->move($targetDir, $bannerName);
            $data['cafe_banner'] = 'images/' . $bannerName;
        }

        $data['require_qr_scan'] = $request->boolean('require_qr_scan');
        $data['enable_gps_radius'] = $request->boolean('enable_gps_radius');
        $data['demo_mode_enabled'] = $request->boolean('demo_mode_enabled');
        $data['alarm_sound_enabled'] = $request->boolean('alarm_sound_enabled');

        if (empty($data['qr_access_key'])) {
            $data['qr_access_key'] = 'sekarsa_qr_cafe_2026';
        }

        $setting->update($data);

        return back()->with('success', 'Pengaturan tampilan & sistem berhasil diperbarui.');
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
