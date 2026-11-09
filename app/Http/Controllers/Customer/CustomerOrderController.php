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
use App\Services\QrisService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerOrderController extends Controller
{
    protected FifoQueueService $fifoService;
    protected QrisService $qrisService;

    public function __construct(FifoQueueService $fifoService, QrisService $qrisService)
    {
        $this->fifoService = $fifoService;
        $this->qrisService = $qrisService;
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
        $menuQuery = Menu::with(['category', 'variations']);

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

        // Validasi QR Gate: Cegah inject pesanan tanpa otorisasi scan QR di meja
        if ($setting->require_qr_scan && !auth()->check()) {
            $providedToken = $request->input('qr_token') ?: $request->query('token') ?: session('qr_token');
            $isTokenValid = $providedToken && hash_equals((string)$setting->qr_access_key, (string)$providedToken);
            $hasValidSession = session('qr_scanned') === true && 
                               !empty(session('qr_token')) && 
                               hash_equals((string)$setting->qr_access_key, (string)session('qr_token'));

            if (!$isTokenValid && !$hasValidSession) {
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
            'payment_method' => 'nullable|string|in:qris,cash',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.variation_id' => 'nullable|exists:menu_variations,id',
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
                $menu = Menu::with('variations')->findOrFail($item['menu_id']);
                
                if (!$menu->is_available) {
                    throw new \Exception("Maaf, sajian '{$menu->name}' saat ini sedang habis.");
                }

                $itemPrice = (float)$menu->price;
                $menuName = $menu->name;
                $variationName = null;
                $variationId = null;

                // Cek jika item menyertakan variasi
                if (!empty($item['variation_id'])) {
                    $variation = $menu->variations->firstWhere('id', $item['variation_id']);
                    if ($variation) {
                        $itemPrice = (float)$variation->price;
                        $variationName = $variation->name;
                        $variationId = $variation->id;
                        $menuName = "{$menu->name} ({$variation->name})";
                    }
                }

                $subtotal = $itemPrice * $item['quantity'];
                $totalPrice += $subtotal;

                $itemsData[] = [
                    'menu_id' => $menu->id,
                    'menu_variation_id' => $variationId,
                    'menu_name' => $menuName,
                    'variation_name' => $variationName,
                    'price' => $itemPrice,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            // Algoritma FIFO: Generate Nomor Antrean (001, 002, ...) dan Nomor Pesanan
            $queueNumber = $this->fifoService->generateQueueNumber();
            $orderNumber = $this->fifoService->generateOrderNumber($queueNumber);
            $trackingToken = Str::random(40);
            $paymentMethod = $request->input('payment_method', 'qris');

            $order = Order::create([
                'order_number' => $orderNumber,
                'queue_number' => $queueNumber,
                'customer_name' => trim($request->customer_name),
                'customer_phone' => $request->customer_phone ? trim($request->customer_phone) : null,
                'table_number' => $request->table_number ? trim($request->table_number) : null,
                'customer_notes' => $request->customer_notes,
                'total_price' => $totalPrice,
                'payment_method' => $paymentMethod,
                'payment_status' => 'menunggu_pembayaran',
                'status' => 'menunggu', // Status awal: Menunggu antrean
                'received_at' => $receivedAt,
                'tracking_token' => $trackingToken,
            ]);

            foreach ($itemsData as $detail) {
                $order->details()->create($detail);
            }

            // Catat riwayat status awal di order_status_history
            $paymentMethodLabel = $paymentMethod === 'qris' ? 'QRIS (DANA)' : 'Tunai di Kasir';
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => null,
                'new_status' => 'menunggu',
                'changed_by' => 'Sistem Pemesanan Digital',
                'notes' => "Pesanan diterima server. Metode pembayaran: {$paymentMethodLabel}. Masuk ke antrean FIFO dengan Nomor Antrean [{$queueNumber}].",
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

            $paymentUrl = route('customer.payment', $order->tracking_token);
            $confirmationUrl = route('customer.confirmation', $order->tracking_token);
            $trackingUrl = route('customer.track', $order->tracking_token);

            // Alur yang diminta user: Jika QRIS, langsung arahkan ke pembayaran QRIS
            $targetUrl = ($paymentMethod === 'qris') ? $paymentUrl : $trackingUrl;

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'queue_number' => $order->queue_number,
                'payment_method' => $paymentMethod,
                'payment_status' => $order->payment_status,
                'payment_url' => $paymentUrl,
                'confirmation_url' => $confirmationUrl,
                'tracking_url' => $trackingUrl,
                'redirect_url' => $targetUrl,
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
     * Halaman Pembayaran QRIS Otomatis Sesuai Nominal Pesanan
     */
    public function payment($token)
    {
        $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();
        $setting = Setting::get();

        // Jika pesanan sudah lunas, langsung arahkan ke pemantauan antrean
        if ($order->isPaid()) {
            return redirect()->route('customer.track', $order->tracking_token)
                ->with('info', 'Pembayaran pesanan Anda telah lunas.');
        }

        $danaNumber = $setting->qris_dana_number ?? '087811139210';
        $merchantName = $setting->qris_merchant_name ?? 'Sekarsa Coffee';
        $nmid = 'ID1026635802105';
        $danaMerchantName = 'M Aprilian Dwi Anggara, S (Sekarsa Coffee)';

        // 1. Generate QRIS Dinamis Standar Nasional dengan nominal terkunci otomatis
        $qrisPayload = $this->qrisService->generateQrisPayload(
            $order->total_price,
            $order->order_number,
            $danaNumber,
            $merchantName
        );
        $qrisDataUri = $this->qrisService->generateQrisDataUri($qrisPayload);

        // 2. Generate QRIS Statis Resmi Toko (Sebagai cadangan jika m-banking tertentu ingin input nominal manual)
        $staticPayload = $this->qrisService->getStaticQrisPayload();
        $staticQrisDataUri = $this->qrisService->generateQrisDataUri($staticPayload);

        // 3. Link & QR Berbagi Tagihan untuk Pembayaran via HP Lain (Multi-Device Sync)
        $paymentShareUrl = route('customer.payment', $order->tracking_token);
        $paymentShareQrUri = $this->qrisService->generateQrisDataUri($paymentShareUrl);

        return view('customer.payment', compact(
            'order', 'setting', 'qrisPayload', 'qrisDataUri',
            'staticPayload', 'staticQrisDataUri',
            'paymentShareUrl', 'paymentShareQrUri',
            'danaNumber', 'merchantName', 'nmid', 'danaMerchantName'
        ));
    }

    /**
     * Konfirmasi Pembayaran QRIS oleh Pelanggan (Instan & Mendukung Multi-Perangkat)
     */
    public function confirmPayment(Request $request, $token)
    {
        $order = Order::where('tracking_token', $token)->firstOrFail();

        if ($order->isPaid()) {
            return response()->json([
                'success' => true,
                'is_paid' => true,
                'message' => 'Pesanan sudah berstatus lunas.',
                'redirect_url' => route('customer.track', $order->tracking_token),
            ]);
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $request->validate([
                'payment_proof' => 'image|mimes:jpeg,png,jpg,webp|max:3072',
            ]);
            $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
        }

        // SISTEM ANTI-KECURANGAN KETAT (OPSI 2):
        // Status pembayaran diubah menjadi 'menunggu_verifikasi'.
        // Pelanggan TIDAK BISA langsung membuat pesanan menjadi lunas sendiri tanpa verifikasi kasir.
        $order->payment_status = 'menunggu_verifikasi';
        if ($proofPath) {
            $order->payment_proof = $proofPath;
        }
        if ($request->filled('payment_note')) {
            $order->payment_note = $request->payment_note;
        }
        $order->save();

        // Catat riwayat klaim pembayaran
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $order->status,
            'new_status' => $order->status,
            'changed_by' => 'Pelanggan (Konfirmasi Bayar QRIS)',
            'notes' => "Pelanggan menyatakan telah membayar QRIS Rp " . number_format($order->total_price, 0, ',', '.') . " ke DANA Bisnis. Menunggu verifikasi mutasi oleh kasir.",
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'is_paid' => false,
            'is_waiting_verification' => true,
            'message' => 'Konfirmasi pembayaran telah terkirim! Kasir akan memverifikasi mutasi masuk DANA Bisnis sebelum pesanan diracik barista.',
            'redirect_url' => route('customer.track', $order->tracking_token),
        ]);
    }

    /**
     * A.2 Halaman Konfirmasi Pesanan (Nomor Antrean Besar & Status Awal)
     */
    public function confirmation($token)
    {
        try {
            $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();
            $setting = Setting::get();
            $queueInfo = $this->fifoService->getQueueInfo($order);

            return view('customer.confirmation', compact('order', 'setting', 'queueInfo'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Confirmation page error: ' . $e->getMessage());
            $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();
            $setting = Setting::first() ?? new Setting();
            $queueInfo = [
                'is_in_queue' => true,
                'posisi_antrean' => 1,
                'total_antrean' => 1,
                'pesanan_di_depan' => 0,
                'daftar_pesanan_di_depan' => [],
                'estimasi_waktu_menit' => 4,
                'estimasi_siap' => null,
            ];
            return view('customer.confirmation', compact('order', 'setting', 'queueInfo'));
        }
    }

    /**
     * A.3 Halaman Pelacakan Antrean Real-time (Tracking Status & Algoritma FIFO)
     */
    public function track($token)
    {
        try {
            $order = Order::with(['details', 'statusHistory' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }])->where('tracking_token', $token)->firstOrFail();

            $setting = Setting::get();
            $queueInfo = $this->fifoService->getQueueInfo($order);

            return view('customer.track', compact('order', 'setting', 'queueInfo'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Track page error: ' . $e->getMessage());
            $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();
            $setting = Setting::first() ?? new Setting();
            $queueInfo = [
                'is_in_queue' => true,
                'posisi_antrean' => 1,
                'total_antrean' => 1,
                'pesanan_di_depan' => 0,
                'daftar_pesanan_di_depan' => [],
                'estimasi_waktu_menit' => 4,
                'estimasi_siap' => null,
            ];
            return view('customer.track', compact('order', 'setting', 'queueInfo'));
        }
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

        $queueInfo = $this->fifoService->getQueueInfo($order);

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'payment_method' => $order->payment_method,
            'payment_method_label' => $order->payment_method_label,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->payment_status_label,
            'is_paid' => $order->isPaid(),
            'is_waiting_payment' => $order->isWaitingPayment(),
            'is_waiting_verification' => $order->isWaitingVerification(),
            'paid_at' => $order->paid_at ? $order->paid_at->format('H:i') : null,
            'queue_number' => $order->queue_number,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total_price' => number_format($order->total_price, 0, ',', '.'),
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
