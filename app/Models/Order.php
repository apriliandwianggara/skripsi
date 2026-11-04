<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'queue_number',
        'customer_name',
        'customer_phone',
        'table_number',
        'customer_notes',
        'total_price',
        'status',
        'received_at',
        'processed_at',
        'ready_at',
        'completed_at',
        'handled_by',
        'tracking_token',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'ready_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('sent_at', 'desc');
    }

    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Hitung urutan antrean FIFO saat ini.
     * Mengembalikan nomor urut pengerjaan (1 = urutan prioritas pertama).
     */
    public function getQueuePositionAttribute(): ?int
    {
        if (!in_array($this->status, ['menunggu', 'diproses'])) {
            return null;
        }

        // Algoritma FIFO: Diurutkan secara murni berdasarkan received_at ASC, id ASC
        return self::whereIn('status', ['menunggu', 'diproses'])
            ->where(function ($query) {
                $query->where('received_at', '<', $this->received_at)
                    ->orWhere(function ($q) {
                        $q->where('received_at', '=', $this->received_at)
                          ->where('id', '<=', $this->id);
                    });
            })
            ->count();
    }

    /**
     * Estimasi waktu tunggu (menit) berdasarkan posisi antrean FIFO.
     */
    public function getEstimatedWaitMinutesAttribute(): int
    {
        $pos = $this->queue_position ?? 1;
        $setting = Setting::first();
        $rate = $setting ? $setting->estimated_minutes_per_queue : 4;
        return max(3, $pos * $rate);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Antrean',
            'diproses' => 'Sedang Diproses',
            'siap_diambil' => 'Siap Diambil',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }
}

