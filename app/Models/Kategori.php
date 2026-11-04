<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'icon',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->orderBy('urutan')->orderBy('nama');
    }

    public function activeMenus(): HasMany
    {
        return $this->hasMany(Menu::class)
            ->where('status_stok', 'tersedia')
            ->orderBy('urutan')
            ->orderBy('nama');
    }
}
