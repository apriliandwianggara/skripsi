<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Meja;
use App\Models\Pengaturan;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MejaController extends Controller
{
    protected QrCodeService $qrService;

    public function __construct(QrCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function index()
    {
        $mejas = Meja::with('activePesanan')->orderBy('nomor_meja')->get();
        return view('admin.mejas.index', compact('mejas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_meja' => 'required|string|max:20|unique:mejas,nomor_meja',
            'kapasitas' => 'required|integer|min:1|max:50',
            'lokasi' => 'required|string|max:100',
        ]);

        $meja = Meja::create([
            'nomor_meja' => trim($request->nomor_meja),
            'kapasitas' => $request->kapasitas,
            'lokasi' => $request->lokasi,
            'status' => 'tersedia',
            'qr_token' => Str::random(32),
        ]);

        AktivitasLog::catat(
            'TAMBAH_MEJA',
            "Menambahkan Meja baru [{$meja->nomor_meja}] di area {$meja->lokasi}."
        );

        return back()->with('success', "Meja {$meja->nomor_meja} berhasil ditambahkan.");
    }

    public function update(Request $request, Meja $meja)
    {
        $request->validate([
            'nomor_meja' => 'required|string|max:20|unique:mejas,nomor_meja,' . $meja->id,
            'kapasitas' => 'required|integer|min:1|max:50',
            'lokasi' => 'required|string|max:100',
            'status' => 'required|in:tersedia,terisi,reservasi',
        ]);

        $meja->update([
            'nomor_meja' => trim($request->nomor_meja),
            'kapasitas' => $request->kapasitas,
            'lokasi' => $request->lokasi,
            'status' => $request->status,
        ]);

        AktivitasLog::catat(
            'UBAH_MEJA',
            "Memperbarui informasi Meja [{$meja->nomor_meja}]."
        );

        return back()->with('success', "Data Meja {$meja->nomor_meja} berhasil diperbarui.");
    }

    public function destroy(Meja $meja)
    {
        $nomor = $meja->nomor_meja;
        $meja->delete();

        AktivitasLog::catat(
            'HAPUS_MEJA',
            "Menghapus Meja [{$nomor}] dari sistem."
        );

        return back()->with('success', "Meja {$nomor} berhasil dihapus.");
    }

    public function resetStatus(Meja $meja)
    {
        $meja->update(['status' => 'tersedia']);
        return back()->with('success', "Status Meja {$meja->nomor_meja} direset menjadi Tersedia.");
    }

    /**
     * Tampilan Cetak QR Code Meja Individual
     */
    public function showQr(Meja $meja)
    {
        $qrSvg = $this->qrService->generateSvgForMeja($meja);
        $orderUrl = $this->qrService->getTableOrderUrl($meja);
        $pengaturan = Pengaturan::get();

        return view('admin.mejas.qr-single', compact('meja', 'qrSvg', 'orderUrl', 'pengaturan'));
    }

    /**
     * Tampilan Cetak Seluruh QR Code Meja Sekaligus (Untuk ditempel di meja pelanggan)
     */
    public function printAllQr()
    {
        $mejas = Meja::orderBy('nomor_meja')->get();
        $pengaturan = Pengaturan::get();

        $qrData = [];
        foreach ($mejas as $meja) {
            $qrData[] = [
                'meja' => $meja,
                'qrSvg' => $this->qrService->generateSvgForMeja($meja),
                'orderUrl' => $this->qrService->getTableOrderUrl($meja),
            ];
        }

        return view('admin.mejas.qr-all', compact('qrData', 'pengaturan'));
    }
}
