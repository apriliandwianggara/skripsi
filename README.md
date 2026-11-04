# SISTEM PEMESANAN MINUMAN MENGGUNAKAN ALGORITMA FIFO BERBASIS WEB & QR CODE
## Studi Kasus: Sekarsa Coffee

Sistem ini dikembangkan sebagai tugas akhir / skripsi untuk mengotomasi alur pemesanan minuman dan makanan pada kafe **Sekarsa Coffee**. Sistem memanfaatkan kode QR di setiap meja pelanggan untuk pemesanan mandiri (*self-ordering*), dipadukan dengan **Algoritma FIFO (First In First Out)** untuk mengatur antrean peracikan pesanan secara adil dan transparan.

---

## ☕ Karakteristik & Desain Sistem
- **Framework Backend:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL (XAMPP)
- **Desain UI:** Modern Cafe Aesthetic dengan warna dominan **Hijau Emerald (`#14532d`, `#16a34a`)**, aksen hangat kopi & amber.
- **Mobile First:** Dioptimalkan untuk tampilan smartphone pelanggan tanpa perlu login atau unduh aplikasi.
- **Real-Time Synchronous Chime:** Notifikasi suara bel kafe otomatis berbunyi di ponsel pelanggan saat pesanan siap diambil di meja kasir.
- **Printable Thermal Receipt & QR Standee:** Cetak nota transaksi 80mm/58mm dan cetak kartu QR Code meja untuk akrilik meja.

---

## 🔄 10 Tahap Alur Pemesanan Sekarsa Coffee

```
+-----------------------------------------------------------------------------------------+
|                                  ALUR PEMESANAN SEKARSA COFFEE                          |
+-----------------------------------------------------------------------------------------+
|  1. Scan QR Meja       : Pelanggan scan QR Code meja -> Otomatis masuk web pemesanan   |
|  2. Pilih Menu         : Pelanggan memilih menu, custom catatan, jumlah & klaim promo   |
|  3. Antrean FIFO Masuk : Pesanan tersimpan di database dengan nomor urut & waktu masuk  |
|  4. Kasir Proses       : Kasir/Barista memeriksa detail & menekan "Proses Pesanan"      |
|  5. Notif Diproses     : HP pelanggan real-time berubah: "Sedang Diracik Barista"       |
|  6. Pesanan Selesai    : Barista selesai meracik -> Kasir menekan "Pesanan Selesai"     |
|  7. Notif Siap & Bel   : HP pelanggan berdering (Suara Bel Kafe) & status "Siap Ambil"  |
|  8. Ambil Pesanan      : Pelanggan ke kasir menyebutkan/menunjukkan nomor pesanan       |
|  9. Pembayaran         : Pelanggan membayar via QRIS/Tunai -> Kasir ubah status "LUNAS" |
| 10. Duduk & Menikmati  : Pelanggan menikmati sajian di meja yang telah dipilih          |
+-----------------------------------------------------------------------------------------+
```

---

## 📊 Pemisahan Status (Sesuai Rancangan Skripsi)

Sistem memisahkan secara tegas antara status pengerjaan pesanan dan status keuangan/pembayaran:

| Status Pesanan | Keterangan |
|---|---|
| `menunggu` | Pesanan baru masuk dalam antrean pengerjaan FIFO |
| `diproses` | Pesanan sedang diracik oleh barista |
| `selesai` | Pesanan sudah siap diambil di meja kasir (Memicu suara bel) |
| `diambil` | Pesanan telah diserahkan dan diambil oleh pelanggan |
| `dibatalkan` | Pesanan dibatalkan jika ada kendala |

| Status Pembayaran | Keterangan |
|---|---|
| `belum_dibayar` | Belum ada pembayaran dari pelanggan |
| `menunggu_konfirmasi` | Pelanggan telah mengunggah bukti bayar QRIS |
| `lunas` | Kasir memverifikasi pembayaran uang tunai atau QRIS |
| `dibatalkan` | Transaksi pembayaran dibatalkan |

---

## 🧮 Implementasi Algoritma FIFO (First In First Out)

Algoritma FIFO diimplementasikan secara tersentralisasi pada kelas:
`app/Services/FifoQueueService.php`

### Prinsip Kerja FIFO:
1. Setiap pesanan yang masuk mencatat timestamp presisi pada kolom `waktu_masuk` di tabel `pesanans`.
2. Antrean pengerjaan diurutkan secara murni berdasarkan:
   $$\text{Queue Order} = \text{ORDER BY } \text{waktu\_masuk ASC, id ASC}$$
3. Pesanan dengan `waktu_masuk` paling awal akan mendapatkan **Urutan #1 (Prioritas Utama)**.
4. Estimasi waktu tunggu dihitung secara matematis berdasarkan posisi antrean di depannya:
   $$\text{Estimasi Waktu Tunggu (Menit)} = \text{Posisi Antrean} \times \text{Estimasi Menit Toko}$$

---

## 🔑 Kredensial Akun Default

Sistem telah dilengkapi data *seeder* awal untuk keperluan pengujian dan sidang skripsi:

| Peran (Role) | Username | Password | Keterangan Hak Akses |
|---|---|---|---|
| **Admin / Owner** | `admin` | `password123` | Akses penuh: Dashboard omzet, laporan penjualan, kelola meja & QR, kelola menu, promo, akun pengguna, log aktivitas. |
| **Kasir Utama** | `kasir` | `password123` | Antrean FIFO real-time, proses pesanan, selesai pesanan, pembayaran tunai/QRIS, cetak struk nota, toggle ketersediaan menu. |
| **Pelanggan** | *Tanpa Akun* | - | Buka langsung lewat Scan QR Code atau URL Meja. |

---

## 🚀 Cara Menjalankan Sistem di Komputer / Laptop

### 1. Pastikan XAMPP Aktif
Buka **XAMPP Control Panel**, lalu hidupkan module:
- **Apache** (Port 80 / 443)
- **MySQL** (Port 3306)

### 2. Konfigurasi Database
Database `sekarsa_coffee` telah dibuat di MySQL localhost (User: `root`, tanpa password).
Jika ingin me-reset database kembali ke kondisi awal demo:
```bash
php artisan migrate:fresh --seed
```

### 3. Mengakses Sistem

#### A. Melalui Web Server Apache XAMPP (Direkomendasikan):
- **Tampilan Pelanggan (Scan QR Meja):**
  - [http://localhost/skripsi/public/](http://localhost/skripsi/public/)
  - [http://localhost/skripsi/public/meja/01](http://localhost/skripsi/public/meja/01) (Simulasi Meja 01)
  - [http://localhost/skripsi/public/meja/04](http://localhost/skripsi/public/meja/04) (Simulasi Meja 04)
- **Portal Masuk Kasir & Admin:**
  - [http://localhost/skripsi/public/login](http://localhost/skripsi/public/login)

#### B. Melalui PHP Built-in Server:
```bash
php artisan serve
```
Akses di: `http://localhost:8000`

---

## 📝 Skenario Demonstrasi Sidang Skripsi

Untuk mendemonstrasikan sistem ini di hadapan dosen penguji:

1. **Buka 2 Jendela Browser Berdampingan:**
   - **Jendela Kiri (HP Pelanggan):** Buka `http://localhost/skripsi/public/meja/02`
   - **Jendela Kanan (Dashboard Kasir):** Buka `http://localhost/skripsi/public/login`, masuk sebagai `kasir` (`password123`).
2. **Kirim Pesanan Baru di Jendela Pelanggan:**
   - Pilih menu *Kopi Susu Sekarsa* dan *Truffle Fries Mayo*.
   - Masukkan kode promo `SEKARSA15` (Diskon 15%).
   - Tulis catatan: *"Less sugar, mayo dipisah"*.
   - Klik **Kirim Pesanan**.
3. **Amati Algoritma FIFO di Jendela Kasir:**
   - Dalam hitungan detik (live polling), pesanan baru muncul pada antrean FIFO dengan nomor urut berikutnya.
   - Perhatikan posisi antrean diurutkan berdasarkan waktu pemesanan.
4. **Proses & Selesaikan Pesanan di Jendela Kasir:**
   - Kasir menekan tombol **"Proses Pesanan"** &rarr; Di HP pelanggan status berubah jadi *"Sedang Diracik"*.
   - Kasir menekan tombol **"Pesanan Selesai"** &rarr; Di HP pelanggan langsung berbunyi **suara bel kafe (chime)** dan muncul notifikasi *"Pesanan Siap Diambil di Meja Kasir"*.
5. **Pembayaran & Cetak Nota:**
   - Kasir menekan tombol **"Bayar (Lunas)"**, masukkan uang tunai / pilih QRIS, sistem otomatis menghitung kembalian.
   - Klik **"Cetak Struk"** untuk melihat nota pembayaran format thermal print.
   - Tekan **"Serahkan (Diambil)"** untuk menyelesaikan seluruh siklus pesanan.
6. **Buka Menu Admin / Owner (`admin` / `password123`):**
   - Tunjukkan grafik penjualan 7 hari, menu terlaris, rekap pendapatan, dan cetak laporan penjualan.
   - Tunjukkan menu **"Meja & QR Code"** untuk memperlihatkan lembar cetak akrilik standee QR code meja.
   - Tunjukkan **"Log Aktivitas"** sebagai bukti sistem mencatat riwayat perubahan harga dan operasional.
