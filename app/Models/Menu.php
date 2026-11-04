<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'nama',
        'slug',
        'deskripsi',
        'harga',
        'harga_promo',
        'foto',
        'status_stok',
        'is_rekomendasi',
        'urutan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'harga_promo' => 'decimal:2',
        'is_rekomendasi' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function getHargaAktifAttribute(): float
    {
        if ($this->harga_promo !== null && $this->harga_promo > 0 && $this->harga_promo < $this->harga) {
            return (float) $this->harga_promo;
        }
        return (float) $this->harga;
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/menu-placeholder.svg');
    }
}
