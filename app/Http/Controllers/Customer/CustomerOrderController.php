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
                'qr_scanned_at' => now()->timestamp,
                'table_number' => $table ?: session('table_number'),
            ]);

            // Set cookie valid selama 4 jam (240 menit)
            cookie()->queue('sekarsa_qr_verified', $setting->qr_access_key, 240);

            $params = [];
            if ($table) {
                $params['meja'] = $table;
            }
            if ($token) {
                $params['token'] = $token;
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
     * A.1 Halaman Pemesanan (Dibuka setelah memindai QR Code)
     */
    public function index(Request $request)
    {
        $setting = Setting::get();

        // Pemeriksaan Gerbang Keamanan QR Code
        if ($setting->require_qr_scan) {
            // 1. Cek jika query membawa token QR valid secara langsung
            $tokenParam = $request->query('token');
            if ($tokenParam && hash_equals((string)$setting->qr_access_key, (string)$tokenParam)) {
                session([
                    'qr_scanned' => true,
                    'qr_scanned_at' => now()->timestamp,
                    'table_number' => $request->query('meja') ?: session('table_number'),
                ]);
                cookie()->queue('sekarsa_qr_verified', $setting->qr_access_key, 240);
            }

            // 2. Cek apakah pengguna staf/admin yang login, atau memiliki session/cookie valid
            $isStaff = auth()->check();
            $hasValidSession = session('qr_scanned') === true;
            $hasValidCookie = $request->cookie('sekarsa_qr_verified') === $setting->qr_access_key;

            if (!$isStaff && !$hasValidSession && !$hasValidCookie) {
                return redirect()->route('customer.qr_required');
            }
        }

        $tableParam = $request->query('meja', session('table_number'));

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

        $menus = $menuQuery->orderBy('order')->orderBy('name')->get();

        return view('customer.order', compact(
            'categories',
            'menus',
            'selectedCategory',
            'search',
            'setting',
            'tableParam'
        ));
    }

    /**
     * Submit Pesanan ke Server (Dasar Waktu Masuk Algoritma FIFO)
     */
    public function submitOrder(Request $request)
    {
        $setting = Setting::get();

        // Validasi proteksi QR Gate saat pengiriman pesanan
        if ($setting->require_qr_scan) {
            $isStaff = auth()->check();
            $hasValidSession = session('qr_scanned') === true;
            $hasValidCookie = $request->cookie('sekarsa_qr_verified') === $setting->qr_access_key;

            if (!$isStaff && !$hasValidSession && !$hasValidCookie) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemesanan ditolak: Anda harus memindai Kode QR resmi di meja kafe untuk melakukan pemesanan.'
                ], 403);
            }
        }

        // Validasi Proteksi Lokasi GPS Kafe (Geofencing Radius)
        if (!$setting->demo_mode_enabled && $setting->enable_gps_radius && !auth()->check()) {
            $lat = $request->input('customer_lat');
            $lng = $request->input('customer_lng');
            $accuracy = $request->input('customer_accuracy');

            if ($lat === null || $lng === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Izin lokasi GPS ponsel diperlukan untuk memastikan Anda berada di area Sekarsa Coffee. Harap aktifkan GPS ponsel Anda.'
                ], 422);
            }

            $accuracyFloat = is_numeric($accuracy) ? (float)$accuracy : null;
            if (!$setting->isWithinCafeRadius((float)$lat, (float)$lng, $accuracyFloat)) {
                $distance = $setting->calculateDistanceToCafe((float)$lat, (float)$lng);
                $formattedDist = $distance >= 1000 ? round($distance / 1000, 2) . ' km' : round($distance) . ' meter';
                $maxRadius = (int)$setting->cafe_radius_meters + (int)($setting->gps_tolerance_meters ?? 50);

                return response()->json([
                    'success' => false,
                    'message' => "Pemesanan ditolak: Anda terdeteksi berada di luar area kafe (Jarak: {$formattedDist}, batas toleransi: {$maxRadius} meter). Pastikan Anda berada di area Sekarsa Coffee dan GPS berakurasi tinggi aktif."
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

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'queue_number' => $order->queue_number,
                'confirmation_url' => route('customer.confirmation', $order->tracking_token),
                'tracking_url' => route('customer.track', $order->tracking_token),
                'message' => "Pesanan Anda berhasil dikirim dengan Nomor Antrean #{$queueNumber}!",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * A.2 Halaman Konfirmasi Pesanan
     */
    public function confirmation(string $tracking_token)
    {
        $order = Order::with(['details.menu'])
            ->where('tracking_token', $tracking_token)
            ->firstOrFail();

        $queueInfo = $this->fifoService->getQueueInfo($order);
        $setting = Setting::get();

        return view('customer.confirmation', compact('order', 'queueInfo', 'setting'));
    }

    /**
     * A.3 Halaman Pelacakan Pesanan (Live Tracking & Notifikasi Suara)
     */
    public function track(string $tracking_token)
    {
        $order = Order::with(['details.menu', 'statusHistory'])
            ->where('tracking_token', $tracking_token)
            ->firstOrFail();

        $queueInfo = $this->fifoService->getQueueInfo($order);
        $setting = Setting::get();

        return view('customer.track', compact('order', 'queueInfo', 'setting'));
    }

    /**
     * A.4 Endpoint API JSON Polling Real-time Status HP Pelanggan
     */
    public function trackStatusApi(string $tracking_token)
    {
        $order = Order::where('tracking_token', $tracking_token)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $queueInfo = $this->fifoService->getQueueInfo($order);

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'queue_number' => $order->queue_number,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'posisi_antrean' => $queueInfo['posisi_antrean'],
            'total_antrean' => $queueInfo['total_antrean'],
            'pesanan_di_depan' => $queueInfo['pesanan_di_depan'],
            'estimasi_waktu_menit' => $queueInfo['estimasi_waktu_menit'],
            'estimasi_siap' => $queueInfo['estimasi_siap'],
            'received_at' => $order->received_at ? $order->received_at->format('H:i') : null,
            'processed_at' => $order->processed_at ? $order->processed_at->format('H:i') : null,
            'ready_at' => $order->ready_at ? $order->ready_at->format('H:i') : null,
            'completed_at' => $order->completed_at ? $order->completed_at->format('H:i') : null,
        ]);
    }

    /**
     * Endpoint API Status Ketersediaan Menu Real-Time (Jika Menu Habis di Kasir)
     */
    public function menuStatusApi()
    {
        $menus = Menu::select('id', 'name', 'is_available', 'updated_at')->get();
        $latestUpdate = Menu::max('updated_at') ?? '0';

        return response()->json([
            'success' => true,
            'version' => strtotime($latestUpdate),
            'menus' => $menus->map(function ($m) {
                return [
                    'id' => $m->id,
                    'is_available' => (bool) $m->is_available,
                ];
            }),
        ]);
    }

    /**
     * Endpoint API Verifikasi Lokasi GPS Pelanggan (Geofencing Radius)
     */
    public function verifyLocationApi(Request $request)
    {
        $setting = Setting::get();

        // Mode Demo Sidang Skripsi: Otomatis valid 100% tanpa risiko sinyal satelit drop di ruang sidang
        if ($setting->demo_mode_enabled) {
            return response()->json([
                'enabled' => true,
                'demo_mode' => true,
                'within_radius' => true,
                'distance_meters' => 0,
                'formatted_distance' => '0 meter',
                'max_radius_meters' => $setting->cafe_radius_meters,
                'tolerance_meters' => $setting->gps_tolerance_meters ?? 50,
                'message' => 'Mode Demo Sidang Skripsi: Lokasi otomatis terverifikasi di Sekarsa Coffee (0 meter)',
            ]);
        }

        if (!$setting->enable_gps_radius) {
            return response()->json([
                'enabled' => false,
                'demo_mode' => false,
                'within_radius' => true,
                'message' => 'Proteksi radius GPS tidak aktif.',
            ]);
        }

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $accuracy = $request->input('accuracy');

        if ($lat === null || $lng === null) {
            return response()->json([
                'enabled' => true,
                'within_radius' => false,
                'message' => 'Koordinat GPS tidak diterima.',
            ], 422);
        }

        $accuracyFloat = is_numeric($accuracy) ? (float)$accuracy : null;
        $distance = $setting->calculateDistanceToCafe((float)$lat, (float)$lng);
        $withinRadius = $setting->isWithinCafeRadius((float)$lat, (float)$lng, $accuracyFloat);
        $formattedDist = $distance >= 1000 ? round($distance / 1000, 2) . ' km' : round($distance) . ' meter';
        $maxAllowed = (int)$setting->cafe_radius_meters + (int)($setting->gps_tolerance_meters ?? 50);

        return response()->json([
            'enabled' => true,
            'within_radius' => $withinRadius,
            'distance_meters' => $distance,
            'formatted_distance' => $formattedDist,
            'max_radius_meters' => $setting->cafe_radius_meters,
            'tolerance_meters' => $setting->gps_tolerance_meters ?? 50,
            'message' => $withinRadius
                ? "Lokasi terverifikasi di Sekarsa Coffee (Jarak: {$formattedDist})"
                : "Anda berada di luar area kafe (Jarak: {$formattedDist}, batas toleransi: {$maxAllowed} m)",
        ]);
    }
}

