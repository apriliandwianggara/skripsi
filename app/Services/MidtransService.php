<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('services.midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        $this->clientKey = config('services.midtrans.client_key', env('MIDTRANS_CLIENT_KEY', ''));
        $this->isProduction = (bool) config('services.midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false));
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    public function getSnapJsUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Membuat Snap Token untuk transaksi pesanan
     */
    public function createSnapToken(Order $order): array
    {
        $url = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        // Pastikan details ter-load
        if (!$order->relationLoaded('details')) {
            $order->load('details');
        }

        // Suffix waktu 4 digit agar jika pelanggan memuat ulang transaksi di Midtrans tidak kena 'order_id already used'
        $midtransOrderId = $order->order_number . '-' . substr(time(), -4);

        $items = [];
        foreach ($order->details as $d) {
            $items[] = [
                'id' => (string) ($d->menu_id ?? 1),
                'price' => (int) round($d->price),
                'quantity' => (int) $d->quantity,
                'name' => mb_substr((string) $d->menu_name, 0, 50),
            ];
        }

        // Jika rincian item kosong atau pembulatan berbeda dengan total
        $totalItems = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        if ($totalItems !== (int) round($order->total_price)) {
            $items = [
                [
                    'id' => $order->order_number,
                    'price' => (int) round($order->total_price),
                    'quantity' => 1,
                    'name' => "Pesanan {$order->order_number} ({$order->customer_name})",
                ]
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) round($order->total_price),
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_phone ?? '087811139210',
            ],
            'item_details' => $items,
            'enabled_payments' => [
                'other_qris',
                'gopay',
                'shopeepay',
                'bca_va',
                'bni_va',
                'bri_va',
                'mandiri_va'
            ],
            'callbacks' => [
                'finish' => route('customer.track', $order->tracking_token),
            ],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':'),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('Midtrans cURL Error: ' . $curlError);
            throw new \Exception('Gagal menghubungi server Midtrans: ' . $curlError);
        }

        $resData = json_decode($response, true);

        if ($httpCode !== 201 || empty($resData['token'])) {
            Log::error('Midtrans Snap Error: ', ['http' => $httpCode, 'response' => $resData]);
            $msg = $resData['error_messages'][0] ?? 'Gagal membuat sesi pembayaran Midtrans.';
            throw new \Exception($msg);
        }

        return [
            'token' => $resData['token'],
            'redirect_url' => $resData['redirect_url'] ?? null,
            'midtrans_order_id' => $midtransOrderId,
        ];
    }

    /**
     * Memeriksa status transaksi langsung ke server Midtrans (v2 Status API)
     */
    public function checkTransactionStatus(string $orderId): ?array
    {
        $baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        $ch = curl_init($baseUrl . urlencode($orderId) . '/status');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->serverKey . ':'),
        ]);

        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code === 200 && $res) {
            return json_decode($res, true);
        }

        return null;
    }

    /**
     * Memvalidasi keaslian signature dari notifikasi webhook Midtrans
     */
    public function isValidSignature(array $data): bool
    {
        if (empty($data['signature_key']) || empty($data['order_id']) || empty($data['status_code']) || empty($data['gross_amount'])) {
            return false;
        }

        $input = $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $this->serverKey;
        $expectedSignature = hash('sha512', $input);

        return hash_equals($expectedSignature, (string) $data['signature_key']);
    }

    /**
     * Memproses webhook notifikasi dari Midtrans secara real-time
     */
    public function processNotification(array $data): ?Order
    {
        $rawOrderId = $data['order_id'] ?? '';
        
        // Ekstrak order_number asli (hapus akhiran suffix timestamp jika ada, misal: ORD-20261117-001-4921 -> ORD-20261117-001)
        $orderNumber = preg_replace('/-\d{4}$/', '', $rawOrderId);

        $order = Order::where('order_number', $orderNumber)
            ->orWhere('order_number', $rawOrderId)
            ->first();

        if (!$order) {
            Log::warning('Midtrans Webhook: Pesanan tidak ditemukan untuk order_id: ' . $rawOrderId);
            return null;
        }

        $transactionStatus = $data['transaction_status'] ?? '';
        $fraudStatus = $data['fraud_status'] ?? '';
        $paymentType = $data['payment_type'] ?? 'midtrans';

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $this->markOrderAsPaid($order, $paymentType, $data);
            }
        } elseif ($transactionStatus === 'settlement') {
            $this->markOrderAsPaid($order, $paymentType, $data);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $order->payment_status = 'menunggu_pembayaran';
            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'previous_status' => $order->status,
                'new_status' => $order->status,
                'changed_by' => 'Midtrans Webhook',
                'notes' => "Transaksi Midtrans ({$transactionStatus}) kedaluwarsa atau dibatalkan.",
                'created_at' => now(),
            ]);
        } elseif ($transactionStatus === 'pending') {
            $order->payment_status = 'menunggu_pembayaran';
            $order->save();
        }

        return $order;
    }

    /**
     * Ubah status pembayaran menjadi lunas dan catat riwayat
     */
    public function markOrderAsPaid(Order $order, string $paymentType, array $data = []): void
    {
        if ($order->isPaid()) {
            return;
        }

        $order->payment_status = 'lunas';
        $order->paid_at = now();
        $order->payment_note = "Lunas via QRIS / E-Wallet";
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $order->status,
            'new_status' => $order->status,
            'changed_by' => 'Kasir',
            'notes' => "Pembayaran sejumlah Rp " . number_format($order->total_price, 0, ',', '.') . " telah lunas.",
            'created_at' => now(),
        ]);

        Log::info("Pesanan {$order->order_number} berhasil diverifikasi LUNAS otomatis via Midtrans.");
    }
}

