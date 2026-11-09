<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'price',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'order' => 'integer',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}

