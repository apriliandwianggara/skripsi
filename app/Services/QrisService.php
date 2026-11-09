<?php

namespace App\Services;

use App\Models\Setting;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrisService
{
    /**
     * Hitung Checksum CRC16 CCITT (0xFFFF, Poly 0x1021) sesuai standar EMVCo QRIS.
     */
    public static function calculateCrc16(string $data): string
    {
        $crc = 0xFFFF;
        $length = strlen($data);
        for ($i = 0; $i < $length; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * Buat format Tag-Length-Value (TLV) standar EMVCo.
     */
    public static function formatTlv(string $tag, string $value): string
    {
        $len = str_pad((string)strlen($value), 2, '0', STR_PAD_LEFT);
        return $tag . $len . $value;
    }

    /**
     * Buat payload QRIS Dinamis dengan nominal otomatis mengarah ke DANA.
     */
    public function generateQrisPayload(float|int $amount, string $orderNumber, ?string $danaNumber = null, ?string $merchantName = null): string
    {
        $setting = Setting::first();
        $dana = $danaNumber ?: ($setting->qris_dana_number ?? '087811139210');
        $merchant = strtoupper($merchantName ?: ($setting->qris_merchant_name ?? 'SEKARSA COFFEE'));

        // Normalisasi nomor DANA (buang karakter non-angka)
        $cleanDana = preg_replace('/[^0-9]/', '', $dana);
        if (str_starts_with($cleanDana, '62')) {
            $cleanDana = '0' . substr($cleanDana, 2);
        }

        // Sub-tag 26: Merchant Account Information (DANA / National QRIS)
        // 00: GUI ID.DANA.WWW, 01: DANA ID/NMID, 02: Criteria
        $tag26Sub00 = self::formatTlv('00', 'ID.DANA.WWW');
        $tag26Sub01 = self::formatTlv('01', '93600911' . $cleanDana);
        $tag26Sub02 = self::formatTlv('02', '01');
        $tag26Value = $tag26Sub00 . $tag26Sub01 . $tag26Sub02;
        $tag26 = self::formatTlv('26', $tag26Value);

        // Sub-tag 51: DANA Specific Account Information
        $tag51Sub00 = self::formatTlv('00', 'ID.DANA.WWW');
        $tag51Sub01 = self::formatTlv('01', $cleanDana);
        $tag51Value = $tag51Sub00 . $tag51Sub01;
        $tag51 = self::formatTlv('51', $tag51Value);

        // Sub-tag 62: Additional Data Field (Nomor Order dan No Meja / Referensi)
        $cleanOrderNumber = preg_replace('/[^A-Za-z0-9\-]/', '', $orderNumber);
        $tag62Sub01 = self::formatTlv('01', substr($cleanOrderNumber, 0, 25));
        $tag62Sub07 = self::formatTlv('07', substr($cleanDana, 0, 15));
        $tag62Value = $tag62Sub01 . $tag62Sub07;
        $tag62 = self::formatTlv('62', $tag62Value);

        $intAmount = (int)round($amount);

        // Susun struktur QRIS Dinamis EMVCo:
        $raw = self::formatTlv('00', '01')                        // Payload Format Indicator
            . self::formatTlv('01', '12')                        // Point of Initiation (12 = Dinamis dengan nominal terkunci)
            . $tag26                                             // Merchant Info
            . $tag51                                             // DANA Info
            . self::formatTlv('52', '5812')                      // Merchant Category Code (Resto/Cafe)
            . self::formatTlv('53', '360')                       // Transaction Currency (360 = IDR Rupiah)
            . self::formatTlv('54', (string)$intAmount)          // Transaction Amount (Nominal Otomatis)
            . self::formatTlv('58', 'ID')                        // Country Code (Indonesia)
            . self::formatTlv('59', substr($merchant, 0, 25))    // Merchant Name
            . self::formatTlv('60', 'BENGKULU')                  // Merchant City
            . self::formatTlv('61', '38119')                     // Postal Code
            . $tag62                                             // Additional Data
            . '6304';                                            // Tag 63 (CRC16)

        // Hitung CRC16 dan gabungkan
        $crc = self::calculateCrc16($raw);
        return $raw . $crc;
    }

    /**
     * Render QR code QRIS sebagai Data URI (PNG / fallback SVG base64).
     */
    public function generateQrisDataUri(string $payload): string
    {
        try {
            $options = new QROptions();
            $options->outputInterface = QRGdImagePNG::class;
            $options->eccLevel = EccLevel::M;
            $options->scale = 8;
            $options->addQuietzone = true;

            return (new QRCode($options))->render($payload);
        } catch (\Throwable $e) {
            $svg = $this->generateQrisSvg($payload);
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        }
    }

    /**
     * Render QR code QRIS sebagai SVG mentah.
     */
    public function generateQrisSvg(string $payload): string
    {
        $options = new QROptions();
        $options->outputInterface = QRMarkupSVG::class;
        $options->eccLevel = EccLevel::M;
        $options->outputBase64 = false;
        $options->addQuietzone = true;

        $svg = (new QRCode($options))->render($payload);
        return preg_replace('/<\?xml.*?\?>/i', '', $svg);
    }
}
