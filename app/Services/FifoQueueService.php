<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Carbon\Carbon;

/**
 * Service Algoritma FIFO (First In First Out)
 * 
 * Sesuai Judul Penelitian Skripsi:
 * "Perancangan Sistem Pemesanan Digital dan Notifikasi Pengambilan Pesanan Berbasis Web 
 *  dengan Algoritma First In First Out (FIFO) pada Sekarsa Coffee"
 * 
 * Aturan Utama Algoritma FIFO:
 * 1. Dasar pengurutan adalah waktu pesanan diterima server (`received_at`), bukan saat pelanggan memilih menu.
 * 2. Konsistensi waktu UTC / Asia/Jakarta.
 * 3. Tie-breaker: Jika waktu received_at persis sama, ID pencatatan database menentukan urutan (`received_at ASC, id ASC`).
 * 4. Pesanan dibatalkan (`dibatalkan`) otomatis keluar dari antrean aktif.
 * 5. Update status (`diproses`, `siap_diambil`, `selesai`) tidak merusak urutan antrean pesanan yang masih menunggu.
 */
class FifoQueueService
{
    /**
     * Generate Nomor Antrean Harian (001, 002, 003, ...)
     * Reset setiap hari baru.
     */
    public function generateQueueNumber(): string
    {
        $today = Carbon::today();
        
        $lastOrderToday = Order::whereDate('received_at', $today)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrderToday || !preg_match('/^(\d+)$/', $lastOrderToday->queue_number, $matches)) {
            return '001';
        }

        $nextNumber = ((int) $matches[1]) + 1;
        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Kode Pesanan Unik Sistem (Contoh: ORD-20261105-001)
     */
    public function generateOrderNumber(string $queueNumber): string
    {
        return 'ORD-' . date('Ymd') . '-' . $queueNumber;
    }

    /**
     * Mengambil seluruh antrean pesanan aktif yang diurutkan secara murni
     * menggunakan algoritma FIFO (received_at ASC, id ASC).
     * 
     * Hanya mengambil status: 'menunggu' dan 'diproses'.
     */
    public function getActiveFifoQueue()
    {
        return Order::with(['details.menu'])
            ->whereIn('status', ['menunggu', 'diproses'])
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Dapatkan rincian posisi antrean FIFO untuk pesanan tertentu.
     * Mengembalikan informasi urutan pengerjaan ke-X dari Y total pesanan aktif.
     */
    public function getQueueInfo(Order $order): array
    {
        $activeOrders = $this->getActiveFifoQueue();

        $position = 0;
        $found = false;
        $ordersAhead = [];

        foreach ($activeOrders as $index => $item) {
            $rank = $index + 1;
            if ($item->id === $order->id) {
                $position = $rank;
                $found = true;
                break;
            } else {
                $ordersAhead[] = [
                    'queue_number' => $item->queue_number,
                    'order_number' => $item->order_number,
                    'status' => $item->status,
                    'received_at' => $item->received_at->format('H:i'),
                ];
            }
        }

        $totalActive = $activeOrders->count();
        $setting = Setting::get();
        $rate = $setting->estimated_minutes_per_queue ?? 4;
        $estimatedMinutes = $position > 0 ? ($position * $rate) : 0;

        return [
            'is_in_queue' => $found,
            'posisi_antrean' => $position,
            'total_antrean' => $totalActive,
            'pesanan_di_depan' => count($ordersAhead),
            'daftar_pesanan_di_depan' => $ordersAhead,
            'estimasi_waktu_menit' => $estimatedMinutes,
            'estimasi_siap' => $position > 0 ? Carbon::now()->addMinutes($estimatedMinutes)->format('H:i') : null,
        ];
    }

    /**
     * Mengambil pesanan berikutnya yang harus dikerjakan oleh barista (prioritas pertama antrean 'menunggu').
     */
    public function getNextOrderToProcess(): ?Order
    {
        return Order::where('status', 'menunggu')
            ->orderBy('received_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Menghitung statistik dan metrik evaluasi FIFO hari ini (untuk bab 4 skripsi).
     */
    public function getFifoMetrics(): array
    {
        $todayOrders = Order::whereDate('received_at', Carbon::today())->get();

        $total = $todayOrders->count();
        $waiting = $todayOrders->where('status', 'menunggu')->count();
        $processing = $todayOrders->where('status', 'diproses')->count();
        $ready = $todayOrders->where('status', 'siap_diambil')->count();
        $completed = $todayOrders->where('status', 'selesai')->count();
        $cancelled = $todayOrders->where('status', 'dibatalkan')->count();

        // Rata-rata waktu tunggu hingga pesanan siap diambil (menit)
        $readyWithTime = $todayOrders->filter(function ($o) {
            return $o->received_at && ($o->ready_at || $o->completed_at);
        });

        $avgMinutes = 0;
        if ($readyWithTime->count() > 0) {
            $totalMins = $readyWithTime->reduce(function ($carry, $o) {
                $finishTime = $o->ready_at ?? $o->completed_at;
                return $carry + $o->received_at->diffInMinutes($finishTime);
            }, 0);
            $avgMinutes = round($totalMins / $readyWithTime->count(), 1);
        }

        return [
            'total_hari_ini' => $total,
            'menunggu' => $waiting,
            'diproses' => $processing,
            'siap_diambil' => $ready,
            'selesai' => $completed,
            'dibatalkan' => $cancelled,
            'rata_rata_waktu_layanan_menit' => $avgMinutes,
        ];
    }
}
