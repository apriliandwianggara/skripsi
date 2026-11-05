<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\Setting;
use App\Services\FifoQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerOrderController extends Controller
{
    protected FifoQueueService $fifoService;

    public function __construct(FifoQueueService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

    /**
     * Gerbang Pemindaian Kode QR Meja Kafe
     * Menverifikasi token QR fisik dan memberikan sesi akses ke menu.
     */
    public function scanQr(Request $request)
    {
        $setting = Setting::get();
        $token = $request->query('token');
        $table = $request->query('meja');

        // Jika QR Gate dinonaktifkan atau token valid
        if (!$setting->require_qr_scan || ($token && hash_equals((string)$setting->qr_access_key, (string)$token))) {
            session([
                'qr_scanned' => true,
                'qr_token' => $token,
                'qr_scanned_at' => now()->timestamp,
                'table_number' => $table ?: session('table_number'),
            ]);

            // Hapus cookie persisten (tidak boleh ada cookie persisten agar tidak bisa diketik manual di masa depan)
            cookie()->queue(cookie()->forget('sekarsa_qr_verified'));

            $params = [];
            if ($token) {
                $params['token'] = $token;
            }
            if ($table) {
                $params['meja'] = $table;
            }

            return redirect()->route('customer.order', $params)->with('qr_success', 'Kode QR Meja berhasil diverifikasi. Selamat memesan!');
        }

        return redirect()->route('customer.qr_required')->with('error', 'Kode QR tidak dikenali atau tidak valid. Silakan scan ulang Kode QR di meja.');
    }

    /**
     * Tampilan Khusus: Akses Ditolak Karena Belum Scan QR di Meja
     */
    public function qrRequired()
    {
        $setting = Setting::get();
        return view('customer.qr-required', compact('setting'));
    }

    /**
     * A.1 Halaman Pemesanan (Hanya dapat diakses setelah memindai QR Code di meja)
     */
    public function index(Request $request)
    {
        $setting = Setting::get();

        // Hapus cookie persisten lama jika ada di browser ponsel
        if ($request->hasCookie('sekarsa_qr_verified')) {
            cookie()->queue(cookie()->forget('sekarsa_qr_verified'));
        }

        // Pemeriksaan Gerbang Keamanan QR Code (Wajib Scan Fisik di Meja Kafe)
        if ($setting->require_qr_scan) {
            // 1. Cek jika URL membawa token QR yang valid
            $tokenParam = $request->query('token');
            $isValidToken = $tokenParam && hash_equals((string)$setting->qr_access_key, (string)$tokenParam);

            if ($isValidToken) {
                session([
                    'qr_scanned' => true,
                    'qr_token' => $tokenParam,
                    'qr_scanned_at' => now()->timestamp,
                    'table_number' => $request->query('meja') ?: session('table_number'),
                ]);
            }

            // 2. Cek apakah pengguna staf/admin yang login, atau memiliki tiket sesi aktif yang sah
            $isStaff = auth()->check();
            $hasValidSession = session('qr_scanned') === true && 
                               !empty(session('qr_token')) && 
                               hash_equals((string)$setting->qr_access_key, (string)session('qr_token'));

            // JIKA TIDAK MEMBAWA TOKEN DAN TIDAK MEMILIKI SESI AKTIF: TOLAK DAN TAMPILKAN HALAMAN SCAN QR
            if (!$isStaff && !$isValidToken && !$hasValidSession) {
                return redirect()->route('customer.qr_required');
            }
        }

        $tableParam = $request->query('meja', session('table_number'));
        $tokenParam = $request->query('token', session('qr_token'));

        $categories = Category::where('is_active', true)->orderBy('order')->get();
        $selectedCategory = $request->query('kategori');
        $search = $request->query('q');

        // Menampilkan seluruh menu aktif, menu habis tetap ditampilkan dengan label Habis
        $menuQuery = Menu::with('category');

        if ($selectedCategory) {
            $menuQuery->whereHas('category', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory);
            });
        }

        if ($search) {
            $menuQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $menus = $menuQuery->orderBy('category_id')->orderBy('name')->get();

        return view('customer.order', compact('categories', 'menus', 'selectedCategory', 'search', 'tableParam', 'tokenParam', 'setting'));
    }

    /**
     * API Real-time Cek Ketersediaan Menu
     */
    public function menuStatusApi()
    {
        $menus = Menu::select('id', 'name', 'is_available', 'updated_at')->get();
        $latestUpdate = Menu::max('updated_at');
        $version = $latestUpdate ? Carbon::parse($latestUpdate)->timestamp : time();

        return response()->json([
            'success' => true,
            'version' => $version,
            'menus' => $menus,
        ]);
    }

    /**
     * API Verifikasi Lokasi (Bypass selalu valid karena GPS telah dihapus)
     */
    public function verifyLocationApi(Request $request)
    {
        return response()->json([
            'success' => true,
            'within_radius' => true,
            'formatted_distance' => '0 meter (Di Meja Kafe)',
            'max_radius_meters' => 500,
            'tolerance_meters' => 50,
        ]);
    }

    /**
     * A.1 Proses Pengiriman Pesanan oleh Pelanggan
     * Algoritma FIFO: Memberi nomor antrean berurutan secara transparan
     * TIKET QR HANGUS SETELAH INI: Pelanggan yang ingin pesan lagi wajib scan QR lagi.
     */
    public function submitOrder(Request $request)
    {
        $setting = Setting::get();

        // Validasi QR Gate: Cegah inject pesanan tanpa sesi scan QR
        if ($setting->require_qr_scan && !auth()->check()) {
            $hasValidSession = session('qr_scanned') === true && 
                               !empty(session('qr_token')) && 
                               hash_equals((string)$setting->qr_access_key, (string)session('qr_token'));

            if (!$hasValidSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi pemesanan Anda telah berakhir. Silakan scan ulang Kode QR di meja untuk memesan.',
                    'redirect_url' => route('customer.qr_required')
                ], 403);
            }
        }

        $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'table_number' => 'nullable|string|max:20',
            'customer_notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1|max:50',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Waktu pesanan diterima server dicatat secara konsisten (Aturan FIFO #1)
            $receivedAt = Carbon::now();

            $totalPrice = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);
                
                if (!$menu->is_available) {
                    throw new \Exception("Maaf, sajian '{$menu->name}' saat ini sedang habis.");
                }

                $subtotal = $menu->price * $item['quantity'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Algoritma FIFO: Generate Nomor Antrean (001, 002, ...) dan Nomor Pesanan
            $queueNumber = $this->fifoService->generateQueueNumber();
            $orderNumber = $this->fifoService->generateOrderNumber($queueNumber);
            $trackingToken = Str::random(40);

            $order = Order::create([
                'order_number' => $orderNumber,
                'queue_number' => $queueNumber,
                'customer_name' => trim($request->customer_name),
                'customer_phone' => $request->customer_phone ? trim($request->customer_phone) : null,
                'table_number' => $request->table_number ? trim($request->table_number) : null,
                'customer_notes' => $request->customer_notes,
                'total_price' => $totalPrice,
                'status' => 'menunggu', // Status awal: Menunggu antrean
                'received_at' => $receivedAt,
                'tracking_token' => $trackingToken,
            ]);

            foreach ($itemsData as $detail) {
                $order->details()->create($detail);
            }

            // Catat riwayat status awal di order_status_history
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => null,
                'new_status' => 'menunggu',
                'changed_by' => 'Sistem Pemesanan Digital',
                'notes' => "Pesanan diterima server. Masuk ke antrean FIFO dengan Nomor Antrean [{$queueNumber}].",
                'created_at' => $receivedAt,
            ]);

            DB::commit();

            // =========================================================================
            // HANGUSKAN TIKET SCAN QR SEKETIKA:
            // Sesi dan token langsung dihapus dari server dan browser,
            // sehingga jika pelanggan mengetik manual /pesan atau ingin memesan lagi,
            // WAJIB memindai Kode QR di meja kembali.
            // =========================================================================
            session()->forget(['qr_scanned', 'qr_token', 'qr_scanned_at', 'table_number']);
            cookie()->queue(cookie()->forget('sekarsa_qr_verified'));

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'queue_number' => $order->queue_number,
                'confirmation_url' => route('customer.confirmation', $order->tracking_token),
                'tracking_url' => route('customer.track', $order->tracking_token),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * A.2 Halaman Konfirmasi Pesanan (Nomor Antrean Besar & Status Awal)
     */
    public function confirmation($token)
    {
        $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();
        $setting = Setting::get();
        $queueInfo = $this->fifoService->calculateQueueInfo($order);

        return view('customer.confirmation', compact('order', 'setting', 'queueInfo'));
    }

    /**
     * A.3 Halaman Pelacakan Antrean Real-time (Tracking Status & Algoritma FIFO)
     */
    public function track($token)
    {
        $order = Order::with(['details', 'statusHistory' => function ($q) {
            $q->orderBy('created_at', 'asc');
        }])->where('tracking_token', $token)->firstOrFail();

        $setting = Setting::get();
        $queueInfo = $this->fifoService->calculateQueueInfo($order);

        return view('customer.track', compact('order', 'setting', 'queueInfo'));
    }

    /**
     * Endpoint API untuk Polling Real-time Status Pesanan
     */
    public function trackStatusApi($token)
    {
        $order = Order::where('tracking_token', $token)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $queueInfo = $this->fifoService->calculateQueueInfo($order);

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'queue_number' => $order->queue_number,
            'order_number' => $order->order_number,
            'pesanan_di_depan' => $queueInfo['pesanan_di_depan'],
            'estimasi_waktu_menit' => $queueInfo['estimasi_waktu_menit'],
            'history' => $order->statusHistory()->orderBy('created_at', 'asc')->get()->map(function ($h) {
                return [
                    'status' => $h->new_status,
                    'notes' => $h->notes,
                    'time' => $h->created_at->format('H:i:s'),
                    'by' => $h->changed_by,
                ];
            }),
        ]);
    }
}
