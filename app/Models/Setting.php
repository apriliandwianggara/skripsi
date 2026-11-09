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
        'cafe_logo',
        'cafe_banner',
        'hero_title',
        'hero_subtitle',
        'cafe_opening_hours',
        'announcement_text',
        'cafe_address',
        'cafe_phone',
        'cafe_instagram',
        'footer_text',
        'whatsapp_notification_template',
        'alarm_sound_enabled',
        'alarm_repeat_interval',
        'estimated_minutes_per_queue',
        'qr_permanent_url',
        'qr_access_key',
        'require_qr_scan',
        'enable_gps_radius',
        'cafe_latitude',
        'cafe_longitude',
        'cafe_radius_meters',
        'gps_tolerance_meters',
        'demo_mode_enabled',
        'demo_mode_label',
        'qris_dana_number',
        'qris_merchant_name',
        'enable_qris_payment',
        'enable_cash_payment',
    ];

    protected $casts = [
        'require_qr_scan' => 'boolean',
        'enable_gps_radius' => 'boolean',
        'demo_mode_enabled' => 'boolean',
        'enable_qris_payment' => 'boolean',
        'enable_cash_payment' => 'boolean',
        'alarm_sound_enabled' => 'boolean',
        'alarm_repeat_interval' => 'integer',
        'estimated_minutes_per_queue' => 'integer',
        'cafe_radius_meters' => 'integer',
        'gps_tolerance_meters' => 'integer',
        'cafe_latitude' => 'float',
        'cafe_longitude' => 'float',
    ];

    /**
     * URL Logo Kafe (Dinamis dari Upload atau Fallback ke Default)
     */
    public function getLogoUrlAttribute(): string
    {
        if (!empty($this->cafe_logo)) {
            if (str_starts_with($this->cafe_logo, 'http://') || str_starts_with($this->cafe_logo, 'https://')) {
                return $this->cafe_logo;
            }
            return asset(ltrim($this->cafe_logo, '/'));
        }
        return asset('images/sekarsa-logo.jpg');
    }

    /**
     * URL Foto Banner Kafe (Dinamis dari Upload atau Fallback ke Default)
     */
    public function getBannerUrlAttribute(): string
    {
        if (!empty($this->cafe_banner)) {
            if (str_starts_with($this->cafe_banner, 'http://') || str_starts_with($this->cafe_banner, 'https://')) {
                return $this->cafe_banner;
            }
            return asset(ltrim($this->cafe_banner, '/'));
        }
        return asset('images/sekarsa-cafe.jpg');
    }

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
     * Cek apakah koordinat pelanggan berada dalam radius kafe (dengan toleransi GPS indoor).
     */
    public function isWithinCafeRadius(float $latitude, float $longitude, ?float $accuracy = null): bool
    {
        // Jika Mode Demo Sidang Skripsi aktif atau proteksi radius nonaktif, langsung izinkan
        if ($this->demo_mode_enabled || !$this->enable_gps_radius) {
            return true;
        }

        $distance = $this->calculateDistanceToCafe($latitude, $longitude);
        $tolerance = (float)($this->gps_tolerance_meters ?? 50);

        // Jika ponsel melaporkan margin of error akurasi GPS (umumnya 15-50m di dalam ruangan kafe),
        // kurangkan deviasi tersebut dari jarak hitung agar pelanggan di dalam ruangan tidak tertolak
        $accuracyDeduction = $accuracy ? min((float)$accuracy, 100.0) : 0;
        $effectiveDistance = max(0, $distance - $accuracyDeduction);

        $maxAllowed = (float)$this->cafe_radius_meters + $tolerance;

        return $effectiveDistance <= $maxAllowed;
    }

    public static function get(): self
    {
        return self::firstOrCreate([], [
            'cafe_name' => 'Sekarsa Coffee',
            'cafe_tagline' => 'Warmth, Taste & Harmony in Every Sip',
            'hero_title' => 'Selamat Datang di Sekarsa Coffee',
            'hero_subtitle' => 'Nikmati racikan kopi istimewa & suasana hangat. Pesan langsung dari meja Anda tanpa perlu antre di kasir.',
            'cafe_opening_hours' => 'Setiap Hari: 08:00 - 23:00 WIB',
            'cafe_address' => 'Jl. Kaliurang KM 5.8 No. 24, Manggung, Caturtunggal, Sleman, D.I. Yogyakarta 55281',
            'cafe_phone' => '0812-3456-7890',
            'cafe_instagram' => '@sekarsacoffee',
            'footer_text' => '© 2026 Sekarsa Coffee & Space. All rights reserved.',
            'whatsapp_notification_template' => "Halo Kak {nama_pelanggan}! ☕\n\nPesanan Anda di Sekarsa Coffee dengan Nomor Antrean *{nomor_antrean}* ({nomor_pesanan}) sudah *SIAP DIAMBIL* di counter barista!\n\nSilakan tunjukkan pesan ini atau sebutkan nomor antrean Anda. Selamat menikmati! ✨",
            'estimated_minutes_per_queue' => 4,
            'qr_permanent_url' => 'http://sekarsacoffee.gt.tc/scan?',
            'cafe_radius_meters' => 150,
            'gps_tolerance_meters' => 50,
            'alarm_sound_enabled' => true,
            'alarm_repeat_interval' => 3,
        ]);
    }
}
