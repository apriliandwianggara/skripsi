<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use App\Models\Meja;

class QrCodeService
{
    /**
     * Render SVG QR Code untuk nomor meja tertentu.
     */
    public function generateSvgForMeja(Meja $meja): string
    {
        $url = $this->getTableOrderUrl($meja);
        
        $options = new QROptions();
        $options->outputType = QRCode::OUTPUT_MARKUP_SVG;
        $options->eccLevel = QRCode::ECC_M;
        $options->imageBase64 = false;
        $options->addQuietzone = true;
        
        return (new QRCode($options))->render($url);
    }

    /**
     * Render Data URI (PNG / SVG) untuk ditampilkan langsung di tag <img>.
     */
    public function generateDataUriForMeja(Meja $meja): string
    {
        $url = $this->getTableOrderUrl($meja);

        $options = new QROptions();
        $options->outputType = QRCode::OUTPUT_IMAGE_PNG;
        $options->eccLevel = QRCode::ECC_M;
        $options->scale = 6;
        $options->addQuietzone = true;

        return (new QRCode($options))->render($url);
    }

    /**
     * Dapatkan URL lengkap pemesanan untuk meja tertentu.
     */
    public function getTableOrderUrl(Meja $meja): string
    {
        return url('/pesan?meja=' . urlencode($meja->nomor_meja) . '&token=' . $meja->qr_token);
    }
}
