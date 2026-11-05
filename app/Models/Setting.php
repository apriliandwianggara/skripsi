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
        'qr_access_key',
        'require_qr_scan',
        'enable_gps_radius',
        'cafe_latitude',
        'cafe_longitude',
        'cafe_radius_meters',
    ];

    protected $casts = [
        'require_qr_scan' => 'boolean',
        'enable_gps_radius' => 'boolean',
        'estimated_minutes_per_queue' => 'integer',
        'cafe_radius_meters' => 'integer',
        'cafe_latitude' => 'float',
        'cafe_longitude' => 'float',
    ];

    /**
     * Hitung jarak Haversine (meter) antara koordinat pelanggan dengan koordinat kafe.
     */
    public function calculateDistanceToCafe(float $latitude, float $longitude): float
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad((float)$latitude);
        $lonFrom = deg2rad((float)$longitude);
        $latTo = deg2rad((float)$this->cafe_latitude);
        $lonTo = deg2rad((float)$this->cafe_longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($earthRadius * $angle, 1);
    }

    /**
     * Cek apakah koordinat pelanggan berada dalam radius kafe.
     */
    public function isWithinCafeRadius(float $latitude, float $longitude): bool
    {
        if (!$this->enable_gps_radius) {
            return true;
        }

        $distance = $this->calculateDistanceToCafe($latitude, $longitude);
        return $distance <= (float)$this->cafe_radius_meters;
    }

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

