<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\Output\QRGdImagePNG;
use App\Models\Setting;

/**
 * Service Pembuatan Kode QR Permanen Sekarsa Coffee
 * 
 * Kode QR ini digunakan permanen / selamanya untuk dicetak pada akrilik meja,
 * stiker meja, atau banner kafe sehingga pelanggan dapat memindai kapan saja.
 */
class QrCodeService
{
    /**
     * Dapatkan URL permanen pemesanan Sekarsa Coffee (dengan proteksi QR token).
     */
    public function getPermanentOrderUrl(): string
    {
        $setting = Setting::get();
        $key = $setting->qr_access_key ?? 'sekarsa_qr_cafe_2026';

        if ($setting && !empty($setting->qr_permanent_url)) {
            $base = rtrim($setting->qr_permanent_url, '/');
            if (str_contains($base, '/scan')) {
                return str_contains($base, '?') ? $base . '&token=' . $key : $base . '?token=' . $key;
            }
            if (str_ends_with($base, '/pesan')) {
                $base = substr($base, 0, -6);
            }
            return $base . '/scan?token=' . $key;
        }

        return url('/scan?token=' . $key);
    }

    /**
     * Dapatkan URL pemesanan langsung untuk nomor meja tertentu.
     */
    public function getTableOrderUrl(string $tableNumber): string
    {
        $setting = Setting::get();
        $key = $setting->qr_access_key ?? 'sekarsa_qr_cafe_2026';

        if ($setting && !empty($setting->qr_permanent_url)) {
            $base = rtrim($setting->qr_permanent_url, '/');
            if (str_ends_with($base, '/pesan')) {
                $base = substr($base, 0, -6);
            }
            return $base . '/scan?token=' . $key . '&meja=' . urlencode($tableNumber);
        }

        return url('/scan?token=' . $key . '&meja=' . urlencode($tableNumber));
    }

    /**
     * Render Kode QR Permanen dalam format SVG Vektor (Kualitas tak terbatas untuk cetak).
     */
    public function generatePermanentSvg(): string
    {
        $url = $this->getPermanentOrderUrl();

        $options = new QROptions();
        $options->outputInterface = QRMarkupSVG::class;
        $options->eccLevel = EccLevel::H;
        $options->outputBase64 = false; // Kembalikan raw SVG XML murni, bukan data:image/svg+xml;base64
        $options->addQuietzone = true;

        $svg = (new QRCode($options))->render($url);
        return trim(preg_replace('/<\?xml[^\?]*\?>/i', '', (string)$svg));
    }

    /**
     * Render Kode QR Permanen dalam format PNG Data URI (Resolusi tinggi).
     */
    public function generatePermanentDataUri(): string
    {
        $url = $this->getPermanentOrderUrl();

        $options = new QROptions();
        $options->outputInterface = QRGdImagePNG::class;
        $options->eccLevel = EccLevel::H;
        $options->scale = 10;
        $options->addQuietzone = true;

        return (new QRCode($options))->render($url);
    }

    /**
     * Render Kode QR khusus nomor meja jika kafe ingin mencetak per nomor meja.
     */
    public function generateTableSvg(string $tableNumber): string
    {
        $url = $this->getTableOrderUrl($tableNumber);

        $options = new QROptions();
        $options->outputInterface = QRMarkupSVG::class;
        $options->eccLevel = EccLevel::H;
        $options->outputBase64 = false;
        $options->addQuietzone = true;

        $svg = (new QRCode($options))->render($url);
        return trim(preg_replace('/<\?xml[^\?]*\?>/i', '', (string)$svg));
    }
}

