<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'total_orders',
        'rewards_claimed',
        'last_ordered_at',
    ];

    protected $casts = [
        'total_orders' => 'integer',
        'rewards_claimed' => 'integer',
        'last_ordered_at' => 'datetime',
    ];

    /**
     * Relasi ke pesanan berdasarkan nomor telepon
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_phone', 'phone')->latest('id');
    }

    /**
     * Normalisasi nomor telepon standar (08xxx)
     */
    public static function normalizePhone(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Hapus karakter selain angka
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali 62, ubah jadi 0
        if (str_starts_with($clean, '62')) {
            $clean = '0' . substr($clean, 2);
        }

        return $clean;
    }

    /**
     * Menghitung posisi urutan pemesanan dalam siklus 10 pesanan (1 s/d 10)
     */
    public function getCurrentCycleOrder(): int
    {
        $cycle = $this->total_orders % 10;
        return $cycle === 0 && $this->total_orders > 0 ? 10 : $cycle;
    }

    /**
     * Cek apakah pelanggan berhak memilih menu gratis (sudah 9 kali memesan sebelumnya, pesanan saat ini adalah ke-10)
     */
    public function isEligibleForReward(): bool
    {
        return ($this->total_orders % 10) === 9;
    }

    /**
     * Jumlah pesanan yang tersisa untuk mendapatkan reward gratis
     */
    public function getOrdersRemaining(): int
    {
        $cycle = $this->total_orders % 10;
        return 10 - $cycle;
    }
}
