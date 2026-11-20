<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Log;

class DokuService
{
    protected string $clientId;
    protected string $secretKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->clientId = config('services.doku.client_id', env('DOKU_CLIENT_ID', 'BRN-0218-1795121278962'));
        $this->secretKey = config('services.doku.secret_key', env('DOKU_SECRET_KEY', ''));
        $this->isProduction = (bool) config('services.doku.is_production', env('DOKU_IS_PRODUCTION', true));
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function isProduction(): bool
    {
        return $this->isProduction;
    }

    /**
     * Membuat Sesi Checkout Pembayaran Resmi DOKU (Virtual Account, E-Wallet, QRIS, Retail)
     */
    public function createCheckout(Order $order): array
    {
        $url = $this->isProduction
            ? 'https://api.doku.com/checkout/v1/payment'
            : 'https://api-sandbox.doku.com/checkout/v1/payment';

        if (!$order->relationLoaded('details')) {
            $order->load('details');
        }

        // Invoice number unik dengan akhiran timestamp
        $invoiceNumber = $order->order_number . '-' . substr(time(), -4);

        $lineItems = [];
        foreach ($order->details as $d) {
            $lineItems[] = [
                'name' => mb_substr((string) $d->menu_name, 0, 50),
                'quantity' => (int) $d->quantity,
                'price' => (int) round($d->price),
            ];
        }

        if (empty($lineItems)) {
            $lineItems = [
                [
                    'name' => "Pesanan {$order->order_number} ({$order->customer_name})",
                    'quantity' => 1,
                    'price' => (int) round($order->total_price),
                ]
            ];
        }

        $callbackUrl = route('customer.track', $order->tracking_token);

        $payload = [
            'order' => [
                'amount' => (int) round($order->total_price),
                'invoice_number' => $invoiceNumber,
                'currency' => 'IDR',
                'callback_url' => $callbackUrl,
                'auto_redirect' => true,
                'line_items' => $lineItems,
            ],
            'payment' => [
                'payment_due_date' => 60, // Sesi berlaku 60 menit
            ],
            'customer' => [
                'id' => 'CUST-' . $order->id,
                'name' => $order->customer_name,
                'phone' => $order->customer_phone ?: '087811139210',
            ],
        ];

        $jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $digest = base64_encode(hash('sha256', $jsonBody, true));

        $requestId = uniqid('req-', true);
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $requestTarget = '/checkout/v1/payment';

        $componentString = 
            "Client-Id:" . $this->clientId . "\n" .
            "Request-Id:" . $requestId . "\n" .
            "Request-Timestamp:" . $timestamp . "\n" .
            "Request-Target:" . $requestTarget . "\n" .
            "Digest:" . $digest;

        $signature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $componentString, $this->secretKey, true));

        $headers = [
            'Client-Id: ' . $this->clientId,
            'Request-Id: ' . $requestId,
            'Request-Timestamp: ' . $timestamp,
            'Signature: ' . $signature,
            'Content-Type: application/json'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('DOKU cURL Error: ' . $curlError);
            throw new \Exception('Gagal menghubungi server DOKU: ' . $curlError);
        }

        $resData = json_decode($response, true);

        if ($httpCode !== 200 || empty($resData['response']['payment']['url'])) {
            Log::error('DOKU Checkout Error: ', ['http' => $httpCode, 'response' => $resData]);
            $msg = $resData['message'][0] ?? 'Gagal membuat sesi pembayaran DOKU.';
            throw new \Exception($msg);
        }

        // Catat invoice number DOKU pada pesanan
        $order->payment_note = $invoiceNumber;
        $order->save();

        return [
            'payment_url' => $resData['response']['payment']['url'],
            'invoice_number' => $invoiceNumber,
            'token_id' => $resData['response']['payment']['token_id'] ?? null,
            'expired_date' => $resData['response']['payment']['expired_datetime'] ?? null,
        ];
    }

    /**
     * Memvalidasi keaslian signature dari Webhook Notifikasi HTTP DOKU
     */
    public function isValidSignature(array $headers, string $rawBody): bool
    {
        $clientIdHeader = $headers['client-id'][0] ?? $headers['Client-Id'] ?? null;
        $requestIdHeader = $headers['request-id'][0] ?? $headers['Request-Id'] ?? null;
        $timestampHeader = $headers['request-timestamp'][0] ?? $headers['Request-Timestamp'] ?? null;
        $signatureHeader = $headers['signature'][0] ?? $headers['Signature'] ?? null;
        $targetHeader = $headers['request-target'][0] ?? $headers['Request-Target'] ?? '/api/doku/notification';

        if (!$clientIdHeader || !$requestIdHeader || !$timestampHeader || !$signatureHeader) {
            return false;
        }

        $digest = base64_encode(hash('sha256', $rawBody, true));

        $componentString = 
            "Client-Id:" . $clientIdHeader . "\n" .
            "Request-Id:" . $requestIdHeader . "\n" .
            "Request-Timestamp:" . $timestampHeader . "\n" .
            "Request-Target:" . $targetHeader . "\n" .
            "Digest:" . $digest;

        $expectedSignature = 'HMACSHA256=' . base64_encode(hash_hmac('sha256', $componentString, $this->secretKey, true));

        return hash_equals($expectedSignature, (string) $signatureHeader);
    }

    /**
     * Memproses notifikasi webhook pembayaran masuk dari DOKU
     */
    public function processNotification(array $data): ?Order
    {
        $rawInvoice = $data['order']['invoice_number'] ?? '';
        if (empty($rawInvoice)) {
            Log::warning('DOKU Webhook: Invoice number kosong', $data);
            return null;
        }

        // Ekstrak order_number asli (hapus akhiran suffix timestamp, misal: ORD-20261121-001-4921 -> ORD-20261121-001)
        $orderNumber = preg_replace('/-\d{4}$/', '', $rawInvoice);

        $order = Order::where('order_number', $orderNumber)
            ->orWhere('order_number', $rawInvoice)
            ->first();

        if (!$order) {
            Log::warning('DOKU Webhook: Pesanan tidak ditemukan untuk invoice: ' . $rawInvoice);
            return null;
        }

        $transactionStatus = strtoupper($data['transaction']['status'] ?? '');
        $channel = $data['channel']['id'] ?? $data['payment']['payment_method_type'] ?? 'DOKU';

        if ($transactionStatus === 'SUCCESS') {
            $this->markOrderAsPaid($order, 'doku', $data);
        }

        return $order;
    }

    /**
     * Tandai pesanan sebagai LUNAS dan catat riwayat
     */
    public function markOrderAsPaid(Order $order, string $paymentType, array $data = []): void
    {
        if ($order->isPaid()) {
            return;
        }

        $channelName = $data['channel']['id'] ?? $data['payment']['payment_method_type'] ?? 'DOKU';

        $order->payment_status = 'lunas';
        $order->paid_at = now();
        $order->payment_note = "Lunas via DOKU ({$channelName})";
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'previous_status' => $order->status,
            'new_status' => $order->status,
            'changed_by' => 'DOKU Gateway',
            'notes' => "Pembayaran sejumlah Rp " . number_format($order->total_price, 0, ',', '.') . " telah lunas via DOKU ({$channelName}).",
            'created_at' => now(),
        ]);

        Log::info("Pesanan {$order->order_number} berhasil diverifikasi LUNAS otomatis via DOKU.");
    }
}
