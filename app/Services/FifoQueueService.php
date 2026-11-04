<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Pengaturan;
use Carbon\Carbon;

/**
 * Service Algoritma FIFO (First In First Out)
 * 
 * Mengatur antrean pengerjaan pesanan berdasarkan waktu pemesanan (waktu_masuk).
 * Pesanan yang pertama masuk akan mendapatkan prioritas nomor 1 untuk diproses oleh barista/kasir.
 * 
 * Implementasi ini digunakan khusus untuk sistem pemesanan Sekarsa Coffee.
 */
class FifoQueueService
{
    /**
     * Generate Nomor Pesanan Harian (Contoh: A001, A002, A003 ...)
     * Format: Huruf A diikuti 3 digit nomor urut pada hari tersebut.
     */
    public function generateOrderNumber(): string
    {
        $today = Carbon::today();
        
        // Ambil pesanan terakhir hari ini
        $lastOrder = Pesanan::whereDate('waktu_masuk', $today)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastOrder || !preg_match('/^A(\d+)$/', $lastOrder->nomor_pesanan, $matches)) {
            return 'A001';
        }

        $nextNumber = ((int) $matches[1]) + 1;
        return 'A' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Mengambil seluruh antrean pesanan aktif yang diurutkan secara murni
     * menggunakan algoritma FIFO (waktu_masuk ASC).
     * 
     * Prioritas:
     * 1. Pesanan 'diproses' dan 'menunggu'
     * 2. Diurutkan berdasarkan waktu_masuk terlama terlebih dahulu.
     */
    public function getActiveFifoQueue()
    {
        return Pesanan::with(['meja', 'items.menu'])
            ->whereIn('status_pesanan', ['menunggu', 'diproses'])
            ->orderBy('waktu_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Dapatkan posisi urutan antrean FIFO untuk pesanan tertentu.
     * Mengembalikan informasi urutan ke-X dari Y pesanan aktif.
     */
    public function getQueueInfo(Pesanan $pesanan): array
    {
        $activeOrders = $this->getActiveFifoQueue();
        
        $position = 0;
        $found = false;
        $ordersAhead = [];

        foreach ($activeOrders as $index => $order) {
            $currentRank = $index + 1;
            if ($order->id === $pesanan->id) {
                $position = $currentRank;
                $found = true;
                break;
            } else {
                $ordersAhead[] = [
                    'nomor_pesanan' => $order->nomor_pesanan,
                    'status' => $order->status_pesanan,
                    'waktu_masuk' => $order->waktu_masuk->format('H:i'),
                ];
            }
        }

        $totalActive = $activeOrders->count();
        $setting = Pengaturan::get();
        $estMinutesPerOrder = $setting->estimasi_menit_per_antrean ?? 4;
        $estimatedMinutes = $position > 0 ? ($position * $estMinutesPerOrder) : 0;

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
     * Dapatkan pesanan berikutnya yang harus diproses berdasarkan FIFO.
     */
    public function getNextOrderToProcess(): ?Pesanan
    {
        return Pesanan::where('status_pesanan', 'menunggu')
            ->orderBy('waktu_masuk', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    /**
     * Hitung metrik performa antrean FIFO (Waktu tunggu rata-rata hari ini).
     * Sangat berguna untuk pengujian dan evaluasi bab 4 skripsi.
     */
    public function getQueuePerformanceMetrics(): array
    {
        $todayOrders = Pesanan::whereDate('waktu_masuk', Carbon::today())->get();

        $totalOrders = $todayOrders->count();
        $waitingOrders = $todayOrders->where('status_pesanan', 'menunggu')->count();
        $processingOrders = $todayOrders->where('status_pesanan', 'diproses')->count();
        $readyOrders = $todayOrders->where('status_pesanan', 'selesai')->count();
        $completedOrders = $todayOrders->where('status_pesanan', 'diambil')->count();

        // Hitung rata-rata waktu pengerjaan (dari masuk hingga selesai)
        $completedWithTime = $todayOrders->filter(function ($order) {
            return $order->waktu_masuk && $order->waktu_selesai;
        });

        $avgServiceTimeMinutes = 0;
        if ($completedWithTime->count() > 0) {
            $totalMinutes = $completedWithTime->reduce(function ($carry, $order) {
                return $carry + $order->waktu_masuk->diffInMinutes($order->waktu_selesai);
            }, 0);
            $avgServiceTimeMinutes = round($totalMinutes / $completedWithTime->count(), 1);
        }

        return [
            'total_hari_ini' => $totalOrders,
            'menunggu' => $waitingOrders,
            'diproses' => $processingOrders,
            'selesai_siap_ambil' => $readyOrders,
            'sudah_diambil' => $completedOrders,
            'rata_rata_waktu_layanan_menit' => $avgServiceTimeMinutes,
        ];
    }
}
