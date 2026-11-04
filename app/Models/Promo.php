<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_promo',
        'judul',
        'deskripsi',
        'tipe',
        'nilai_potongan',
        'min_pembelian',
        'maksimal_potongan',
        'tgl_mulai',
        'tgl_selesai',
        'is_active',
    ];

    protected $casts = [
        'nilai_potongan' => 'decimal:2',
        'min_pembelian' => 'decimal:2',
        'maksimal_potongan' => 'decimal:2',
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function pesanans(): HasMany
    {
        return $this->hasMany(Pesanan::class);
    }

    public function isValid(float $subtotal): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->startOfDay();
        if ($this->tgl_mulai && $today->lt($this->tgl_mulai)) {
            return false;
        }
        if ($this->tgl_selesai && $today->gt($this->tgl_selesai)) {
            return false;
        }

        if ($subtotal < $this->min_pembelian) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid($subtotal)) {
            return 0.00;
        }

        if ($this->tipe === 'persen') {
            $diskon = ($subtotal * $this->nilai_potongan) / 100;
            if ($this->maksimal_potongan && $diskon > $this->maksimal_potongan) {
                $diskon = (float) $this->maksimal_potongan;
            }
            return min($diskon, $subtotal);
        }

        return min((float) $this->nilai_potongan, $subtotal);
    }
}
