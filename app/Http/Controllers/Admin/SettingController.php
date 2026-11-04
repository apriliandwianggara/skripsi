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
     * Update Pengaturan
     */
    public function update(Request $request)
    {
        $request->validate([
            'cafe_name' => 'required|string|max:100',
            'cafe_tagline' => 'nullable|string|max:200',
            'cafe_address' => 'nullable|string',
            'cafe_phone' => 'nullable|string|max:50',
            'cafe_instagram' => 'nullable|string|max:100',
            'whatsapp_notification_template' => 'required|string',
            'estimated_minutes_per_queue' => 'required|integer|min:1|max:30',
            'qr_permanent_url' => 'required|url',
        ]);

        $setting = Setting::get();
        $setting->update($request->all());

        return back()->with('success', 'Pengaturan sistem Sekarsa Coffee berhasil diperbarui.');
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
            $orderUrl = url('/pesan?meja=' . urlencode($tableNumber));
        } else {
            $qrSvg = $this->qrService->generatePermanentSvg();
            $orderUrl = $this->qrService->getPermanentOrderUrl();
        }

        return view('admin.settings.qr-print', compact('setting', 'qrSvg', 'orderUrl', 'tableNumber'));
    }
}

