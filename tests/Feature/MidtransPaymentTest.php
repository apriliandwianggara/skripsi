<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::create([
            'cafe_name' => 'Sekarsa Coffee',
            'qris_merchant_name' => 'Sekarsa Coffee',
            'qris_dana_number' => '087811139210',
            'require_qr_scan' => false,
        ]);
    }

    public function test_can_get_snap_token_or_simulate_payment()
    {
        $order = Order::create([
            'order_number' => 'ORD-20261117-099',
            'queue_number' => '099',
            'customer_name' => 'Dosen Penguji',
            'total_price' => 25000,
            'payment_method' => 'qris',
            'payment_status' => 'menunggu_pembayaran',
            'status' => 'menunggu',
            'tracking_token' => 'test-token-midtrans-12345',
            'received_at' => now(),
        ]);

        $order->details()->create([
            'menu_name' => 'Caramel Macchiato',
            'price' => 25000,
            'quantity' => 1,
            'subtotal' => 25000,
        ]);

        // 1. Test get snap token endpoint
        $response = $this->getJson(route('midtrans.snap_token', $order->tracking_token));
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('snap_token'));

        // 2. Test simulation endpoint for thesis defense
        $simResponse = $this->postJson(route('midtrans.simulate', $order->tracking_token));
        $simResponse->assertStatus(200);
        $this->assertTrue($simResponse->json('is_paid'));

        // Pastikan di database sudah lunas
        $order->refresh();
        $this->assertEquals('lunas', $order->payment_status);
        $this->assertNotNull($order->paid_at);
    }

    public function test_midtrans_webhook_notification_marks_order_as_paid()
    {
        $order = Order::create([
            'order_number' => 'ORD-20261117-088',
            'queue_number' => '088',
            'customer_name' => 'Budi Santoso',
            'total_price' => 30000,
            'payment_method' => 'qris',
            'payment_status' => 'menunggu_pembayaran',
            'status' => 'menunggu',
            'tracking_token' => 'test-token-webhook-98765',
            'received_at' => now(),
        ]);

        $serverKey = config('services.midtrans.server_key');
        $orderId = 'ORD-20261117-088-1234';
        $statusCode = '200';
        $grossAmount = '30000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'trx-midtrans-test-111',
        ];

        $response = $this->postJson(route('midtrans.notification'), $payload);
        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals('lunas', $order->payment_status);
        $this->assertNotNull($order->paid_at);
    }
}
