<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class)->orderBy('order')->orderBy('name');
    }

    public function activeMenus(): HasMany
    {
        return $this->hasMany(Menu::class)
            ->where('is_available', true)
            ->orderBy('order')
            ->orderBy('name');
    }
}

