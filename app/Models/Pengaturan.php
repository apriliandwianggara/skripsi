<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cafe',
        'tagline',
        'alamat',
        'telepon',
        'instagram',
        'pesan_struk',
        'qris_image',
        'estimasi_menit_per_antrean',
    ];

    public static function get(): self
    {
        return self::firstOrCreate([], [
            'nama_cafe' => 'Sekarsa Coffee',
            'tagline' => 'Warmth, Taste & Harmony in Every Sip',
            'alamat' => 'Jl. Kaliurang KM 5.2, Sleman, D.I. Yogyakarta',
            'telepon' => '0812-9876-5432',
            'instagram' => '@sekarsacoffee',
            'pesan_struk' => 'Terima kasih atas kunjungan Anda di Sekarsa Coffee! Semoga hari Anda menyenangkan.',
            'estimasi_menit_per_antrean' => 4,
        ]);
    }
}
