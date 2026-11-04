<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_pesanan',
        'meja_id',
        'nama_pelanggan',
        'catatan_umum',
        'subtotal',
        'promo_id',
        'diskon_promo',
        'total_akhir',
        'status_pesanan',
        'status_pembayaran',
        'metode_pembayaran',
        'jumlah_bayar',
        'kembalian',
        'bukti_qris',
        'waktu_masuk',
        'waktu_diproses',
        'waktu_selesai',
        'waktu_diambil',
        'waktu_bayar',
        'kasir_id',
        'session_token',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'diskon_promo' => 'decimal:2',
        'total_akhir' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'waktu_masuk' => 'datetime',
        'waktu_diproses' => 'datetime',
        'waktu_selesai' => 'datetime',
        'waktu_diambil' => 'datetime',
        'waktu_bayar' => 'datetime',
    ];

    public function meja(): BelongsTo
    {
        return $this->belongsTo(Meja::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PesananItem::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    /**
     * Hitung urutan antrean FIFO untuk pesanan ini.
     * Mengembalikan integer posisi (1 = prioritas pertama).
     */
    public function getQueuePositionAttribute(): ?int
    {
        // Hanya hitung jika pesanan masih aktif (menunggu atau diproses)
        if (!in_array($this->status_pesanan, ['menunggu', 'diproses'])) {
            return null;
        }

        $position = self::whereIn('status_pesanan', ['menunggu', 'diproses'])
            ->where('waktu_masuk', '<=', $this->waktu_masuk)
            ->where(function ($query) {
                $query->where('waktu_masuk', '<', $this->waktu_masuk)
                    ->orWhere('id', '<=', $this->id);
            })
            ->count();

        return $position;
    }

    /**
     * Estimasi waktu tunggu (menit) berdasarkan pesanan aktif di depannya.
     */
    public function getEstimatedWaitTimeMinutesAttribute(): int
    {
        $pos = $this->queue_position ?? 1;
        $setting = Pengaturan::first();
        $rate = $setting ? $setting->estimasi_menit_per_antrean : 5;
        return max(3, $pos * $rate);
    }

    /**
     * Helper status badge pesanan
     */
    public function getStatusPesananLabelAttribute(): string
    {
        return match ($this->status_pesanan) {
            'menunggu' => 'Menunggu Diproses',
            'diproses' => 'Sedang Diproses',
            'selesai' => 'Pesanan Siap Diambil',
            'diambil' => 'Sudah Diambil',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status_pesanan),
        };
    }

    /**
     * Helper status badge pembayaran
     */
    public function getStatusPembayaranLabelAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'belum_dibayar' => 'Belum Dibayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'lunas' => 'Lunas',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status_pembayaran),
        };
    }
}
