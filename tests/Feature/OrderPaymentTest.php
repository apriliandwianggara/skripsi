<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan Setting default
        Setting::query()->delete();
        Setting::create([
            'cafe_name' => 'Sekarsa Coffee',
            'qr_permanent_url' => 'http://sekarsacoffee.gt.tc',
            'qr_access_key' => 'sekarsa_qr_cafe_2026',
            'require_qr_scan' => false,
            'qris_dana_number' => '087811139210',
            'qris_merchant_name' => 'Sekarsa Coffee',
            'whatsapp_notification_template' => 'Pesanan #{queue_number} siap diambil.',
            'estimated_minutes_per_queue' => 4,
        ]);
    }

    public function test_customer_can_order_with_qris_and_view_payment_page()
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Kopi Susu Sekarsa',
            'slug' => 'kopi-susu-sekarsa',
            'price' => 22000,
            'is_available' => true,
        ]);

        $payload = [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'table_number' => '03',
            'payment_method' => 'qris',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 1,
                ]
            ]
        ];

        $response = $this->postJson(route('customer.submit_order'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'payment_method' => 'qris',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        $token = Order::first()->tracking_token;

        // Buka halaman pembayaran QRIS
        $paymentPage = $this->get(route('customer.payment', $token));
        $paymentPage->assertStatus(200);
        $paymentPage->assertSee('087811139210');
        $paymentPage->assertSee('22.000');

        // Pelanggan konfirmasi bayar
        $confirmResponse = $this->postJson(route('customer.confirm_payment', $token));
        $confirmResponse->assertStatus(200);
        $confirmResponse->assertJson(['success' => true]);

        $order = Order::first();
        $this->assertTrue($order->isPaid());
        $this->assertEquals('lunas', $order->payment_status);
    }

    public function test_customer_can_order_with_cash_and_cashier_confirms_payment()
    {
        $category = Category::create(['name' => 'Kopi', 'slug' => 'kopi']);
        $menu = Menu::create([
            'category_id' => $category->id,
            'name' => 'Americano',
            'slug' => 'americano',
            'price' => 20000,
            'is_available' => true,
        ]);

        $payload = [
            'customer_name' => 'Andi Wijaya',
            'customer_phone' => '081298765432',
            'table_number' => '05',
            'payment_method' => 'cash',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 1,
                ]
            ]
        ];

        $response = $this->postJson(route('customer.submit_order'), $payload);
        $response->assertStatus(200);

        $order = Order::latest()->first();
        $this->assertEquals('cash', $order->payment_method);
        $this->assertEquals('menunggu_pembayaran', $order->payment_status);
        $this->assertFalse($order->isPaid());

        // Status tracking API awalnya menunggu pembayaran
        $statusApi = $this->getJson(route('customer.status_api', $order->tracking_token));
        $statusApi->assertStatus(200);
        $statusApi->assertJson([
            'success' => true,
            'is_paid' => false,
            'payment_method' => 'cash',
            'payment_status' => 'menunggu_pembayaran',
        ]);

        // Kasir konfirmasi pembayaran tunai
        $cashier = User::create([
            'name' => 'Kasir Utama',
            'username' => 'kasir_utama',
            'email' => 'kasir@sekarsa.com',
            'password' => bcrypt('password123'),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        $cashierResponse = $this->actingAs($cashier)->postJson(route('pegawai.order.confirm_payment', $order));
        $cashierResponse->assertStatus(200);
        $cashierResponse->assertJson(['success' => true]);

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertEquals('lunas', $order->payment_status);

        // Status tracking API setelah kasir konfirmasi
        $statusApiAfter = $this->getJson(route('customer.status_api', $order->tracking_token));
        $statusApiAfter->assertStatus(200);
        $statusApiAfter->assertJson([
            'success' => true,
            'is_paid' => true,
            'payment_status' => 'lunas',
        ]);
    }
}
