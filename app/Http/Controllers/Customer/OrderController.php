<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Menu;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\Promo;
use App\Services\FifoQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected FifoQueueService $fifoService;

    public function __construct(FifoQueueService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

    /**
     * Halaman Utama Menu Pelanggan (Scan QR Code)
     */
    public function index(Request $request)
    {
        $nomorMeja = $request->query('meja') ?? session('nomor_meja');
        $token = $request->query('token');

        $activeMeja = null;
        if ($nomorMeja) {
            $query = Meja::where('nomor_meja', $nomorMeja);
            if ($token) {
                // If token provided, verify or update
                $mejaCheck = (clone $query)->where('qr_token', $token)->first();
                if ($mejaCheck) {
                    $activeMeja = $mejaCheck;
                }
            }
            if (!$activeMeja) {
                $activeMeja = $query->first();
            }

            if ($activeMeja) {
                session(['meja_id' => $activeMeja->id, 'nomor_meja' => $activeMeja->nomor_meja]);
            }
        }

        $allMejas = Meja::orderBy('nomor_meja')->get();
        $kategoris = Kategori::where('is_active', true)->orderBy('urutan')->get();
        $promos = Promo::where('is_active', true)
            ->where(function ($q) {
                $today = Carbon::today();
                $q->whereNull('tgl_selesai')->orWhere('tgl_selesai', '>=', $today);
            })
            ->get();

        $selectedKategori = $request->query('kategori');
        $search = $request->query('q');

        $menuQuery = Menu::with('kategori')
            ->where('status_stok', 'tersedia');

        if ($selectedKategori) {
            $menuQuery->whereHas('kategori', function ($q) use ($selectedKategori) {
                $q->where('slug', $selectedKategori);
            });
        }

        if ($search) {
            $menuQuery->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $menus = $menuQuery->orderBy('urutan')->orderBy('nama')->get();
        $rekomendasiMenus = Menu::where('status_stok', 'tersedia')
            ->where('is_rekomendasi', true)
            ->take(4)
            ->get();

        $pengaturan = Pengaturan::get();

        return view('customer.index', compact(
            'activeMeja',
            'allMejas',
            'kategoris',
            'promos',
            'menus',
            'rekomendasiMenus',
            'selectedKategori',
            'search',
            'pengaturan'
        ));
    }

    /**
     * Set nomor meja secara manual dari pilihan pelanggan jika tidak melalui QR
     */
    public function setMeja(Request $request)
    {
        $request->validate([
            'meja_id' => 'required|exists:mejas,id',
        ]);

        $meja = Meja::findOrFail($request->meja_id);
        session(['meja_id' => $meja->id, 'nomor_meja' => $meja->nomor_meja]);

        return redirect()->route('customer.order', ['meja' => $meja->nomor_meja])
            ->with('success', "Anda memilih Meja {$meja->nomor_meja}. Selamat memesan!");
    }

    /**
     * Validasi kode promo via AJAX
     */
    public function checkPromo(Request $request)
    {
        $request->validate([
            'kode_promo' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $promo = Promo::where('kode_promo', strtoupper(trim($request->kode_promo)))->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak ditemukan.',
            ], 404);
        }

        if (!$promo->isValid((float) $request->subtotal)) {
            $minFormatted = 'Rp ' . number_format($promo->min_pembelian, 0, ',', '.');
            return response()->json([
                'success' => false,
                'message' => "Kode promo tidak berlaku. Minimal pembelian adalah {$minFormatted}.",
            ], 422);
        }

        $diskon = $promo->calculateDiscount((float) $request->subtotal);

        return response()->json([
            'success' => true,
            'promo_id' => $promo->id,
            'kode_promo' => $promo->kode_promo,
            'judul' => $promo->judul,
            'diskon' => $diskon,
            'diskon_formatted' => 'Rp ' . number_format($diskon, 0, ',', '.'),
            'total_setelah_diskon' => max(0, $request->subtotal - $diskon),
            'message' => "Promo berhasil diterapkan! Potongan: Rp " . number_format($diskon, 0, ',', '.'),
        ]);
    }

    /**
     * Submit Pesanan Pelanggan (Masuk Antrean FIFO)
     */
    public function submitOrder(Request $request)
    {
        $request->validate([
            'meja_id' => 'required|exists:mejas,id',
            'nama_pelanggan' => 'required|string|max:100',
            'catatan_umum' => 'nullable|string|max:500',
            'kode_promo' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1|max:50',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $meja = Meja::findOrFail($request->meja_id);

            // Hitung subtotal dan siapkan item
            $subtotal = 0;
            $itemsData = [];

            foreach ($request->items as $itemInput) {
                $menu = Menu::findOrFail($itemInput['menu_id']);
                
                if ($menu->status_stok !== 'tersedia') {
                    throw new \Exception("Maaf, menu {$menu->nama} saat ini sedang habis.");
                }

                $harga = $menu->harga_aktif;
                $itemSubtotal = $harga * $itemInput['jumlah'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'menu_id' => $menu->id,
                    'nama_menu' => $menu->nama,
                    'harga_satuan' => $harga,
                    'jumlah' => $itemInput['jumlah'],
                    'subtotal' => $itemSubtotal,
                    'catatan' => $itemInput['catatan'] ?? null,
                ];
            }

            // Validasi Promo jika ada
            $promoId = null;
            $diskon = 0;
            if ($request->filled('kode_promo')) {
                $promo = Promo::where('kode_promo', strtoupper(trim($request->kode_promo)))->first();
                if ($promo && $promo->isValid($subtotal)) {
                    $promoId = $promo->id;
                    $diskon = $promo->calculateDiscount($subtotal);
                }
            }

            $totalAkhir = max(0, $subtotal - $diskon);

            // Generate Nomor Pesanan FIFO (A001, A002, ...)
            $nomorPesanan = $this->fifoService->generateOrderNumber();
            $sessionToken = Str::random(40);
            $waktuMasuk = Carbon::now();

            $pesanan = Pesanan::create([
                'nomor_pesanan' => $nomorPesanan,
                'meja_id' => $meja->id,
                'nama_pelanggan' => trim($request->nama_pelanggan),
                'catatan_umum' => $request->catatan_umum,
                'subtotal' => $subtotal,
                'promo_id' => $promoId,
                'diskon_promo' => $diskon,
                'total_akhir' => $totalAkhir,
                'status_pesanan' => 'menunggu', // Alur 3: Pesanan masuk antrean FIFO
                'status_pembayaran' => 'belum_dibayar',
                'metode_pembayaran' => null,
                'waktu_masuk' => $waktuMasuk,
                'session_token' => $sessionToken,
            ]);

            foreach ($itemsData as $item) {
                $pesanan->items()->create($item);
            }

            // Update status meja menjadi terisi
            $meja->update(['status' => 'terisi']);

            // Catat log
            AktivitasLog::catat(
                'PESANAN_BARU',
                "Pesanan baru masuk [{$nomorPesanan}] atas nama {$pesanan->nama_pelanggan} di Meja {$meja->nomor_meja}. Total: Rp " . number_format($totalAkhir, 0, ',', '.')
            );

            DB::commit();

            // Simpan session tracking
            session([
                'last_order_id' => $pesanan->id,
                'last_order_number' => $pesanan->nomor_pesanan,
                'last_order_token' => $sessionToken,
            ]);

            return response()->json([
                'success' => true,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'redirect_url' => route('customer.track', ['nomor_pesanan' => $pesanan->nomor_pesanan, 'token' => $sessionToken]),
                'message' => 'Pesanan Anda berhasil dikirim dan masuk ke antrean pengerjaan barista!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Halaman Live Pelacakan Status Pesanan Pelanggan (Step 5 & Step 7 Alur)
     */
    public function trackOrder(Request $request, string $nomor_pesanan)
    {
        $pesanan = Pesanan::with(['meja', 'items.menu', 'promo'])
            ->where('nomor_pesanan', $nomor_pesanan)
            ->firstOrFail();

        $queueInfo = $this->fifoService->getQueueInfo($pesanan);
        $pengaturan = Pengaturan::get();

        return view('customer.track', compact('pesanan', 'queueInfo', 'pengaturan'));
    }

    /**
     * Endpoint API JSON untuk Polling Status Real-time HP Pelanggan
     */
    public function statusApi(string $nomor_pesanan)
    {
        $pesanan = Pesanan::with(['meja'])
            ->where('nomor_pesanan', $nomor_pesanan)
            ->first();

        if (!$pesanan) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $queueInfo = $this->fifoService->getQueueInfo($pesanan);

        return response()->json([
            'success' => true,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'status_pesanan' => $pesanan->status_pesanan,
            'status_pesanan_label' => $pesanan->status_pesanan_label,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'status_pembayaran_label' => $pesanan->status_pembayaran_label,
            'metode_pembayaran' => $pesanan->metode_pembayaran,
            'posisi_antrean' => $queueInfo['posisi_antrean'],
            'total_antrean' => $queueInfo['total_antrean'],
            'pesanan_di_depan' => $queueInfo['pesanan_di_depan'],
            'estimasi_waktu_menit' => $queueInfo['estimasi_waktu_menit'],
            'estimasi_siap' => $queueInfo['estimasi_siap'],
            'waktu_masuk' => $pesanan->waktu_masuk ? $pesanan->waktu_masuk->format('H:i') : null,
            'waktu_diproses' => $pesanan->waktu_diproses ? $pesanan->waktu_diproses->format('H:i') : null,
            'waktu_selesai' => $pesanan->waktu_selesai ? $pesanan->waktu_selesai->format('H:i') : null,
            'waktu_diambil' => $pesanan->waktu_diambil ? $pesanan->waktu_diambil->format('H:i') : null,
            'total_akhir_formatted' => 'Rp ' . number_format($pesanan->total_akhir, 0, ',', '.'),
        ]);
    }

    /**
     * Upload Bukti Pembayaran QRIS dari HP Pelanggan
     */
    public function uploadQris(Request $request, string $nomor_pesanan)
    {
        $request->validate([
            'bukti_qris' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $pesanan = Pesanan::where('nomor_pesanan', $nomor_pesanan)->firstOrFail();

        $file = $request->file('bukti_qris');
        $fileName = 'qris_' . $pesanan->nomor_pesanan . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('qris_proofs', $fileName, 'public');

        $pesanan->update([
            'bukti_qris' => $path,
            'metode_pembayaran' => 'qris',
            'status_pembayaran' => 'menunggu_konfirmasi',
        ]);

        AktivitasLog::catat(
            'UPLOAD_BUKTI_QRIS',
            "Pelanggan mengunggah bukti pembayaran QRIS untuk pesanan [{$pesanan->nomor_pesanan}]."
        );

        return back()->with('success', 'Bukti pembayaran QRIS berhasil diunggah! Kasir akan segera memverifikasi.');
    }
}
