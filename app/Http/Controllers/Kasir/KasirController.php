<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Menu;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Services\FifoQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    protected FifoQueueService $fifoService;

    public function __construct(FifoQueueService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

    /**
     * Dashboard Antrean FIFO Kasir / Barista
     */
    public function dashboard(Request $request)
    {
        // 1. Antrean FIFO: Menunggu dan Diproses (diurutkan STRICTLY berdasarkan waktu_masuk ASC)
        $antreanFifo = Pesanan::with(['meja', 'items.menu', 'promo'])
            ->whereIn('status_pesanan', ['menunggu', 'diproses'])
            ->orderBy('waktu_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Pesanan Siap Diambil di Meja Kasir (status = 'selesai')
        $pesananSiap = Pesanan::with(['meja', 'items.menu', 'promo'])
            ->where('status_pesanan', 'selesai')
            ->orderBy('waktu_selesai', 'asc')
            ->get();

        // 3. Riwayat Transaksi Hari Ini (selesai & diambil)
        $riwayatHariIni = Pesanan::with(['meja', 'items', 'kasir'])
            ->whereDate('waktu_masuk', Carbon::today())
            ->whereIn('status_pesanan', ['selesai', 'diambil'])
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        // 4. Metrik FIFO & Performa
        $metrics = $this->fifoService->getQueuePerformanceMetrics();
        $pengaturan = Pengaturan::get();

        return view('kasir.dashboard', compact(
            'antreanFifo',
            'pesananSiap',
            'riwayatHariIni',
            'metrics',
            'pengaturan'
        ));
    }

    /**
     * API JSON Live Polling untuk Kasir (Update antrean real-time)
     */
    public function queueApi()
    {
        $antrean = Pesanan::with(['meja', 'items'])
            ->whereIn('status_pesanan', ['menunggu', 'diproses'])
            ->orderBy('waktu_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $siapAmbil = Pesanan::with(['meja'])
            ->where('status_pesanan', 'selesai')
            ->orderBy('waktu_selesai', 'asc')
            ->get();

        $metrics = $this->fifoService->getQueuePerformanceMetrics();

        return response()->json([
            'success' => true,
            'antrean_count' => $antrean->count(),
            'siap_count' => $siapAmbil->count(),
            'total_hari_ini' => $metrics['total_hari_ini'],
            'antrean' => $antrean->map(function ($p, $index) {
                return [
                    'id' => $p->id,
                    'nomor_pesanan' => $p->nomor_pesanan,
                    'fifo_rank' => $index + 1,
                    'nama_pelanggan' => $p->nama_pelanggan,
                    'meja' => $p->meja->nomor_meja,
                    'status_pesanan' => $p->status_pesanan,
                    'status_pembayaran' => $p->status_pembayaran,
                    'waktu_masuk' => $p->waktu_masuk->format('H:i:s'),
                    'elapsed_minutes' => $p->waktu_masuk->diffInMinutes(now()),
                    'total_items' => $p->items->sum('jumlah'),
                    'total_akhir' => 'Rp ' . number_format($p->total_akhir, 0, ',', '.'),
                ];
            }),
        ]);
    }

    /**
     * Aksi 1: Proses Pesanan (Status -> diproses)
     */
    public function processOrder(Pesanan $pesanan)
    {
        if ($pesanan->status_pesanan !== 'menunggu') {
            return back()->with('error', 'Hanya pesanan berstatus menunggu yang dapat diproses.');
        }

        $pesanan->update([
            'status_pesanan' => 'diproses',
            'waktu_diproses' => Carbon::now(),
        ]);

        AktivitasLog::catat(
            'PROSES_PESANAN',
            "Kasir/Barista mulai meracik pesanan [{$pesanan->nomor_pesanan}] untuk Meja {$pesanan->meja->nomor_meja}."
        );

        return back()->with('success', "Pesanan {$pesanan->nomor_pesanan} sekarang sedang diproses oleh Barista.");
    }

    /**
     * Aksi 2: Pesanan Selesai Dibuat (Status -> selesai)
     * Memicu suara bel & notifikasi di HP pelanggan
     */
    public function completeOrder(Pesanan $pesanan)
    {
        if (!in_array($pesanan->status_pesanan, ['menunggu', 'diproses'])) {
            return back()->with('error', 'Status pesanan tidak valid untuk diselesaikan.');
        }

        $pesanan->update([
            'status_pesanan' => 'selesai',
            'waktu_selesai' => Carbon::now(),
        ]);

        AktivitasLog::catat(
            'PESANAN_SELESAI',
            "Pesanan [{$pesanan->nomor_pesanan}] selesai diracik! Pelanggan diberitahu untuk mengambil di kasir."
        );

        return back()->with('success', "Pesanan {$pesanan->nomor_pesanan} selesai! Pelanggan menerima notifikasi siap ambil.");
    }

    /**
     * Aksi 3: Pesanan Diambil Pelanggan (Status -> diambil)
     */
    public function pickupOrder(Pesanan $pesanan)
    {
        $pesanan->update([
            'status_pesanan' => 'diambil',
            'waktu_diambil' => Carbon::now(),
        ]);

        AktivitasLog::catat(
            'PESANAN_DIAMBIL',
            "Pesanan [{$pesanan->nomor_pesanan}] telah diserahkan dan diambil oleh pelanggan ({$pesanan->nama_pelanggan})."
        );

        return back()->with('success', "Pesanan {$pesanan->nomor_pesanan} berhasil diserahkan ke pelanggan.");
    }

    /**
     * Aksi 4: Pembayaran (QRIS atau Tunai -> status_pembayaran = 'lunas')
     */
    public function processPayment(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:tunai,qris',
            'jumlah_bayar' => 'required|numeric|min:' . $pesanan->total_akhir,
        ]);

        $jumlahBayar = (float) $request->jumlah_bayar;
        $kembalian = max(0, $jumlahBayar - (float) $pesanan->total_akhir);

        $pesanan->update([
            'metode_pembayaran' => $request->metode_pembayaran,
            'jumlah_bayar' => $jumlahBayar,
            'kembalian' => $kembalian,
            'status_pembayaran' => 'lunas',
            'waktu_bayar' => Carbon::now(),
            'kasir_id' => Auth::id(),
        ]);

        AktivitasLog::catat(
            'PEMBAYARAN_LUNAS',
            "Pembayaran [{$pesanan->nomor_pesanan}] LUNAS via " . strtoupper($request->metode_pembayaran) . ". Total: Rp " . number_format($pesanan->total_akhir, 0, ',', '.') . " (Bayar: Rp " . number_format($jumlahBayar, 0, ',', '.') . ")"
        );

        return back()->with('success', "Pembayaran untuk {$pesanan->nomor_pesanan} berhasil diproses (LUNAS). Kembalian: Rp " . number_format($kembalian, 0, ',', '.'));
    }

    /**
     * Aksi 5: Batalkan Pesanan jika diperlukan
     */
    public function cancelOrder(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'alasan' => 'required|string|max:255',
        ]);

        $pesanan->update([
            'status_pesanan' => 'dibatalkan',
            'status_pembayaran' => 'dibatalkan',
            'catatan_umum' => ($pesanan->catatan_umum ? $pesanan->catatan_umum . ' | ' : '') . '[DIBATALKAN: ' . $request->alasan . ']',
        ]);

        AktivitasLog::catat(
            'BATALKAN_PESANAN',
            "Pesanan [{$pesanan->nomor_pesanan}] dibatalkan oleh Kasir. Alasan: {$request->alasan}"
        );

        return back()->with('success', "Pesanan {$pesanan->nomor_pesanan} berhasil dibatalkan.");
    }

    /**
     * Cetak Struk Pembayaran / Nota Transaksi (Format Thermal 58mm/80mm)
     */
    public function printReceipt(Pesanan $pesanan)
    {
        $pesanan->load(['meja', 'items.menu', 'promo', 'kasir']);
        $pengaturan = Pengaturan::get();

        return view('kasir.receipt', compact('pesanan', 'pengaturan'));
    }

    /**
     * Kasir Quick Toggle Menu Habis/Tersedia
     */
    public function toggleMenuStock(Menu $menu)
    {
        $newStatus = $menu->status_stok === 'tersedia' ? 'habis' : 'tersedia';
        $menu->update(['status_stok' => $newStatus]);

        AktivitasLog::catat(
            'UBAH_STOK_MENU',
            "Kasir mengubah status stok menu [{$menu->nama}] menjadi: " . strtoupper($newStatus)
        );

        return back()->with('success', "Status stok menu {$menu->nama} berhasil diubah menjadi {$newStatus}.");
    }
}
