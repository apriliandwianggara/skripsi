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

        // Jumlah pesanan yang masih menunggu pembayaran kasir / QRIS (dengan auto-heal jika kolom belum ada di remote)
        $waitingPaymentsCount = 0;
        try {
            $waitingPaymentsCount = 0;
        try {
            $waitingPaymentsCount = Order::where('payment_status', 'menunggu_pembayaran')->where('status', '!=', 'dibatalkan')->count();
        } catch (\Throwable $e) {
            $waitingPaymentsCount = 0;
        }
        } catch (\Throwable $e) {
            $this->ensurePaymentColumnsExist();
            try {
                $waitingPaymentsCount = 0;
        try {
            $waitingPaymentsCount = Order::where('payment_status', 'menunggu_pembayaran')->where('status', '!=', 'dibatalkan')->count();
        } catch (\Throwable $e) {
            $waitingPaymentsCount = 0;
        }
            } catch (\Throwable $ex) {
                $waitingPaymentsCount = 0;
            }
        }

        // Temukan pesanan berstatus 'menunggu' yang paling awal masuk (Prioritas 1 FIFO)
        $firstWaitingOrder = $fifoQueue->where('status', 'menunggu')->first();
        $firstWaitingOrderId = $firstWaitingOrder?->id;
        $firstWaitingQueueNumber = $firstWaitingOrder?->queue_number;

        return view('pegawai.dashboard', compact(
            'fifoQueue',
            'readyOrders',
            'completedToday',
            'metrics',
            'setting',
            'firstWaitingOrderId',
            'firstWaitingQueueNumber',
            'waitingPaymentsCount'
        ));
    }

    /**
     * API JSON Live Polling untuk Dashboard Pegawai (Auto-sync 2.5s)
     */
    public function queueApi()
    {
        $queue = Order::with(['details.menu', 'pegawai'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $readyOrders = Order::with(['details.menu', 'pegawai'])
            ->where('status', 'siap_diambil')
            ->orderBy('ready_at', 'desc')
            ->get();

        $completedToday = Order::with(['details.menu', 'pegawai'])
            ->whereDate('received_at', Carbon::today())
            ->where('status', 'selesai')
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        $metrics = $this->fifoService->getFifoMetrics();

        // Fingerprint timestamp untuk mengecek apakah ada perubahan data
        $latestUpdate = Order::max('updated_at') ?? '0';

        // Temukan pesanan 'menunggu' yang pertama masuk (prioritas FIFO pertama)
        $firstWaitingOrder = $queue->where('status', 'menunggu')->first();
        $firstWaitingOrderId = $firstWaitingOrder?->id;
        $firstWaitingQueueNumber = $firstWaitingOrder?->queue_number;

        $waitingPaymentsCount = 0;
        try {
            $waitingPaymentsCount = Order::whereIn('payment_status', ['menunggu_pembayaran', 'menunggu_verifikasi'])->where('status', '!=', 'dibatalkan')->count();
        } catch (\Throwable $e) {
            $waitingPaymentsCount = 0;
        }

        return response()->json([
            'success' => true,
            'version' => strtotime($latestUpdate),
            'queue_count' => $queue->count(),
            'ready_count' => $readyOrders->count(),
            'waiting_payments_count' => $waitingPaymentsCount,
            'total_today' => $metrics['total_hari_ini'],
            'metrics' => $metrics,
            'queue' => $queue->map(function ($order, $index) use ($firstWaitingOrderId, $firstWaitingQueueNumber) {
                return [
                    'id' => $order->id,
                    'fifo_rank' => $index + 1,
                    'queue_number' => $order->queue_number,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'table_number' => $order->table_number,
                    'customer_notes' => $order->customer_notes,
                    'status' => $order->status,
                    'status_label' => $order->status_label,
                    'payment_method' => $order->payment_method,
                    'payment_method_label' => $order->payment_method_label,
                    'payment_status' => $order->payment_status,
                    'payment_status_label' => $order->payment_status_label,
                    'is_paid' => $order->isPaid(),
                    'is_waiting_payment' => $order->isWaitingPayment(),
                    'is_waiting_verification' => $order->isWaitingVerification(),
                    'payment_proof_url' => $order->payment_proof ? asset('storage/' . $order->payment_proof) : null,
                    'is_allowed_to_process' => ($order->status === 'menunggu') && ($order->id === $firstWaitingOrderId) && $order->isPaid(),
                    'first_waiting_queue_number' => $firstWaitingQueueNumber,
                    'received_at' => $order->received_at->format('H:i'),
                    'received_ago' => $order->received_at->diffForHumans(),
                    'total_price' => number_format($order->total_price, 0, ',', '.'),
                    'handler_name' => $order->pegawai ? $order->pegawai->name : null,
                    'items' => $order->details->map(function ($d) {
                        return [
                            'quantity' => $d->quantity,
                            'menu_name' => $d->menu_name,
                            'subtotal' => number_format($d->subtotal, 0, ',', '.'),
                            'notes' => $d->notes,
                        ];
                    }),
                ];
            }),
            'ready' => $readyOrders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'queue_number' => $order->queue_number,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'table_number' => $order->table_number,
                    'ready_at' => $order->ready_at ? $order->ready_at->format('H:i') : '-',
                    'item_summary' => $order->details->pluck('menu_name')->join(', '),
                    'total_qty' => $order->details->sum('quantity'),
                    'handler_name' => $order->pegawai ? $order->pegawai->name : null,
                ];
            }),
            'completed' => $completedToday->map(function ($order) {
                return [
                    'id' => $order->id,
                    'queue_number' => $order->queue_number,
                    'customer_name' => $order->customer_name,
                    'completed_at' => $order->completed_at ? $order->completed_at->format('H:i') : '-',
                ];
            }),
        ]);
    }

    /**
     * Konfirmasi Pembayaran Kasir (Tunai / QRIS)
     */
    public function confirmCashierPayment(Order $order, Request $request)
    {
        $user = Auth::user();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($order, $user, $request) {
            $locked = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($locked->isPaid()) {
                $msg = "Pesanan #{$locked->queue_number} atas nama {$locked->customer_name} sudah berstatus lunas sebelumnya.";
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                ]);
            }

            $locked->payment_status = 'lunas';
            $locked->paid_at = now();
            $locked->save();

            $nominalFmt = 'Rp ' . number_format($locked->total_price, 0, ',', '.');
            $methodName = $locked->payment_method === 'cash' ? 'Tunai' : 'QRIS';

            OrderStatusHistory::create([
                'order_id' => $locked->id,
                'previous_status' => $locked->status,
                'new_status' => $locked->status,
                'changed_by' => $user->name . ' (Kasir)',
                'notes' => "Kasir telah menerima pembayaran {$methodName} sebesar {$nominalFmt} dari pelanggan atas nama {$locked->customer_name}.",
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Pembayaran {$methodName} sebesar {$nominalFmt} atas nama {$locked->customer_name} berhasil dikonfirmasi lunas!",
                'order' => [
                    'id' => $locked->id,
                    'queue_number' => $locked->queue_number,
                    'customer_name' => $locked->customer_name,
                    'payment_status' => $locked->payment_status,
                    'payment_status_label' => $locked->payment_status_label,
                ]
            ]);
        });
    }

    /**
     * 5. Ubah Status Menjadi "Sedang Diproses" (Dengan Database Locking Anti-Tabrakan Kasir)
     */
    public function processOrder(Order $order, Request $request)
    {
        $user = Auth::user();

        // Mencegah race condition antar multi-kasir
        return \Illuminate\Support\Facades\DB::transaction(function () use ($order, $user, $request) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status !== 'menunggu') {
                $handlerName = $lockedOrder->pegawai ? $lockedOrder->pegawai->name : 'kasir lain';
                $msg = "Pesanan #{$lockedOrder->queue_number} sudah tidak berstatus menunggu (sedang diproses oleh {$handlerName}).";

                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            // =========================================================================
            // VALIDASI STATUS PEMBAYARAN (ANTI-KECURANGAN):
            // Barista TIDAK BISA memproses pesanan jika belum lunas / belum diverifikasi kasir.
            // =========================================================================
            if (!$lockedOrder->isPaid()) {
                $statusDesc = $lockedOrder->isWaitingVerification() 
                    ? 'sedang menunggu verifikasi mutasi QRIS kasir' 
                    : 'belum melakukan pembayaran';
                $msg = "Pesanan #{$lockedOrder->queue_number} atas nama {$lockedOrder->customer_name} belum lunas ({$statusDesc}). Kasir wajib memverifikasi pembayaran terlebih dahulu!";

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg,
                    ], 422);
                }
                return back()->with('error', $msg);
            }

            // =========================================================================
            // ATURAN KETAT FIFO (First In First Out):
            // Kasir HANYA BISA memproses pesanan yang masuk terlebih dahulu.
            // Cek apakah ada pesanan lain berstatus 'menunggu' yang masuk lebih awal.
            // =========================================================================
            $earlierWaitingOrder = Order::where('status', 'menunggu')
                ->where(function ($q) use ($lockedOrder) {
                    $q->where('received_at', '<', $lockedOrder->received_at)
                      ->orWhere(function ($sub) use ($lockedOrder) {
                          $sub->where('received_at', '=', $lockedOrder->received_at)
                              ->where('id', '<', $lockedOrder->id);
                      });
                })
                ->orderBy('received_at', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            if ($earlierWaitingOrder) {
                $msg = "Sesuai ketentuan FIFO, Anda wajib memproses pesanan Nomor Antrean [#{$earlierWaitingOrder->queue_number}] ({$earlierWaitingOrder->customer_name}) terlebih dahulu sebelum memproses pesanan #{$lockedOrder->queue_number}.";

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $msg,
                        'required_queue_number' => $earlierWaitingOrder->queue_number,
                    ], 422);
                }
                return back()->with('error', $msg);
            }

            $now = Carbon::now();
            $lockedOrder->update([
                'status' => 'diproses',
                'processed_at' => $now,
                'handled_by' => $user->id,
            ]);

            OrderStatusHistory::log(
                $lockedOrder,
                'diproses',
                "Pesanan Nomor Antrean [{$lockedOrder->queue_number}] mulai diproses oleh {$user->name}."
            );

            $msg = "Pesanan #{$lockedOrder->queue_number} ({$lockedOrder->customer_name}) berhasil diambil & sedang diproses oleh {$user->name}.";

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);
        });
    }

    /**
     * 6 & 7. Ubah Status Menjadi "Siap Diambil" & Kirim Notifikasi
     */
    public function readyOrder(Order $order, Request $request)
    {
        $user = Auth::user();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($order, $user, $request) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!in_array($lockedOrder->status, ['menunggu', 'diproses'])) {
                $msg = "Pesanan #{$lockedOrder->queue_number} sudah berstatus {$lockedOrder->status_label}.";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            $now = Carbon::now();
            $lockedOrder->update([
                'status' => 'siap_diambil',
                'ready_at' => $now,
                'handled_by' => $user->id,
            ]);

            OrderStatusHistory::log(
                $lockedOrder,
                'siap_diambil',
                "Pesanan selesai disiapkan oleh {$user->name} dan siap diambil di counter. Notifikasi otomatis dikirimkan ke pelanggan."
            );

            $notifResult = $this->notificationService->notifyOrderReady($lockedOrder);

            $msg = "Pesanan #{$lockedOrder->queue_number} ({$lockedOrder->customer_name}) SIAP DIAMBIL! Notifikasi dipicu di HP pelanggan.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $msg,
                    'whatsapp_url' => $notifResult['whatsapp_url'] ?? null
                ]);
            }

            if (!empty($notifResult['whatsapp_url'])) {
                return redirect()->back()->with('success', $msg)->with('whatsapp_url', $notifResult['whatsapp_url']);
            }

            return back()->with('success', $msg);
        });
    }

    /**
     * 8. Ubah Status Menjadi "Selesai" (Setelah Pesanan Diambil Pelanggan)
     */
    public function completeOrder(Order $order, Request $request)
    {
        $user = Auth::user();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($order, $user, $request) {
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if ($lockedOrder->status === 'selesai') {
                $msg = "Pesanan #{$lockedOrder->queue_number} sudah selesai sebelumnya.";
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 409);
                }
                return back()->with('error', $msg);
            }

            $now = Carbon::now();
            $lockedOrder->update([
                'status' => 'selesai',
                'completed_at' => $now,
                'handled_by' => $user->id,
            ]);

            OrderStatusHistory::log(
                $lockedOrder,
                'selesai',
                "Pesanan diserahkan ke pelanggan oleh {$user->name}. Seluruh alur selesai."
            );

            $msg = "Pesanan #{$lockedOrder->queue_number} telah diserahkan dan ditandai SELESAI oleh {$user->name}.";

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => $msg]);
            }
            return back()->with('success', $msg);
        });
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

    /**
     * Pastikan kolom pembayaran ada di database remote (Self-Healing InfinityFree)
     */
    private function ensurePaymentColumnsExist(): void
    {
        $sqls = [
            "ALTER TABLE `orders` ADD `payment_method` VARCHAR(20) DEFAULT 'qris' AFTER `total_price`",
            "ALTER TABLE `orders` ADD `payment_status` VARCHAR(30) DEFAULT 'menunggu_pembayaran' AFTER `payment_method`",
            "ALTER TABLE `orders` ADD `paid_at` DATETIME NULL AFTER `payment_status`",
            "ALTER TABLE `orders` ADD `payment_proof` VARCHAR(255) NULL AFTER `paid_at`",
            "ALTER TABLE `orders` ADD `payment_note` TEXT NULL AFTER `payment_proof`",
        ];

        foreach ($sqls as $sql) {
            try {
                \Illuminate\Support\Facades\DB::statement($sql);
            } catch (\Throwable $e) {
                // Abaikan jika kolom sudah ada
            }
        }
    }
}

