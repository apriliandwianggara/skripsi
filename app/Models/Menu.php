<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_available',
        'order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * URL Foto Menu Cerdas (Kompatibel 100% untuk Shared Hosting & Local)
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }

            $cleanPath = ltrim($this->image, '/');

            // 1. Cek langsung di public/ (misal 'images/menus/xxx.jpg' atau 'images/xxx.jpg')
            if (file_exists(public_path($cleanPath))) {
                return asset($cleanPath);
            }

            // 2. Cek di public/images/
            if (file_exists(public_path('images/' . $cleanPath))) {
                return asset('images/' . $cleanPath);
            }

            // 3. Cek di public/storage/ (jika symlink aktif)
            if (file_exists(public_path('storage/' . $cleanPath))) {
                return asset('storage/' . $cleanPath);
            }

            // 4. Auto-recovery: Jika file terlanjur di storage/app/public/ (bawaan store()), salin otomatis ke public/images/
            $storageCandidate = storage_path('app/public/' . $cleanPath);
            if (file_exists($storageCandidate)) {
                $targetFile = public_path('images/' . $cleanPath);
                @mkdir(dirname($targetFile), 0755, true);
                @copy($storageCandidate, $targetFile);
                return asset('images/' . $cleanPath);
            }

            // 5. Fallback ke asset path langsung
            return asset($cleanPath);
        }

        return asset('images/menu-placeholder.svg');
    }
}
