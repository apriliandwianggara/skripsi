<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Meja;
use App\Models\Kategori;
use App\Models\Menu;
use App\Models\Promo;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\Pengaturan;
use App\Models\AktivitasLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pengaturan Cafe
        $pengaturan = Pengaturan::create([
            'nama_cafe' => 'Sekarsa Coffee',
            'tagline' => 'Warmth, Taste & Harmony in Every Sip',
            'alamat' => 'Jl. Kaliurang KM 5.8 No. 24, Manggung, Caturtunggal, Sleman, D.I. Yogyakarta 55281',
            'telepon' => '0812-3456-7890',
            'instagram' => '@sekarsacoffee',
            'pesan_struk' => 'Terima kasih atas pesanan Anda di Sekarsa Coffee! Bagikan momen ngopi Anda di Instagram @sekarsacoffee.',
            'estimasi_menit_per_antrean' => 4,
        ]);

        // 2. Akun Pengguna (Owner & Kasir)
        $admin = User::create([
            'name' => 'Owner Sekarsa',
            'username' => 'admin',
            'email' => 'admin@sekarsacoffee.com',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $kasir = User::create([
            'name' => 'Budi Santoso (Kasir)',
            'username' => 'kasir',
            'email' => 'kasir@sekarsacoffee.com',
            'phone' => '081298765432',
            'role' => 'kasir',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // 3. Meja Pelanggan
        $mejas = [
            ['nomor_meja' => '01', 'kapasitas' => 2, 'lokasi' => 'Indoor Bar Area', 'status' => 'tersedia'],
            ['nomor_meja' => '02', 'kapasitas' => 2, 'lokasi' => 'Indoor Bar Area', 'status' => 'tersedia'],
            ['nomor_meja' => '03', 'kapasitas' => 4, 'lokasi' => 'Indoor Main Hall (AC)', 'status' => 'tersedia'],
            ['nomor_meja' => '04', 'kapasitas' => 4, 'lokasi' => 'Indoor Main Hall (AC)', 'status' => 'tersedia'],
            ['nomor_meja' => '05', 'kapasitas' => 4, 'lokasi' => 'Indoor Main Hall (AC)', 'status' => 'tersedia'],
            ['nomor_meja' => '06', 'kapasitas' => 6, 'lokasi' => 'Outdoor Garden Smoking', 'status' => 'tersedia'],
            ['nomor_meja' => '07', 'kapasitas' => 4, 'lokasi' => 'Outdoor Garden Smoking', 'status' => 'tersedia'],
            ['nomor_meja' => '08', 'kapasitas' => 4, 'lokasi' => 'Outdoor Garden Smoking', 'status' => 'tersedia'],
            ['nomor_meja' => '09', 'kapasitas' => 6, 'lokasi' => 'Lantai 2 Mezzanine', 'status' => 'tersedia'],
            ['nomor_meja' => '10', 'kapasitas' => 8, 'lokasi' => 'VIP Meeting Room', 'status' => 'tersedia'],
        ];

        $mejaModels = [];
        foreach ($mejas as $m) {
            $mejaModels[$m['nomor_meja']] = Meja::create([
                'nomor_meja' => $m['nomor_meja'],
                'kapasitas' => $m['kapasitas'],
                'lokasi' => $m['lokasi'],
                'status' => $m['status'],
                'qr_token' => Str::random(32),
            ]);
        }

        // 4. Kategori Menu
        $katSignature = Kategori::create([
            'nama' => 'Signature Coffee',
            'slug' => 'signature-coffee',
            'deskripsi' => 'Kopi racikan khas barista Sekarsa dengan biji kopi pilihan nusantara',
            'icon' => 'coffee',
            'urutan' => 1,
            'is_active' => true,
        ]);

        $katEspresso = Kategori::create([
            'nama' => 'Espresso Based',
            'slug' => 'espresso-based',
            'deskripsi' => 'Klasik espresso murni dan kombinasi susu segar berkualitas',
            'icon' => 'cup-hot',
            'urutan' => 2,
            'is_active' => true,
        ]);

        $katNonCoffee = Kategori::create([
            'nama' => 'Non-Coffee & Tea',
            'slug' => 'non-coffee-tea',
            'deskripsi' => 'Pilihan segar non-kopi mulai dari artisan tea hingga artisan matcha',
            'icon' => 'droplet',
            'urutan' => 3,
            'is_active' => true,
        ]);

        $katMakanan = Kategori::create([
            'nama' => 'Makanan Utama',
            'slug' => 'makanan-utama',
            'deskripsi' => 'Sajian makanan hangat mengenyangkan teman santai Anda',
            'icon' => 'bowl-food',
            'urutan' => 4,
            'is_active' => true,
        ]);

        $katSnack = Kategori::create([
            'nama' => 'Snack & Pastry',
            'slug' => 'snack-pastry',
            'deskripsi' => 'Camilan renyah dan pastry gurih manis pendamping ngopi',
            'icon' => 'cookie',
            'urutan' => 5,
            'is_active' => true,
        ]);

        // 5. Menu Items
        $menus = [
            // Signature
            [
                'kategori_id' => $katSignature->id,
                'nama' => 'Kopi Susu Sekarsa',
                'slug' => 'kopi-susu-sekarsa',
                'deskripsi' => 'House blend espresso signature Sekarsa dipadukan dengan susu creamy dan gula aren murni.',
                'harga' => 22000,
                'harga_promo' => null,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 1,
            ],
            [
                'kategori_id' => $katSignature->id,
                'nama' => 'Karamel Macchiato Sekarsa',
                'slug' => 'karamel-macchiato-sekarsa',
                'deskripsi' => 'Espresso layered milk dengan saus karamel artisanal lembut yang lumer di lidah.',
                'harga' => 28000,
                'harga_promo' => 25000,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 2,
            ],
            [
                'kategori_id' => $katSignature->id,
                'nama' => 'Cold Brew Berry Breeze',
                'slug' => 'cold-brew-berry-breeze',
                'deskripsi' => 'Cold brew steeping 16 jam dengan sentuhan ekstrak strawberry alami dan tonic segar.',
                'harga' => 26000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 3,
            ],
            [
                'kategori_id' => $katSignature->id,
                'nama' => 'Avocado Coffee Float',
                'slug' => 'avocado-coffee-float',
                'deskripsi' => 'Jus alpukat mentega segar disiram single shot espresso dan vanilla ice cream.',
                'harga' => 29000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 4,
            ],

            // Espresso
            [
                'kategori_id' => $katEspresso->id,
                'nama' => 'Americano Sekarsa (Hot/Ice)',
                'slug' => 'americano-sekarsa',
                'deskripsi' => 'Double shot espresso murni dengan air mineral dingin/panas, clean dan bold.',
                'harga' => 18000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 1,
            ],
            [
                'kategori_id' => $katEspresso->id,
                'nama' => 'Cafe Latte',
                'slug' => 'cafe-latte',
                'deskripsi' => 'Steamed fresh milk dengan single shot espresso dan velvety microfoam.',
                'harga' => 24000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 2,
            ],
            [
                'kategori_id' => $katEspresso->id,
                'nama' => 'Cappuccino Brulee',
                'slug' => 'cappuccino-brulee',
                'deskripsi' => 'Cappuccino hangat dengan lapisan foam tebal dan taburan brown sugar karamel bakar di atasnya.',
                'harga' => 26000,
                'harga_promo' => null,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 3,
            ],

            // Non-Coffee
            [
                'kategori_id' => $katNonCoffee->id,
                'nama' => 'Matcha Oat Latte',
                'slug' => 'matcha-oat-latte',
                'deskripsi' => 'Pure Uji Matcha import dipadukan dengan creamy oat milk bebas laktosa.',
                'harga' => 28000,
                'harga_promo' => 24000,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 1,
            ],
            [
                'kategori_id' => $katNonCoffee->id,
                'nama' => 'Belgian Dark Chocolate',
                'slug' => 'belgian-dark-chocolate',
                'deskripsi' => 'Cokelat Belgia pekat 70% dengan susu segar, rasa cokelat mewah dan tidak serik.',
                'harga' => 25000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 2,
            ],
            [
                'kategori_id' => $katNonCoffee->id,
                'nama' => 'Artisan Lychee Blossom Tea',
                'slug' => 'artisan-lychee-blossom-tea',
                'deskripsi' => 'Teh hitam beraroma bunga dengan buah leci utuh dan daun mint segar.',
                'harga' => 22000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 3,
            ],

            // Makanan
            [
                'kategori_id' => $katMakanan->id,
                'nama' => 'Rice Bowl Beef Blackpepper',
                'slug' => 'rice-bowl-beef-blackpepper',
                'deskripsi' => 'Irisan daging sapi tenderloin empuk dengan saus lada hitam khas Sekarsa, telur mata sapi & nasi hangat.',
                'harga' => 36000,
                'harga_promo' => null,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 1,
            ],
            [
                'kategori_id' => $katMakanan->id,
                'nama' => 'Chicken Katsu Sambal Matah',
                'slug' => 'chicken-katsu-sambal-matah',
                'deskripsi' => 'Ayam katsu krispi emas disajikan bersama sambal matah Bali segar dan nasi pulen.',
                'harga' => 32000,
                'harga_promo' => 29000,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 2,
            ],
            [
                'kategori_id' => $katMakanan->id,
                'nama' => 'Spaghetti Aglio Olio Smoked Beef',
                'slug' => 'spaghetti-aglio-olio-smoked-beef',
                'deskripsi' => 'Pasta al dente ditumis minyak zaitun murni, bawang putih, cabai rawit, dan smoked beef premium.',
                'harga' => 30000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 3,
            ],

            // Snack
            [
                'kategori_id' => $katSnack->id,
                'nama' => 'Truffle Fries Mayo',
                'slug' => 'truffle-fries-mayo',
                'deskripsi' => 'Kentang goreng renyah wangi minyak truffle bertabur keju parmesan & saus garlic mayo.',
                'harga' => 22000,
                'harga_promo' => null,
                'is_rekomendasi' => true,
                'status_stok' => 'tersedia',
                'urutan' => 1,
            ],
            [
                'kategori_id' => $katSnack->id,
                'nama' => 'Churros Cinnamon Sugar Dip',
                'slug' => 'churros-cinnamon-sugar-dip',
                'deskripsi' => '6 potong churros Spanyol garing luar lembut dalam dengan baluran gula kayu manis & saus dark chocolate.',
                'harga' => 22000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 2,
            ],
            [
                'kategori_id' => $katSnack->id,
                'nama' => 'Pisang Goreng Wijen Madu',
                'slug' => 'pisang-goreng-wijen-madu',
                'deskripsi' => 'Pisang raja manis berselimut tepung krispi wijen disiram madu murni dan parutan keju cheddar.',
                'harga' => 20000,
                'harga_promo' => null,
                'is_rekomendasi' => false,
                'status_stok' => 'tersedia',
                'urutan' => 3,
            ],
        ];

        $createdMenus = [];
        foreach ($menus as $m) {
            $createdMenus[$m['slug']] = Menu::create($m);
        }

        // 6. Promo Berjalan
        $promo1 = Promo::create([
            'kode_promo' => 'SEKARSA15',
            'judul' => 'Promo Diskon 15% Semua Menu',
            'deskripsi' => 'Potongan 15% khusus pemesanan lewat scan QR code meja, minimal transaksi Rp 40.000.',
            'tipe' => 'persen',
            'nilai_potongan' => 15,
            'min_pembelian' => 40000,
            'maksimal_potongan' => 15000,
            'tgl_mulai' => Carbon::now()->subDays(5),
            'tgl_selesai' => Carbon::now()->addDays(25),
            'is_active' => true,
        ]);

        $promo2 = Promo::create([
            'kode_promo' => 'HEMAT5K',
            'judul' => 'Potongan Langsung Rp 5.000',
            'deskripsi' => 'Diskon Rp 5.000 tanpa batasan menu untuk minimum belanja Rp 25.000.',
            'tipe' => 'nominal',
            'nilai_potongan' => 5000,
            'min_pembelian' => 25000,
            'maksimal_potongan' => null,
            'tgl_mulai' => Carbon::now()->subDays(10),
            'tgl_selesai' => Carbon::now()->addDays(20),
            'is_active' => true,
        ]);

        // 7. Data Pesanan Demonstrasi FIFO (Sesuai skenario skripsi yang diinginkan user)
        // Nomor A001: Masuk jam 10:01 -> Status: 'diproses' (Urutan 1)
        // Nomor A002: Masuk jam 10:03 -> Status: 'menunggu' (Urutan 2)
        // Nomor A003: Masuk jam 10:05 -> Status: 'menunggu' (Urutan 3)
        // Nomor A004: Masuk jam 10:07 -> Status: 'menunggu' (Urutan 4)

        $now = Carbon::now();

        // A001 (Sedang Diproses)
        $p1 = Pesanan::create([
            'nomor_pesanan' => 'A001',
            'meja_id' => $mejaModels['01']->id,
            'nama_pelanggan' => 'Andi Pratama',
            'catatan_umum' => 'Pesanan dine-in, tolong sediakan air putih jika ada.',
            'subtotal' => 44000,
            'promo_id' => $promo2->id,
            'diskon_promo' => 5000,
            'total_akhir' => 39000,
            'status_pesanan' => 'diproses',
            'status_pembayaran' => 'belum_dibayar',
            'metode_pembayaran' => 'qris',
            'waktu_masuk' => $now->copy()->subMinutes(12),
            'waktu_diproses' => $now->copy()->subMinutes(8),
            'session_token' => 'session-demo-a001',
        ]);
        PesananItem::create([
            'pesanan_id' => $p1->id,
            'menu_id' => $createdMenus['kopi-susu-sekarsa']->id,
            'nama_menu' => 'Kopi Susu Sekarsa',
            'harga_satuan' => 22000,
            'jumlah' => 1,
            'subtotal' => 22000,
            'catatan' => 'Less ice, normal sugar',
        ]);
        PesananItem::create([
            'pesanan_id' => $p1->id,
            'menu_id' => $createdMenus['truffle-fries-mayo']->id,
            'nama_menu' => 'Truffle Fries Mayo',
            'harga_satuan' => 22000,
            'jumlah' => 1,
            'subtotal' => 22000,
            'catatan' => 'Mayo dipisah',
        ]);

        // A002 (Menunggu - Urutan 2)
        $p2 = Pesanan::create([
            'nomor_pesanan' => 'A002',
            'meja_id' => $mejaModels['03']->id,
            'nama_pelanggan' => 'Citra Lestari',
            'catatan_umum' => 'Tolong diantar ke meja 3',
            'subtotal' => 56000,
            'promo_id' => $promo1->id,
            'diskon_promo' => 8400,
            'total_akhir' => 47600,
            'status_pesanan' => 'menunggu',
            'status_pembayaran' => 'belum_dibayar',
            'waktu_masuk' => $now->copy()->subMinutes(9),
            'session_token' => 'session-demo-a002',
        ]);
        PesananItem::create([
            'pesanan_id' => $p2->id,
            'menu_id' => $createdMenus['matcha-oat-latte']->id,
            'nama_menu' => 'Matcha Oat Latte',
            'harga_satuan' => 24000,
            'jumlah' => 1,
            'subtotal' => 24000,
            'catatan' => 'Dingin, tanpa gula tambahan',
        ]);
        PesananItem::create([
            'pesanan_id' => $p2->id,
            'menu_id' => $createdMenus['chicken-katsu-sambal-matah']->id,
            'nama_menu' => 'Chicken Katsu Sambal Matah',
            'harga_satuan' => 29000,
            'jumlah' => 1,
            'subtotal' => 29000,
            'catatan' => 'Sambal agak banyak',
        ]);

        // A003 (Menunggu - Urutan 3)
        $p3 = Pesanan::create([
            'nomor_pesanan' => 'A003',
            'meja_id' => $mejaModels['06']->id,
            'nama_pelanggan' => 'Rian Hidayat',
            'catatan_umum' => null,
            'subtotal' => 40000,
            'promo_id' => null,
            'diskon_promo' => 0,
            'total_akhir' => 40000,
            'status_pesanan' => 'menunggu',
            'status_pembayaran' => 'belum_dibayar',
            'waktu_masuk' => $now->copy()->subMinutes(6),
            'session_token' => 'session-demo-a003',
        ]);
        PesananItem::create([
            'pesanan_id' => $p3->id,
            'menu_id' => $createdMenus['pisang-goreng-wijen-madu']->id,
            'nama_menu' => 'Pisang Goreng Wijen Madu',
            'harga_satuan' => 20000,
            'jumlah' => 2,
            'subtotal' => 40000,
            'catatan' => null,
        ]);

        // A004 (Menunggu - Urutan 4)
        $p4 = Pesanan::create([
            'nomor_pesanan' => 'A004',
            'meja_id' => $mejaModels['04']->id,
            'nama_pelanggan' => 'Maya Putri',
            'catatan_umum' => null,
            'subtotal' => 26000,
            'promo_id' => null,
            'diskon_promo' => 0,
            'total_akhir' => 26000,
            'status_pesanan' => 'menunggu',
            'status_pembayaran' => 'belum_dibayar',
            'waktu_masuk' => $now->copy()->subMinutes(2),
            'session_token' => 'session-demo-a004',
        ]);
        PesananItem::create([
            'pesanan_id' => $p4->id,
            'menu_id' => $createdMenus['cappuccino-brulee']->id,
            'nama_menu' => 'Cappuccino Brulee',
            'harga_satuan' => 26000,
            'jumlah' => 1,
            'subtotal' => 26000,
            'catatan' => 'Extra hot',
        ]);

        // 8. Beberapa pesanan yang SUDAH SELESAI & LUNAS hari ini (untuk laporan & grafik penjualan)
        for ($i = 1; $i <= 5; $i++) {
            $selesaiOrder = Pesanan::create([
                'nomor_pesanan' => 'A00' . (4 + $i),
                'meja_id' => $mejaModels['0' . $i]->id,
                'nama_pelanggan' => 'Pelanggan #' . $i,
                'catatan_umum' => null,
                'subtotal' => 45000,
                'promo_id' => null,
                'diskon_promo' => 0,
                'total_akhir' => 45000,
                'status_pesanan' => 'diambil',
                'status_pembayaran' => 'lunas',
                'metode_pembayaran' => ($i % 2 === 0) ? 'qris' : 'tunai',
                'jumlah_bayar' => 50000,
                'kembalian' => 5000,
                'waktu_masuk' => $now->copy()->subHours(5)->addMinutes($i * 25),
                'waktu_diproses' => $now->copy()->subHours(5)->addMinutes($i * 25 + 3),
                'waktu_selesai' => $now->copy()->subHours(5)->addMinutes($i * 25 + 12),
                'waktu_diambil' => $now->copy()->subHours(5)->addMinutes($i * 25 + 15),
                'waktu_bayar' => $now->copy()->subHours(5)->addMinutes($i * 25 + 15),
                'kasir_id' => $kasir->id,
            ]);

            PesananItem::create([
                'pesanan_id' => $selesaiOrder->id,
                'menu_id' => $createdMenus['kopi-susu-sekarsa']->id,
                'nama_menu' => 'Kopi Susu Sekarsa',
                'harga_satuan' => 22000,
                'jumlah' => 1,
                'subtotal' => 22000,
            ]);
            PesananItem::create([
                'pesanan_id' => $selesaiOrder->id,
                'menu_id' => $createdMenus['truffle-fries-mayo']->id,
                'nama_menu' => 'Truffle Fries Mayo',
                'harga_satuan' => 22000,
                'jumlah' => 1,
                'subtotal' => 22000,
            ]);
        }

        // 9. Catat Log Aktivitas Awal
        AktivitasLog::create([
            'user_id' => $admin->id,
            'user_name' => $admin->name . ' (Admin)',
            'aksi' => 'INISIALISASI_SISTEM',
            'deskripsi' => 'Inisialisasi sistem pemesanan Sekarsa Coffee, data menu, meja, dan pengaturan toko berhasil dibuat.',
            'ip_address' => '127.0.0.1',
        ]);
    }
}
