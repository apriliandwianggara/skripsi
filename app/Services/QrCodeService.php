<?php

namespace App\Services;

use App\Models\Setting;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\Output\QRGdImagePNG;

/**
 * Service Pembuatan Kode QR Permanen Sekarsa Coffee
 * Kode QR ini digunakan permanen / selamanya untuk dicetak pada akrilik meja,
 * mengarahkan langsung ke halaman pemesanan dengan proteksi token QR gate.
 */
class QrCodeService
{
    /**
     * Dapatkan Base URL yang bersih (hanya scheme://host[:port]).
     */
    public function getCleanBaseUrl(): string
    {
        $setting = Setting::get();
        $raw = !empty($setting->qr_permanent_url) ? trim($setting->qr_permanent_url) : url('/');

        $parsed = parse_url($raw);
        $scheme = $parsed['scheme'] ?? 'http';
        $host = $parsed['host'] ?? 'sekarsacoffee.gt.tc';
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';

        // Hapus path apapun seperti /scan, /pesan, ? dsb.
        return $scheme . '://' . $host . $port;
    }

    /**
     * Dapatkan URL permanen pemesanan Sekarsa Coffee (dengan proteksi QR token).
     */
    public function getPermanentOrderUrl(): string
    {
        $setting = Setting::get();
        $key = $setting->qr_access_key ?? 'sekarsa_qr_cafe_2026';
        $base = $this->getCleanBaseUrl();

        return $base . '/pesan?token=' . urlencode($key);
    }

    /**
     * Dapatkan URL pemesanan langsung untuk nomor meja tertentu.
     */
    public function getTableOrderUrl(string $tableNumber): string
    {
        $setting = Setting::get();
        $key = $setting->qr_access_key ?? 'sekarsa_qr_cafe_2026';
        $base = $this->getCleanBaseUrl();

        return $base . '/pesan?token=' . urlencode($key) . '&meja=' . urlencode($tableNumber);
    }

    /**
     * Render Kode QR Permanen dalam format SVG Vektor (Kualitas tak terbatas untuk cetak).
     */
    public function generatePermanentSvg(): string
    {
        $url = $this->getPermanentOrderUrl();

        $options = new QROptions();
        $options->version = QRCode::VERSION_AUTO;
        $options->eccLevel = EccLevel::M;
        $options->outputInterface = QRMarkupSVG::class;
        $options->outputBase64 = false;
        $options->svgUseFillAttribute = true;

        $svg = (new QRCode($options))->render($url);

        return $this->cleanSvg($svg);
    }

    /**
     * Render Kode QR Permanen dalam format PNG Data URI (Resolusi tinggi).
     */
    public function generatePermanentDataUri(): string
    {
        $url = $this->getPermanentOrderUrl();

        $options = new QROptions();
        $options->version = QRCode::VERSION_AUTO;
        $options->eccLevel = EccLevel::H;
        $options->outputInterface = QRGdImagePNG::class;
        $options->scale = 10;
        $options->outputBase64 = true;

        return (new QRCode($options))->render($url);
    }

    /**
     * Render Kode QR khusus nomor meja jika kafe ingin mencetak per nomor meja.
     */
    public function generateTableSvg(string $tableNumber): string
    {
        $url = $this->getTableOrderUrl($tableNumber);

        $options = new QROptions();
        $options->version = QRCode::VERSION_AUTO;
        $options->eccLevel = EccLevel::M;
        $options->outputInterface = QRMarkupSVG::class;
        $options->outputBase64 = false;
        $options->svgUseFillAttribute = true;

        $svg = (new QRCode($options))->render($url);

        return $this->cleanSvg($svg);
    }

    /**
     * Bersihkan XML declaration jika ada dari SVG agar bisa di-inline ke blade.
     */
    private function cleanSvg(string $svg): string
    {
        $svg = preg_replace('/<\?xml[^\>]*\?>/i', '', $svg);
        $svg = preg_replace('/<!DOCTYPE[^\>]*>/i', '', $svg);
        return trim($svg);
    }
}
