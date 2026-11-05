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
     * Dapatkan URL permanen pemesanan Sekarsa Coffee.
     */
    public function getPermanentOrderUrl(): string
    {
        $setting = Setting::get();
        if ($setting && !empty($setting->qr_permanent_url)) {
            return $setting->qr_permanent_url;
        }
        return url('/pesan');
    }

    /**
     * Render Kode QR Permanen dalam format SVG Vektor (Kualitas tak terbatas untuk cetak).
     */
    public function generatePermanentSvg(): string
    {
        $url = $this->getPermanentOrderUrl();

        $options = new QROptions();
        $options->outputInterface = QRMarkupSVG::class;
        $options->eccLevel = EccLevel::H; // Error correction tinggi (High 30%)
        $options->imageBase64 = false;
        $options->addQuietzone = true;

        return (new QRCode($options))->render($url);
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
        $url = url('/pesan?meja=' . urlencode($tableNumber));

        $options = new QROptions();
        $options->outputInterface = QRMarkupSVG::class;
        $options->eccLevel = EccLevel::H;
        $options->imageBase64 = false;
        $options->addQuietzone = true;

        return (new QRCode($options))->render($url);
    }
}

