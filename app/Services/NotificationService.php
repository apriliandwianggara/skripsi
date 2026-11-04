<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Notification;
use App\Models\Setting;

/**
 * Service Notifikasi Pengambilan Pesanan
 * 
 * Bertanggung jawab mengirimkan pemberitahuan kepada pelanggan saat pesanan siap diambil:
 * 1. Notifikasi Suara / Chime Bell di web pelacakan pelanggan.
 * 2. Notifikasi WhatsApp pesan instan ke nomor pelanggan.
 * 3. Pencatatan log notifikasi pada tabel `notifications`.
 */
class NotificationService
{
    /**
     * Kirim seluruh notifikasi saat pesanan siap diambil.
     */
    public function notifyOrderReady(Order $order): array
    {
        $setting = Setting::get();
        $message = $this->composeWhatsAppMessage($order, $setting);

        // 1. Catat notifikasi Web Chime
        Notification::send($order, 'web_sound', "Notifikasi suara bel kafe dibunyikan untuk nomor antrean {$order->queue_number}.");

        // 2. Jika pelanggan mencantumkan nomor WhatsApp, catat notifikasi WhatsApp
        $waUrl = null;
        if (!empty($order->customer_phone)) {
            $formattedPhone = $this->formatIndonesianPhone($order->customer_phone);
            $waUrl = 'https://api.whatsapp.com/send?phone=' . $formattedPhone . '&text=' . rawurlencode($message);

            Notification::send($order, 'whatsapp', $message, $formattedPhone);
        }

        return [
            'success' => true,
            'whatsapp_url' => $waUrl,
            'message' => $message,
        ];
    }

    /**
     * Format isi pesan WhatsApp sesuai template pengaturan kafe.
     */
    public function composeWhatsAppMessage(Order $order, Setting $setting): string
    {
        $template = $setting->whatsapp_notification_template;
        if (empty($template)) {
            $template = "Halo Kak {nama_pelanggan}! ☕\n\nPesanan Anda di Sekarsa Coffee dengan Nomor Antrean *{nomor_antrean}* ({nomor_pesanan}) sudah *SIAP DIAMBIL* di counter barista!\n\nSilakan tunjukkan pesan ini atau sebutkan nomor antrean Anda. Terima kasih! ✨";
        }

        $itemsSummary = $order->details->map(function ($d) {
            return "- {$d->quantity}x {$d->menu_name}";
        })->implode("\n");

        $replacements = [
            '{nama_pelanggan}' => $order->customer_name,
            '{nomor_antrean}' => $order->queue_number,
            '{nomor_pesanan}' => $order->order_number,
            '{daftar_menu}' => $itemsSummary,
            '{meja}' => $order->table_number ? "Meja {$order->table_number}" : "Dine-in / Takeaway",
            '{nama_cafe}' => $setting->cafe_name,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Konversi format nomor telepon Indonesia (misal: 0812... -> 62812...).
     */
    public function formatIndonesianPhone(string $phone): string
    {
        // Bersihkan spasi, tanda minus, plus
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }
        return $cleaned;
    }
}

