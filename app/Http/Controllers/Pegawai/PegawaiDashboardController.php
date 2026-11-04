<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Setting;
use App\Services\FifoQueueService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PegawaiDashboardController extends Controller
{
    protected FifoQueueService $fifoService;
    protected NotificationService $notificationService;

    public function __construct(FifoQueueService $fifoService, NotificationService $notificationService)
    {
        $this->fifoService = $fifoService;
        $this->notificationService = $notificationService;
    }

    /**
     * C. Dashboard Pegawai / Barista (Pusat Operasional FIFO)
     */
    public function dashboard()
    {
        // 1. Antrean FIFO: Menunggu & Sedang Diproses (Diurutkan KETAT berdasarkan received_at ASC, id ASC)
        $fifoQueue = Order::with(['details.menu'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 2. Pesanan Siap Diambil di Counter
        $readyOrders = Order::with(['details.menu', 'notifications'])
            ->where('status', 'siap_diambil')
            ->orderBy('ready_at', 'desc')
            ->get();

        // 3. Riwayat Pesanan Selesai Hari Ini
        $completedToday = Order::with(['details.menu', 'pegawai'])
            ->whereDate('received_at', Carbon::today())
            ->where('status', 'selesai')
            ->orderBy('completed_at', 'desc')
            ->take(15)
            ->get();

        $metrics = $this->fifoService->getFifoMetrics();
        $setting = Setting::get();

        return view('pegawai.dashboard', compact(
            'fifoQueue',
            'readyOrders',
            'completedToday',
            'metrics',
            'setting'
        ));
    }

    /**
     * API JSON Live Polling untuk Dashboard Pegawai (Auto-sync 4s)
     */
    public function queueApi()
    {
        $queue = Order::with(['details.menu'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $readyCount = Order::where('status', 'siap_diambil')->count();
        $metrics = $this->fifoService->getFifoMetrics();

        return response()->json([
            'success' => true,
            'queue_count' => $queue->count(),
            'ready_count' => $readyCount,
            'total_today' => $metrics['total_hari_ini'],
            'orders' => $queue->map(function ($order, $index) {
                return [
                    'id' => $order->id,
                    'fifo_rank' => $index + 1,
                    'queue_number' => $order->queue_number,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'table_number' => $order->table_number,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'received_at' => $order->received_at->format('H:i:s'),
                    'elapsed_minutes' => $order->received_at->diffInMinutes(now()),
                    'total_items' => $order->details->sum('quantity'),
                    'total_price' => 'Rp ' . number_format($order->total_price, 0, ',', '.'),
                ];
            }),
        ]);
    }

    /**
     * 5. Ubah Status Menjadi "Sedang Diproses"
     */
    public function processOrder(Order $order)
    {
        if ($order->status !== 'menunggu') {
            return back()->with('error', 'Hanya pesanan yang menunggu antrean yang dapat diproses.');
        }

        $now = Carbon::now();
        $order->update([
            'status' => 'diproses',
            'processed_at' => $now,
            'handled_by' => Auth::id(),
        ]);

        OrderStatusHistory::log(
            $order,
            'diproses',
            "Pegawai/Barista mulai meracik pesanan Nomor Antrean [{$order->queue_number}] sesuai urutan FIFO."
        );

        return back()->with('success', "Pesanan #{$order->queue_number} ({$order->customer_name}) sekarang SEDANG DIPROSES oleh barista.");
    }

    /**
     * 6 & 7. Ubah Status Menjadi "Siap Diambil" & Kirim Notifikasi
     */
    public function readyOrder(Order $order)
    {
        if (!in_array($order->status, ['menunggu', 'diproses'])) {
            return back()->with('error', 'Status pesanan tidak valid untuk diubah ke siap diambil.');
        }

        $now = Carbon::now();
        $order->update([
            'status' => 'siap_diambil',
            'ready_at' => $now,
            'handled_by' => Auth::id(),
        ]);

        // Catat riwayat status
        OrderStatusHistory::log(
            $order,
            'siap_diambil',
            "Pesanan telah selesai dibuat dan siap diambil di counter. Notifikasi otomatis dikirimkan ke pelanggan."
        );

        // Kirim notifikasi sistem (Web Chime & Log WhatsApp)
        $notifResult = $this->notificationService->notifyOrderReady($order);

        $msg = "Pesanan #{$order->queue_number} ({$order->customer_name}) SIAP DIAMBIL! Notifikasi suara bel telah dipicu di HP pelanggan.";
        
        if (!empty($notifResult['whatsapp_url'])) {
            return redirect()->back()->with('success', $msg)->with('whatsapp_url', $notifResult['whatsapp_url']);
        }

        return back()->with('success', $msg);
    }

    /**
     * 8. Ubah Status Menjadi "Selesai" (Setelah Pesanan Diambil Pelanggan)
     */
    public function completeOrder(Order $order)
    {
        $now = Carbon::now();
        $order->update([
            'status' => 'selesai',
            'completed_at' => $now,
            'handled_by' => Auth::id(),
        ]);

        OrderStatusHistory::log(
            $order,
            'selesai',
            "Pelanggan telah mengambil pesanan di counter. Seluruh proses pesanan selesai."
        );

        return back()->with('success', "Pesanan #{$order->queue_number} telah diserahkan dan ditandai SELESAI.");
    }

    /**
     * Batalkan Pesanan jika Diperlukan
     */
    public function cancelOrder(Request $request, Order $order)
    {
        $request->validate(['reason' => 'required|string|max:255']);

        $order->update([
            'status' => 'dibatalkan',
            'customer_notes' => ($order->customer_notes ? $order->customer_notes . ' | ' : '') . '[DIBATALKAN: ' . $request->reason . ']',
        ]);

        OrderStatusHistory::log(
            $order,
            'dibatalkan',
            "Pesanan dibatalkan. Alasan: " . $request->reason
        );

        return back()->with('success', "Pesanan #{$order->queue_number} berhasil dibatalkan dan dikeluarkan dari antrean FIFO.");
    }

    /**
     * Kirim Pesan Notifikasi WhatsApp Langsung
     */
    public function sendWhatsApp(Order $order)
    {
        $setting = Setting::get();
        $message = $this->notificationService->composeWhatsAppMessage($order, $setting);
        $phone = $this->notificationService->formatIndonesianPhone($order->customer_phone);

        $url = 'https://api.whatsapp.com/send?phone=' . $phone . '&text=' . rawurlencode($message);
        return redirect()->away($url);
    }
}

