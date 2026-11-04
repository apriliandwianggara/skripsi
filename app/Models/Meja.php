<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Meja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_meja',
        'kapasitas',
        'lokasi',
        'status',
        'qr_token',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($meja) {
            if (empty($meja->qr_token)) {
                $meja->qr_token = Str::random(32);
            }
        });
    }

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function activePesanan()
    {
        return $this->hasOne(Pesanan::class)
            ->whereIn('status_pesanan', ['menunggu', 'diproses', 'selesai'])
            ->latest('waktu_masuk');
    }

    public function getQrUrlAttribute(): string
    {
        return route('customer.order', ['meja' => $this->nomor_meja, 'token' => $this->qr_token]);
    }
}
