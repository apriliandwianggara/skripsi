<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatusHistory;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pengaturan Sistem Sekarsa Coffee
        $setting = Setting::create([
            'cafe_name' => 'Sekarsa Coffee',
            'cafe_tagline' => 'Warmth, Taste & Harmony in Every Sip',
            'cafe_address' => 'Jl. Kaliurang KM 5.8 No. 24, Manggung, Caturtunggal, Sleman, D.I. Yogyakarta 55281',
            'cafe_phone' => '0812-3456-7890',
            'cafe_instagram' => '@sekarsacoffee',
            'whatsapp_notification_template' => "Halo Kak {nama_pelanggan}! ☕\n\nPesanan Anda di Sekarsa Coffee dengan Nomor Antrean *{nomor_antrean}* ({nomor_pesanan}) sudah *SIAP DIAMBIL* di counter barista!\n\nSilakan tunjukkan pesan ini atau sebutkan nomor antrean Anda di counter. Selamat menikmati! ✨",
            'estimated_minutes_per_queue' => 4,
            'qr_permanent_url' => url('/pesan'),
        ]);

        // 2. Akun Admin dan Kasir / Barista (Multi-Kasir 1, 2, 3 untuk Skripsi)
        $admin = User::create([
            'name' => 'Owner / Admin',
            'username' => 'admin',
            'email' => 'admin@sekarsacoffee.com',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $kasir1 = User::create([
            'name' => 'Kasir 1 (Barista Bar Depan)',
            'username' => 'kasir1',
            'email' => 'kasir1@sekarsacoffee.com',
            'phone' => '081298765431',
            'role' => 'pegawai',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $kasir2 = User::create([
            'name' => 'Kasir 2 (Barista Espresso)',
            'username' => 'kasir2',
            'email' => 'kasir2@sekarsacoffee.com',
            'phone' => '081298765432',
            'role' => 'pegawai',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $kasir3 = User::create([
            'name' => 'Kasir 3 (Barista Manual Brew)',
            'username' => 'kasir3',
            'email' => 'kasir3@sekarsacoffee.com',
            'phone' => '081298765433',
            'role' => 'pegawai',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $pegawai = $kasir1;

        // 3. Kategori Menu
        $catSignature = Category::create([
            'name' => 'Signature Coffee',
            'slug' => 'signature-coffee',
            'description' => 'Racikan kopi khas barista Sekarsa dengan cita rasa unik dan gula aren murni',
            'icon' => 'coffee',
            'order' => 1,
            'is_active' => true,
        ]);

        $catEspresso = Category::create([
            'name' => 'Espresso Based',
            'slug' => 'espresso-based',
            'description' => 'Kombinasi espresso murni dan susu segar berkualitas tinggi',
            'icon' => 'mug-hot',
            'order' => 2,
            'is_active' => true,
        ]);

        $catNonCoffee = Category::create([
            'name' => 'Non-Coffee & Tea',
            'slug' => 'non-coffee-tea',
            'description' => 'Sajian segar tanpa kopi mulai dari artisan matcha hingga chocolate',
            'icon' => 'glass-water',
            'order' => 3,
            'is_active' => true,
        ]);

        $catFood = Category::create([
            'name' => 'Makanan & Pastry',
            'slug' => 'makanan-pastry',
            'description' => 'Menu makanan hangat dan kudapan renyah pendamping ngopi santai',
            'icon' => 'utensils',
            'order' => 4,
            'is_active' => true,
        ]);

        // 4. Daftar Menu
        $menus = [
            // Signature
            [
                'category_id' => $catSignature->id,
                'name' => 'Kopi Susu Sekarsa',
                'slug' => 'kopi-susu-sekarsa',
                'description' => 'Espresso blend khas Sekarsa dengan susu creamy dan gula aren organik pilihan.',
                'price' => 22000,
                'is_available' => true,
                'order' => 1,
            ],
            [
                'category_id' => $catSignature->id,
                'name' => 'Karamel Macchiato Sekarsa',
                'slug' => 'karamel-macchiato-sekarsa',
                'description' => 'Espresso layered milk dengan saus karamel artisanal lembut yang lumer di lidah.',
                'price' => 28000,
                'is_available' => true,
                'order' => 2,
            ],
            [
                'category_id' => $catSignature->id,
                'name' => 'Cold Brew Berry Breeze',
                'slug' => 'cold-brew-berry-breeze',
                'description' => 'Cold brew steeping 16 jam dengan ekstrak strawberry alami dan tonic segar.',
                'price' => 26000,
                'is_available' => true,
                'order' => 3,
            ],

            // Espresso Based
            [
                'category_id' => $catEspresso->id,
                'name' => 'Americano Sekarsa',
                'slug' => 'americano-sekarsa',
                'description' => 'Double shot espresso murni dengan air mineral dingin/panas, clean dan bold.',
                'price' => 18000,
                'is_available' => true,
                'order' => 1,
            ],
            [
                'category_id' => $catEspresso->id,
                'name' => 'Cafe Latte',
                'slug' => 'cafe-latte',
                'description' => 'Steamed fresh milk dengan single shot espresso dan velvety microfoam.',
                'price' => 24000,
                'is_available' => true,
                'order' => 2,
            ],
            [
                'category_id' => $catEspresso->id,
                'name' => 'Cappuccino Brulee',
                'slug' => 'cappuccino-brulee',
                'description' => 'Cappuccino dengan lapisan foam tebal dan taburan brown sugar karamel bakar di atasnya.',
                'price' => 26000,
                'is_available' => true,
                'order' => 3,
            ],

            // Non-Coffee
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Matcha Oat Latte',
                'slug' => 'matcha-oat-latte',
                'description' => 'Pure Uji Matcha import dipadukan dengan creamy oat milk bebas laktosa.',
                'price' => 28000,
                'is_available' => true,
                'order' => 1,
            ],
            [
                'category_id' => $catNonCoffee->id,
                'name' => 'Belgian Dark Chocolate',
                'slug' => 'belgian-dark-chocolate',
                'description' => 'Cokelat Belgia pekat 70% dengan susu segar, rasa cokelat mewah dan tidak serik.',
                'price' => 25000,
                'is_available' => true,
                'order' => 2,
            ],

            // Makanan
            [
                'category_id' => $catFood->id,
                'name' => 'Rice Bowl Beef Blackpepper',
                'slug' => 'rice-bowl-beef-blackpepper',
                'description' => 'Daging sapi tenderloin empuk dengan saus lada hitam khas Sekarsa, telur & nasi hangat.',
                'price' => 36000,
                'is_available' => true,
                'order' => 1,
            ],
            [
                'category_id' => $catFood->id,
                'name' => 'Chicken Katsu Sambal Matah',
                'slug' => 'chicken-katsu-sambal-matah',
                'description' => 'Ayam katsu krispi emas disajikan bersama sambal matah Bali segar dan nasi pulen.',
                'price' => 32000,
                'is_available' => true,
                'order' => 2,
            ],
            [
                'category_id' => $catFood->id,
                'name' => 'Truffle Fries Mayo',
                'slug' => 'truffle-fries-mayo',
                'description' => 'Kentang goreng renyah wangi minyak truffle bertabur keju parmesan & saus garlic mayo.',
                'price' => 22000,
                'is_available' => true,
                'order' => 3,
            ],
        ];

        $createdMenus = [];
        foreach ($menus as $m) {
            $createdMenus[$m['slug']] = Menu::create($m);
        }

        // Variasi Menu Contoh (S: 20k, M: 23k, L: 25k) untuk Americano
        if (isset($createdMenus['americano-sekarsa'])) {
            $createdMenus['americano-sekarsa']->variations()->createMany([
                ['name' => 'S', 'price' => 20000, 'order' => 1],
                ['name' => 'M', 'price' => 23000, 'order' => 2],
                ['name' => 'L', 'price' => 25000, 'order' => 3],
            ]);
            $createdMenus['americano-sekarsa']->update(['price' => 20000]);
        }

        // 5. Data Demonstrasi Algoritma FIFO (Persis Contoh Skripsi Pengguna)
        // Pelanggan A | Waktu: 10.00 | Nomor antrean: 001 | Status: diproses
        // Pelanggan B | Waktu: 10.02 | Nomor antrean: 002 | Status: menunggu
        // Pelanggan C | Waktu: 10.05 | Nomor antrean: 003 | Status: menunggu
        // Pelanggan D | Waktu: 10.07 | Nomor antrean: 004 | Status: siap_diambil (Contoh Notifikasi!)
        // Pelanggan E | Selesai (Riwayat)

        $baseTime = Carbon::now()->subMinutes(25);

        // Pelanggan A (Urutan FIFO #1 - Sedang Diproses)
        $o1 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-001',
            'queue_number' => '001',
            'customer_name' => 'Pelanggan A (Andi)',
            'customer_phone' => '081234567801',
            'table_number' => '01',
            'customer_notes' => 'Less sugar, es dipisah',
            'total_price' => 44000,
            'status' => 'diproses',
            'received_at' => $baseTime->copy(), // 10.00
            'processed_at' => $baseTime->copy()->addMinutes(2),
            'handled_by' => $pegawai->id,
            'tracking_token' => 'token-demo-001',
        ]);
        OrderDetail::create([
            'order_id' => $o1->id,
            'menu_id' => $createdMenus['kopi-susu-sekarsa']->id,
            'menu_name' => 'Kopi Susu Sekarsa',
            'price' => 22000,
            'quantity' => 2,
            'subtotal' => 44000,
            'notes' => 'Less sugar, es dipisah',
        ]);
        OrderStatusHistory::create([
            'order_id' => $o1->id,
            'previous_status' => null,
            'new_status' => 'menunggu',
            'changed_by' => 'Sistem Pemesanan Digital',
            'notes' => 'Pesanan diterima server dan masuk antrean FIFO urutan #1.',
            'created_at' => $baseTime->copy(),
        ]);
        OrderStatusHistory::create([
            'order_id' => $o1->id,
            'previous_status' => 'menunggu',
            'new_status' => 'diproses',
            'changed_by' => $pegawai->name,
            'notes' => 'Barista mulai meracik pesanan sesuai urutan FIFO.',
            'created_at' => $baseTime->copy()->addMinutes(2),
        ]);

        // Pelanggan B (Urutan FIFO #2 - Menunggu Antrean)
        $o2 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-002',
            'queue_number' => '002',
            'customer_name' => 'Pelanggan B (Bunga)',
            'customer_phone' => '081234567802',
            'table_number' => '03',
            'customer_notes' => 'Mohon jangan terlalu manis',
            'total_price' => 52000,
            'status' => 'menunggu',
            'received_at' => $baseTime->copy()->addMinutes(2), // 10.02
            'tracking_token' => 'token-demo-002',
        ]);
        OrderDetail::create([
            'order_id' => $o2->id,
            'menu_id' => $createdMenus['matcha-oat-latte']->id,
            'menu_name' => 'Matcha Oat Latte',
            'price' => 28000,
            'quantity' => 1,
            'subtotal' => 28000,
        ]);
        OrderDetail::create([
            'order_id' => $o2->id,
            'menu_id' => $createdMenus['cafe-latte']->id,
            'menu_name' => 'Cafe Latte',
            'price' => 24000,
            'quantity' => 1,
            'subtotal' => 24000,
        ]);
        OrderStatusHistory::create([
            'order_id' => $o2->id,
            'previous_status' => null,
            'new_status' => 'menunggu',
            'changed_by' => 'Sistem Pemesanan Digital',
            'notes' => 'Pesanan diterima server dan masuk antrean FIFO urutan #2.',
            'created_at' => $baseTime->copy()->addMinutes(2),
        ]);

        // Pelanggan C (Urutan FIFO #3 - Menunggu Antrean)
        $o3 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-003',
            'queue_number' => '003',
            'customer_name' => 'Pelanggan C (Citra)',
            'customer_phone' => '081234567803',
            'table_number' => '04',
            'customer_notes' => null,
            'total_price' => 36000,
            'status' => 'menunggu',
            'received_at' => $baseTime->copy()->addMinutes(5), // 10.05
            'tracking_token' => 'token-demo-003',
        ]);
        OrderDetail::create([
            'order_id' => $o3->id,
            'menu_id' => $createdMenus['rice-bowl-beef-blackpepper']->id,
            'menu_name' => 'Rice Bowl Beef Blackpepper',
            'price' => 36000,
            'quantity' => 1,
            'subtotal' => 36000,
        ]);
        OrderStatusHistory::create([
            'order_id' => $o3->id,
            'previous_status' => null,
            'new_status' => 'menunggu',
            'changed_by' => 'Sistem Pemesanan Digital',
            'notes' => 'Pesanan diterima server dan masuk antrean FIFO urutan #3.',
            'created_at' => $baseTime->copy()->addMinutes(5),
        ]);

        // Pelanggan D (Siap Diambil - Sudah Mendapatkan Notifikasi Suara & WhatsApp!)
        $o4 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-004',
            'queue_number' => '004',
            'customer_name' => 'Doni Saputra',
            'customer_phone' => '081234567804',
            'table_number' => '02',
            'customer_notes' => null,
            'total_price' => 28000,
            'status' => 'siap_diambil',
            'received_at' => $baseTime->copy()->subMinutes(15),
            'processed_at' => $baseTime->copy()->subMinutes(10),
            'ready_at' => $baseTime->copy()->subMinutes(2),
            'handled_by' => $pegawai->id,
            'tracking_token' => 'token-demo-004',
        ]);
        OrderDetail::create([
            'order_id' => $o4->id,
            'menu_id' => $createdMenus['karamel-macchiato-sekarsa']->id,
            'menu_name' => 'Karamel Macchiato Sekarsa',
            'price' => 28000,
            'quantity' => 1,
            'subtotal' => 28000,
        ]);
        OrderStatusHistory::create([
            'order_id' => $o4->id,
            'previous_status' => 'diproses',
            'new_status' => 'siap_diambil',
            'changed_by' => $pegawai->name,
            'notes' => 'Pesanan selesai dibuat dan siap diambil di counter. Notifikasi dikirimkan.',
            'created_at' => $baseTime->copy()->subMinutes(2),
        ]);
        Notification::create([
            'order_id' => $o4->id,
            'type' => 'web_sound',
            'recipient' => 'Doni Saputra',
            'message' => 'Notifikasi suara bel dan visual siap ambil dikirim ke perangkat pelanggan.',
            'status' => 'sent',
            'sent_at' => $baseTime->copy()->subMinutes(2),
        ]);
        Notification::create([
            'order_id' => $o4->id,
            'type' => 'whatsapp',
            'recipient' => '6281234567804',
            'message' => "Halo Kak Doni Saputra! ☕\n\nPesanan Anda di Sekarsa Coffee dengan Nomor Antrean *004* (ORD-" . date('Ymd') . "-004) sudah *SIAP DIAMBIL* di counter barista!\n\nSilakan tunjukkan pesan ini atau sebutkan nomor antrean Anda di counter. Terima kasih! ✨",
            'status' => 'sent',
            'sent_at' => $baseTime->copy()->subMinutes(2),
        ]);

        // Pelanggan E (Selesai Diambil)
        $o5 = Order::create([
            'order_number' => 'ORD-' . date('Ymd') . '-005',
            'queue_number' => '005',
            'customer_name' => 'Eka Pratama',
            'customer_phone' => '081234567805',
            'table_number' => '05',
            'customer_notes' => null,
            'total_price' => 40000,
            'status' => 'selesai',
            'received_at' => $baseTime->copy()->subHours(2),
            'processed_at' => $baseTime->copy()->subHours(2)->addMinutes(3),
            'ready_at' => $baseTime->copy()->subHours(2)->addMinutes(12),
            'completed_at' => $baseTime->copy()->subHours(2)->addMinutes(15),
            'handled_by' => $pegawai->id,
            'tracking_token' => 'token-demo-005',
        ]);
        OrderDetail::create([
            'order_id' => $o5->id,
            'menu_id' => $createdMenus['americano-sekarsa']->id,
            'menu_name' => 'Americano Sekarsa',
            'price' => 18000,
            'quantity' => 1,
            'subtotal' => 18000,
        ]);
        OrderDetail::create([
            'order_id' => $o5->id,
            'menu_id' => $createdMenus['truffle-fries-mayo']->id,
            'menu_name' => 'Truffle Fries Mayo',
            'price' => 22000,
            'quantity' => 1,
            'subtotal' => 22000,
        ]);
        OrderStatusHistory::create([
            'order_id' => $o5->id,
            'previous_status' => 'siap_diambil',
            'new_status' => 'selesai',
            'changed_by' => $pegawai->name,
            'notes' => 'Pesanan telah diambil oleh pelanggan. Siklus pesanan selesai.',
            'created_at' => $baseTime->copy()->subHours(2)->addMinutes(15),
        ]);
    }
}
