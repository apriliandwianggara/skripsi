<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cafe_name',
        'cafe_tagline',
        'cafe_address',
        'cafe_phone',
        'cafe_instagram',
        'whatsapp_notification_template',
        'estimated_minutes_per_queue',
        'qr_permanent_url',
    ];

    public static function get(): self
    {
        return self::firstOrCreate([], [
            'cafe_name' => 'Sekarsa Coffee',
            'cafe_tagline' => 'Warmth, Taste & Harmony in Every Sip',
            'cafe_address' => 'Jl. Kaliurang KM 5.8 No. 24, Manggung, Caturtunggal, Sleman, D.I. Yogyakarta 55281',
            'cafe_phone' => '0812-3456-7890',
            'cafe_instagram' => '@sekarsacoffee',
            'whatsapp_notification_template' => "Halo Kak {nama_pelanggan}! ☕\n\nPesanan Anda di Sekarsa Coffee dengan Nomor Antrean *{nomor_antrean}* ({nomor_pesanan}) sudah *SIAP DIAMBIL* di counter barista!\n\nSilakan tunjukkan pesan ini atau sebutkan nomor antrean Anda. Selamat menikmati! ✨",
            'estimated_minutes_per_queue' => 4,
            'qr_permanent_url' => url('/pesan'),
        ]);
    }
}

