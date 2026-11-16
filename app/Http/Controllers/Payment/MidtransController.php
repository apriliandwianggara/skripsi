<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Mengambil Snap Token untuk inisialisasi Midtrans Popup di Frontend
     */
    public function getSnapToken($token)
    {
        $order = Order::with('details')->where('tracking_token', $token)->firstOrFail();

        if ($order->isPaid()) {
            return response()->json([
                'success' => true,
                'is_paid' => true,
                'message' => 'Pesanan ini sudah lunas.',
                'redirect_url' => route('customer.track', $order->tracking_token),
            ]);
        }

        try {
            $snapData = $this->midtransService->createSnapToken($order);

            return response()->json([
                'success' => true,
                'is_paid' => false,
                'snap_token' => $snapData['token'],
                'redirect_url' => $snapData['redirect_url'],
                'client_key' => $this->midtransService->getClientKey(),
                'is_production' => $this->midtransService->isProduction(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal mendapatkan Snap Token Midtrans: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Webhook Handler / Notifikasi HTTP POST dari Server Midtrans
     */
    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        Log::info('Midtrans Webhook Received: ', $payload);

        // Validasi signature keamanan
        if (!$this->midtransService->isValidSignature($payload)) {
            Log::warning('Midtrans Webhook: Invalid Signature', $payload);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 403);
        }

        $order = $this->midtransService->processNotification($payload);

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        return response()->json(['status' => 'success', 'message' => 'Notification processed successfully']);
    }

    /**
     * Endpoint Simulasi Sukses (Khusus Pengujian / Sidang Skripsi)
     * Digunakan untuk mendemonstrasikan status lunas seketika tanpa jeda
     */
    public function simulatePaymentSuccess($token)
    {
        $order = Order::where('tracking_token', $token)->firstOrFail();

        if ($order->isPaid()) {
            return response()->json([
                'success' => true,
                'is_paid' => true,
                'message' => 'Pesanan ini sudah berstatus lunas.',
                'redirect_url' => route('customer.track', $order->tracking_token),
            ]);
        }

        $this->midtransService->markOrderAsPaid($order, 'qris_simulator', [
            'transaction_id' => 'SIM-' . strtoupper(\Illuminate\Support\Str::random(10)),
        ]);

        return response()->json([
            'success' => true,
            'is_paid' => true,
            'message' => 'Simulasi pembayaran sukses! Status pesanan otomatis lunas.',
            'redirect_url' => route('customer.track', $order->tracking_token),
        ]);
    }
}
