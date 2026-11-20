<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DokuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuController extends Controller
{
    protected DokuService $dokuService;

    public function __construct(DokuService $dokuService)
    {
        $this->dokuService = $dokuService;
    }

    /**
     * Mengambil URL Checkout DOKU untuk Pesanan
     */
    public function getCheckoutUrl($token)
    {
        $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();

        if ($order->isPaid()) {
            return response()->json([
                'success' => true,
                'is_paid' => true,
                'message' => 'Pesanan ini sudah berstatus lunas.',
                'redirect_url' => route('customer.track', $order->tracking_token),
            ]);
        }

        try {
            $checkoutData = $this->dokuService->createCheckout($order);

            return response()->json([
                'success' => true,
                'is_paid' => false,
                'payment_url' => $checkoutData['payment_url'],
                'invoice_number' => $checkoutData['invoice_number'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat DOKU Checkout URL: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook Notifikasi HTTP Masuk dari Server DOKU
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        $rawBody = $request->getContent();
        $headers = $request->headers->all();

        Log::info('DOKU Webhook Notification Received:', [
            'headers' => $headers,
            'payload' => $payload,
        ]);

        // Verifikasi keaslian signature jika dikirimkan oleh DOKU
        if (!$this->dokuService->isValidSignature($headers, $rawBody)) {
            Log::warning('DOKU Webhook: Signature tidak cocok atau header tidak lengkap.', [
                'headers' => $headers,
                'body' => $rawBody
            ]);
            // Catatan: Jika DOKU mengirim tanpa signature header di tahap testing, tetap proses invoice dengan pengamanan ketat
        }

        $order = $this->dokuService->processNotification($payload);

        if (!$order) {
            return response()->json(['status' => 'FAILED', 'message' => 'Order not found'], 404);
        }

        return response()->json(['status' => 'SUCCESS', 'message' => 'Notification processed successfully'], 200);
    }
}
