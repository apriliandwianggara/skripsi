<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Table as TableStyle;
use PhpOffice\PhpWord\IOFactory;

echo "Memulai penyusunan Dokumen Skripsi Master Lengkap (Dengan 8 Gambar Visual & Bedah Semua File)...\n";

Settings::setOutputEscapingEnabled(true);

$phpWord = new PhpWord();

// Konfigurasi Default Dokumen (A4, TNR 11.5)
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(11.5);

// Margin Standar Karya Tulis Ilmiah: Kiri 4cm (2268 twip), Atas/Kanan/Bawah 3cm (1701 twip)
$sectionStyle = [
    'paperSize'    => 'A4',
    'marginLeft'   => 2268,
    'marginRight'  => 1701,
    'marginTop'    => 1701,
    'marginBottom' => 1701,
    'headerHeight' => 720,
    'footerHeight' => 720,
];

// Gaya Heading
$phpWord->addTitleStyle(1, ['name' => 'Times New Roman', 'size' => 15, 'bold' => true, 'color' => '0f172a'], ['spaceAfter' => 200, 'spaceBefore' => 240]);
$phpWord->addTitleStyle(2, ['name' => 'Times New Roman', 'size' => 12.5, 'bold' => true, 'color' => '166534'], ['spaceAfter' => 140, 'spaceBefore' => 180]);
$phpWord->addTitleStyle(3, ['name' => 'Times New Roman', 'size' => 11.5, 'bold' => true, 'color' => '1e293b'], ['spaceAfter' => 100, 'spaceBefore' => 120]);

$pJustify = ['alignment' => Jc::BOTH, 'spaceAfter' => 120, 'lineHeight' => 1.25];
$pCenter  = ['alignment' => Jc::CENTER, 'spaceAfter' => 120];
$pLeft    = ['alignment' => Jc::START, 'spaceAfter' => 100];

// Format Tabel Akademik
$tableStyleHeader = ['bgColor' => '166534', 'valign' => 'center'];
$tableStyleSubHeader = ['bgColor' => '047857', 'valign' => 'center'];
$tableStyleRowEven = ['bgColor' => 'F8FAFC'];
$tableStyleRowOdd  = ['bgColor' => 'FFFFFF'];

$fontHeader    = ['name' => 'Times New Roman', 'size' => 10, 'bold' => true, 'color' => 'FFFFFF'];
$fontSubHeader = ['name' => 'Times New Roman', 'size' => 9.5, 'bold' => true, 'color' => 'FFFFFF'];
$fontCell      = ['name' => 'Times New Roman', 'size' => 9.5, 'color' => '1E293B'];
$fontCellBold  = ['name' => 'Times New Roman', 'size' => 9.5, 'bold' => true, 'color' => '0F172A'];
$fontCode      = ['name' => 'Consolas', 'size' => 9, 'color' => '0F172A'];

$tableBorders = [
    'borderColor' => 'CBD5E1',
    'borderSize'  => 4,
    'cellMargin'  => 80,
    'alignment'   => JcTable::CENTER
];
$phpWord->addTableStyle('StandardTable', $tableBorders);

$section = $phpWord->addSection($sectionStyle);

// -------------------------------------------------------------
// COVER RESMI SKRIPSI
// -------------------------------------------------------------
$section->addTextBreak(2);
$section->addText("DOKUMENTASI LENGKAP KODE PROGRAM & PERANCANGAN SISTEM SKRIPSI", ['size' => 16, 'bold' => true, 'color' => '0f172a'], $pCenter);
$section->addText("SISTEM INFORMASI / TEKNIK INFORMATIKA", ['size' => 12, 'bold' => true, 'color' => '166534'], $pCenter);

$section->addTextBreak(1);
$section->addText("Judul Penelitian:", ['size' => 11, 'italic' => true], $pCenter);
$section->addText("“Perancangan Sistem Pemesanan Digital dan Notifikasi Pengambilan Pesanan Berbasis Web dengan Algoritma First In First Out (FIFO) pada Sekarsa Coffee”", ['size' => 13, 'bold' => true, 'italic' => true, 'color' => '047857'], $pCenter);

$section->addTextBreak(2);

$metaTable = $section->addTable(['borderColor' => '94A3B8', 'borderSize' => 6, 'alignment' => JcTable::CENTER]);
$metaTable->addRow();
$metaCell = $metaTable->addCell(8500, ['bgColor' => 'F1F5F9', 'valign' => 'center']);
$metaCell->addText("INFORMASI SISTEM & METADATA PENELITIAN", ['size' => 10, 'bold' => true, 'color' => '0F172A'], $pCenter);
$metaCell->addText("• Live Hosting URL : http://sekarsacoffee.gt.tc", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Repository GitHub: https://github.com/apriliandwianggara/skripsi.git", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Framework & Stack: Laravel 11.x | PHP 8.2 | MariaDB / MySQL | Tailwind CSS", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Algoritma Utama  : First In First Out (FIFO) Queue Dispatcher Berbasis Timestamp", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Mekanisme Suara  : Web Audio API Oscillator (Dual-Chime Alarm Berulang)", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Integrasi VS Code: SFTP Auto-Upload on Save (Host: ftpupload.net)", ['size' => 9.5, 'color' => '1E293B']);

$section->addPageBreak();

// -------------------------------------------------------------
// DAFTAR ISI RESMI
// -------------------------------------------------------------
$section->addTitle("DAFTAR ISI DOKUMEN", 1);
$section->addText("Dokumen ini memuat dokumentasi teknis dan pemodelan sistem komprehensif, mencakup penjelasan mendalam seluruh kode program 1 per 1, alasan ilmiah pemilihan arsitektur, serta 8 gambar diagram visual berstandar akademik internasional.", ['size' => 10.5, 'italic' => true], $pJustify);

$toc = [
    "BAB I : IKHTISAR SISTEM & PEMILIHAN TEKNOLOGI",
    "   1.1 Latar Belakang & Identifikasi Masalah",
    "   1.2 Solusi yang Diterapkan dalam Sistem Sekarsa Coffee",
    "   1.3 Bedah Stack Teknologi & Alasan Ilmiah Pemilihan",
    "BAB II : PENJELASAN SEMUA KODE PROGRAM 1 PER 1 (LENGKAP)",
    "   2.1 Struktur Hierarki Folder Proyek Laravel 11",
    "   2.2 File Konfigurasi Inti (.env, composer.json, package.json, sftp.json)",
    "   2.3 File Bootstrap & Middleware (app.php, RoleMiddleware.php)",
    "   2.4 Service Layer (FifoQueueService, QrCodeService, NotificationService)",
    "   2.5 Controller Pelanggan (CustomerOrderController.php)",
    "   2.6 Controller Pos Barista / Kasir (PegawaiDashboardController.php)",
    "   2.7 Controller Administrator & Pemilik (Dashboard, Menu, Category, Order, Setting, User)",
    "   2.8 Controller Autentikasi (AuthController.php)",
    "   2.9 Layer Model & Relasi Basis Data Eloquent",
    "   2.10 Layer Routing (routes/web.php)",
    "   2.11 Layer Antarmuka Tampilan (Blade Views: Customer, Pegawai, Admin, Layouts)",
    "   2.12 Layer Migrasi Basis Data (Skema Tabel & Penambahan Kolom)",
    "BAB III : PERANCANGAN DIAGRAM SISTEM (STANDAR SKRIPSI DENGAN GAMBAR VISUAL)",
    "   3.1 Kamus Simbol Diagram Baku (ANSI/ISO 5807, Crow's Foot, UML 2.5)",
    "   3.2 Bagan Alir Dokumen (ASI / Flowmap Sistem Berjalan vs Diusulkan)",
    "   3.3 Flowchart Sistem (Alur Pelanggan & Alur Kasir/Barista FIFO)",
    "   3.4 Entity Relationship Diagram (ERD) & Kamus Data Relasional Lengkap",
    "   3.5 Unified Modeling Language (UML) Lengkap:",
    "       - 3.5.1 Use Case Diagram & Tabel Spesifikasi Skenario",
    "       - 3.5.2 Activity Diagram (Swimlane 3 Partisi)",
    "       - 3.5.3 Sequence Diagram (Penerbitan Nomor Antrean & Pemicu Alarm Suara)",
    "       - 3.5.4 Class Diagram (Arsitektur Model, Controller & Service Layer)",
    "BAB IV : PANDUAN PENGUJIAN BLACK-BOX & ATURAN BISNIS FIFO",
    "   4.1 Pembuktian Aturan Ketat Antrean Masuk FIFO (Anti-Penyerobotan)",
    "   4.2 Fleksibilitas Penyelesaian Pesanan Selesai (Service Time Independence)",
    "   4.3 Mekanisme Keamanan Single-Use QR Gate & Penghapusan Sesi",
    "   4.4 Alasan Akademik Penghapusan GPS Geofencing",
    "   4.5 Tabel Hasil Uji Coba Black-Box Sistem",
    "BAB V : KESIMPULAN & PETUNJUK SIDANG SKRIPSI",
    "   5.1 Kesimpulan Teknis",
    "   5.2 Petunjuk & Jawaban Menghadapi Pertanyaan Dosen Penguji Sidang"
];

foreach ($toc as $item) {
    if (str_starts_with($item, "BAB")) {
        $section->addText($item, ['bold' => true, 'color' => '166534', 'size' => 10.5]);
    } else {
        $section->addText($item, ['color' => '334155', 'size' => 9.5]);
    }
}

$section->addPageBreak();

// -------------------------------------------------------------
// BAB I: PENDAHULUAN & TEKNOLOGI
// -------------------------------------------------------------
$section->addTitle("BAB I : IKHTISAR SISTEM & PEMILIHAN TEKNOLOGI", 1);

$section->addTitle("1.1 Latar Belakang & Identifikasi Masalah", 2);
$section->addText("Sekarsa Coffee merupakan salah satu unit usaha kedai kopi modern dengan volume transaksi pelanggan yang fluktuatif, khususnya pada jam-jam sibuk (peak hours). Berdasarkan hasil observasi operasional awal, sistem konvensional (pemesanan kasir manual) menghadapi tiga permasalahan mendasar:", [], $pJustify);
$section->addText("1. Antrean Fisik yang Menumpuk di Meja Kasir: Pelanggan yang baru tiba harus berdiri berdesakan di depan meja kasir hanya untuk membaca lembar menu cetak dan menunggu kasir mengetik pesanan. Hal ini menyebabkan kemacetan di area pintu masuk kedai.", [], $pJustify);
$section->addText("2. Out-of-Order Execution (Penyerobotan Antrean): Pencatatan pesanan menggunakan kertas nota rawan tertukar, terselip, atau dikerjakan tidak sesuai urutan kedatangan. Pelanggan yang memesan lebih awal kerap kali mendapati pesanannya disajikan lebih lambat daripada pelanggan yang datang belakangan. Hal ini menimbulkan komplain dan merusak kepuasan pelanggan.", [], $pJustify);
$section->addText("3. Ketidakpastian Waktu Tunggu & Kegaduhan Panggilan: Pelanggan tidak memiliki transparansi mengenai berapa antrean di depannya dan berapa menit perkiraan minumannya selesai. Di sisi lain, barista harus berulang kali berteriak memanggil nama pelanggan, yang sering kali tidak terdengar di area outdoor kafe.", [], $pJustify);

$section->addTitle("1.2 Solusi yang Diterapkan dalam Sistem Sekarsa Coffee", 2);
$section->addText("Untuk menyelesaikan masalah tersebut, penelitian ini merancang sistem pemesanan digital berbasis web responsif yang mengintegrasikan algoritma First In First Out (FIFO) secara presisi dengan tiga pilar teknologi utama:", [], $pJustify);
$section->addText("• Standee Kode QR Permanen di Setiap Meja: Pelanggan langsung duduk di meja kafe dan memindai QR akrilik menggunakan kamera ponsel. Sistem secara otomatis membuka menu digital interaktif tanpa mengharuskan pelanggan mengunduh aplikasi tambahan dari Play Store atau App Store.", [], $pJustify);
$section->addText("• Algoritma FIFO Berbasis Waktu Server (Timestamp): Sistem mencatat waktu masuk pesanan secara presisi hingga satuan detik (kolom received_at) dan menerbitkan nomor antrean harian berurutan. Pos barista diwajibkan memproses pesanan paling awal terlebih dahulu, mencegah tindakan penyerobotan antrean.", [], $pJustify);
$section->addText("• Sistem Alarm Suara Otomatis Terintegrasi (Web Audio API): Ketika barista menyelesaikan racikan kopi dan mengubah status pesanan menjadi 'Siap Diambil', ponsel pelanggan secara otomatis memutar nada dering berdering keras (dual-tone chime) berulang-ulang sampai pelanggan menekan tombol 'Saya Ambil', memastikan kopi dinikmati selagi segar.", [], $pJustify);

$section->addTitle("1.3 Bedah Stack Teknologi & Alasan Ilmiah Pemilihan", 2);

$tableTech = $section->addTable('StandardTable');
$tableTech->addRow();
$tableTech->addCell(2000, $tableStyleHeader)->addText("Komponen Stack", $fontHeader, $pCenter);
$tableTech->addCell(2500, $tableStyleHeader)->addText("Teknologi Dipilih", $fontHeader, $pCenter);
$tableTech->addCell(4500, $tableStyleHeader)->addText("Alasan Ilmiah & Keunggulan Akademik", $fontHeader, $pCenter);

$techRows = [
    [
        "Bahasa Pemrograman",
        "PHP 8.2",
        "Menyediakan fitur Type Hinting ketat, JIT (Just-In-Time) compilation untuk eksekusi algoritma yang cepat, performa memori optimal, serta kompatibilitas penuh dengan server hosting modern."
    ],
    [
        "Framework Backend",
        "Laravel 11.x",
        "Mengadopsi pola arsitektur MVC (Model-View-Controller) dengan Service Layer terpisah. Memiliki ORM Eloquent yang kuat untuk relasi data, proteksi CSRF bawaan, Transaction Locking (lockForUpdate), serta struktur bootstrap modern yang bersih."
    ],
    [
        "Basis Data (RDBMS)",
        "MySQL / MariaDB (InnoDB)",
        "Mendukung transaksi database ACID (Atomicity, Consistency, Isolation, Durability) dan penguncian baris (Pessimistic Row Locking) yang mutlak dibutuhkan untuk mencegah antrean ganda (race condition) saat jam sibuk."
    ],
    [
        "Frontend Styling",
        "Tailwind CSS (CDN / Vite)",
        "Utility-first CSS framework yang memungkinkan pembuatan antarmuka responsif ramah seluler (mobile-first) secara cepat tanpa bloatware CSS eksternal."
    ],
    [
        "Mekanisme Notifikasi",
        "Web Audio API (Synthesizer)",
        "Menghasilkan nada dering audio murni langsung dari browser pelanggan tanpa bergantung pada layanan pihak ketiga berbayar (seperti Pusher/Firebase) dan kebal terhadap pemblokiran autoplay browser setelah ada interaksi klik."
    ],
    [
        "Keamanan Akses QR",
        "Strict QR Token Gate",
        "Memastikan pesanan hanya bisa dikirim oleh pelanggan yang berada di lokasi fisik kafe dengan memindai kode QR standee meja, menggantikan modul GPS yang terbukti sering gagal di area indoor."
    ]
];

foreach ($techRows as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableTech->addRow();
    $tableTech->addCell(2000, $rowStyle)->addText($row[0], $fontCellBold);
    $tableTech->addCell(2500, $rowStyle)->addText($row[1], $fontCell);
    $tableTech->addCell(4500, $rowStyle)->addText($row[2], $fontCell, $pJustify);
}

$section->addPageBreak();

// -------------------------------------------------------------
// BAB II: PENJELASAN SEMUA KODE PROGRAM 1 PER 1 (LENGKAP)
// -------------------------------------------------------------
$section->addTitle("BAB II : PENJELASAN SEMUA KODE PROGRAM 1 PER 1", 1);
$section->addText("Bab ini menyajikan bedah menyeluruh terhadap seluruh file dan folder penting yang menyusun sistem Sekarsa Coffee. Setiap file dibahas tujuannya, struktur kodingannya, serta alasan ilmiah mengapa sintaks tersebut digunakan.", [], $pJustify);

$section->addTitle("2.1 Struktur Hierarki Direktori Proyek", 2);
$treeString = "skripsi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── CategoryController.php       (Kelola Kategori Menu)
│   │   │   │   ├── DashboardController.php      (Statistik Omset & Evaluasi FIFO)
│   │   │   │   ├── MenuController.php           (Kelola Menu, Harga & Upload Gambar)
│   │   │   │   ├── OrderManagementController.php(Riwayat Transaksi & Filter Tanggal)
│   │   │   │   ├── SettingController.php        (Pengaturan Kafe & Cetak QR Meja)
│   │   │   │   └── UserController.php           (Manajemen Akun Kasir & Admin)
│   │   │   ├── Auth/
│   │   │   │   └── AuthController.php           (Autentikasi Login/Logout Pegawai)
│   │   │   ├── Customer/
│   │   │   │   └── CustomerOrderController.php  (Pemesanan, Validasi QR & Live Track)
│   │   │   └── Pegawai/
│   │   │       └── PegawaiDashboardController.php (Pos Operasional Barista FIFO)
│   │   └── Middleware/
│   │       └── RoleMiddleware.php               (Proteksi Hak Akses Role Admin/Kasir)
│   ├── Models/
│   │   ├── Category.php, Menu.php, Order.php, OrderDetail.php
│   │   ├── OrderStatusHistory.php, Setting.php, User.php, Notification.php
│   │   └── Providers/AppServiceProvider.php
│   └── Services/
│       ├── FifoQueueService.php                 (Jantung Algoritma Antrean FIFO)
│       ├── QrCodeService.php                    (Generator Vektor SVG QR Standee Meja)
│       └── NotificationService.php              (Layanan Pengiriman Notifikasi & WA)
├── bootstrap/
│   ├── app.php                                  (Konfigurasi Exception & Routing)
│   └── providers.php                            (Pendaftaran Service Providers)
├── config/
│   ├── app.php, database.php, session.php, auth.php, filesystems.php
├── database/
│   ├── migrations/                              (Skema Tabel Relasional Basis Data)
│   └── seeders/DatabaseSeeder.php               (Data Awal Kategori, Menu & Admin)
├── resources/
│   └── views/
│       ├── admin/                               (Tampilan Dasbor & CRUD Pemilik)
│       ├── customer/                            (Tampilan Menu & Layar Pelanggan)
│       ├── pegawai/                             (Tampilan Layar Pos Barista FIFO)
│       ├── auth/                                (Formulir Login Petugas)
│       └── layouts/                             (Master Layouts app.blade & admin.blade)
└── routes/
    └── web.php                                  (Deklarasi Endpoint URL & Hak Akses)";

$section->addText($treeString, ['name' => 'Consolas', 'size' => 8.5, 'color' => '0F172A']);

// Helper Pembahasan File
function addFileExplanation($section, $filePath, $purpose, $whyCode, $pJustify, $fontCode) {
    $section->addText("File: " . $filePath, ['name' => 'Consolas', 'size' => 10, 'bold' => true, 'color' => '166534']);
    $section->addText("• Tujuan File: " . $purpose, ['bold' => false], $pJustify);
    $section->addText("• Kenapa Kodingannya Seperti Ini & Kenapa Pakai Ini:", ['bold' => true], $pJustify);
    foreach ($whyCode as $bullet) {
        $section->addText("  - " . $bullet, ['size' => 9.5], $pJustify);
    }
    $section->addTextBreak(1);
}

$section->addTitle("2.2 File Konfigurasi Inti Proyek", 2);

addFileExplanation(
    $section,
    ".env",
    "Menyimpan konfigurasi environment sensitif aplikasi (kredensial database, URL aplikasi, mode debug, sesi) secara terisolasi.",
    [
        "Kenapa pakai .env: Memisahkan rahasia konfigurasi dari repositori kode publik (prinsip Twelve-Factor App). Variabel APP_ENV diset ke 'production' pada hosting agar pesan error fatal tidak membocorkan struktur kodingan ke pengunjung.",
        "Koneksi DB: Menggunakan DB_CONNECTION=mysql dengan socket atau host ftpupload / localhost sesuai lingkungan server."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    ".vscode/sftp.json",
    "Konfigurasi ekstensi VS Code SFTP untuk sinkronisasi otomatis kode dari editor lokal ke hosting InfinityFree.",
    [
        "Kenapa pakai ini: Mengaktifkan parameter 'uploadOnSave: true' sehingga setiap kali pengembang menekan Ctrl+S di VS Code, perubahan file langsung terunggah ke direktori htdocs/ di hosting secara real-time.",
        "Parameter 'ignore': Mengabaikan direktori .git, node_modules, dan vendor agar proses upload hemat bandwidth dan tidak membebani server FTP."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.3 File Bootstrap & Middleware", 2);

addFileExplanation(
    $section,
    "bootstrap/app.php",
    "Pusat inisialisasi framework Laravel 11 yang menggabungkan konfigurasi routing, middleware pipeline, dan penanganan exception global.",
    [
        "Kenapa pakai Laravel 11 app.php: Pada Laravel 11, struktur kernel lama (Http/Kernel.php) dihilangkan dan disederhanakan ke dalam builder withMiddleware() dan withExceptions().",
        "Penanganan CSRF: Memetakan route pengecualian jika ada webhook eksternal yang memerlukan bypass token CSRF, sekaligus mendaftarkan alias middleware seperti 'role'."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Middleware/RoleMiddleware.php",
    "Pencegat permintaan HTTP (Request Interceptor) untuk menegakkan kontrol akses berbasis peran (Role-Based Access Control / RBAC).",
    [
        "Kenapa dibuat seperti ini: Memeriksa atribut `auth()->user()->role`. Jika seorang pegawai dengan peran 'kasir' mencoba mengakses menu keuangan admin (/admin/reports atau /admin/settings), middleware langsung memblokir akses dan mengalihkan pengguna ke dasbor kasir dengan pesan flash 'Akses Ditolak'.",
        "Menjamin prinsip least privilege dalam keamanan sistem informasi."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.4 Service Layer (Jantung Logika Bisnis & Algoritma FIFO)", 2);

addFileExplanation(
    $section,
    "app/Services/FifoQueueService.php",
    "Implementasi murni algoritma antrean First In First Out (FIFO) dan perhitungan metrik antrean kafe.",
    [
        "Kueri Urutan FIFO (getActiveFifoQueue): Menggunakan klausa SQL `ORDER BY received_at ASC, id ASC`. Waktu masuknya pesanan ke server (`received_at`) adalah penentu mutlak prioritas, bukan waktu browser dibuka. Kolom `id ASC` berfungsi sebagai tie-breaker jika dua pesanan masuk pada detik yang identik.",
        "Penerbitan Nomor Antrean Harian (generateQueueNumber): Mereset nomor antrean setiap berganti tanggal (001, 002, dst.). Dengan format `str_pad(\$nextNumber, 3, '0', STR_PAD_LEFT)`, nomor antrean selalu berformat tiga digit yang seragam dan mudah dibaca kasir.",
        "Validasi Pemrosesan Ketat (canProcessOrder): Mengecek apakah ada pesanan lain yang berstatus 'menunggu' dengan waktu masuk lebih lama. Jika ada, sistem melarang kasir memproses pesanan tersebut sebelum pesanan terlama selesai diproses.",
        "Estimasi Waktu Tunggu Dinamis: Mengalikan sisa pesanan di depan dengan parameter `estimated_minutes_per_queue` dari tabel pengaturan kafe."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Services/QrCodeService.php",
    "Pembangkit kode QR meja beresolusi tinggi dalam format vektor SVG untuk dicetak pada standee akrilik meja kafe.",
    [
        "Kenapa pakai format SVG: Berbeda dengan PNG bitmap yang rentan pecah saat diperbesar, format SVG berbasis rumus matematika vektor sehingga saat dicetak dalam ukuran akrilik meja A5 atau kartu kecil, gambar barcode QR tetap tajam 100% dan mudah dipindai oleh kamera ponsel resolusi rendah sekalipun.",
        "Penyematan Token Keamanan: Otomatis membubuhkan query string rahasia `?token=...&meja=...` untuk memvalidasi bahwa pelanggan memang sedang duduk di meja fisik kafe."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Services/NotificationService.php",
    "Layanan pengiriman pesan notifikasi otomatis (WhatsApp API gateway & logging riwayat notifikasi).",
    [
        "Kenapa dipisahkan ke Service: Memisahkan logika koneksi API pihak ketiga dari Controller, sehingga jika vendor penyedia WhatsApp gateway berubah di kemudian hari, perubahan kode hanya dilakukan pada satu berkas ini tanpa mengganggu alur transaksi pesanan.",
        "Fail-Safe Exception Handling: Menggunakan blok try-catch agar jika kuota pesan WhatsApp habis atau jaringan internet gateway terputus, sistem lokal kafe tidak crash dan transaksi pemesanan tetap berjalan normal."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.5 Controller Pelanggan (Customer)", 2);

addFileExplanation(
    $section,
    "app/Http/Controllers/Customer/CustomerOrderController.php",
    "Mengontrol alur transaksi pelanggan mulai dari verifikasi pemindaian QR meja, pemilihan menu, submit keranjang, hingga layar konfirmasi dan pelacakan live status antrean.",
    [
        "Mekanisme Strict QR Gate: Method index() memeriksa apakah ada token QR meja yang valid di URL atau di sesi browser. Jika pelanggan mengetik URL manual tanpa memindai QR, akses langsung dialihkan ke halaman /akses-qr-diperlukan.",
        "Penerapan Database Transaction (DB::transaction): Seluruh proses pencatatan pesanan (insert tabel `orders` dan loop insert `order_details`) dibungkus dalam transaksi atomik. Jika salah satu query gagal, seluruh proses dibatalkan (rollback) sehingga tidak akan pernah terjadi pesanan tersimpan tanpa item sajian.",
        "Single-Use QR Session: Begitu pesanan sukses disimpan (DB::commit), sistem mengeksekusi `session()->forget(['qr_scanned', 'qr_token', 'table_number'])` dan menghapus cookie. Hal ini mencegah pelanggan menyalahgunakan link yang sama untuk memesan berkali-kali tanpa berada di meja.",
        "Endpoint getLiveStatus(): Mengembalikan payload JSON berisi status pesanan saat ini, nomor antrean, sisa pesanan di depan, dan estimasi waktu selesai untuk dikonsumsi oleh polling JavaScript di antarmuka pelanggan."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.6 Controller Pos Barista / Kasir (Pegawai)", 2);

addFileExplanation(
    $section,
    "app/Http/Controllers/Pegawai/PegawaiDashboardController.php",
    "Pusat kendali pos operasional barista untuk memonitor antrean FIFO, memproses pesanan, memicu alarm siap saji, dan menyelesaikan transaksi.",
    [
        "Penegakan Aturan Ketat FIFO (processOrder): Barista HANYA BISA memproses pesanan yang berada di urutan terdepan (paling awal masuk). Jika barista mencoba mengeklik tombol proses pada pesanan nomor 2 padahal pesanan nomor 1 masih berstatus 'menunggu', server menolak dengan respons HTTP 422 Unprocessable Entity dan memunculkan SweetAlert peringatan.",
        "Pessimistic Row Locking (Order::lockForUpdate): Mengunci baris data pesanan di tingkat database saat transaksi berlangsung. Jika dua kasir mengeklik tombol pesanan pada milidetik yang sama, transaksi kasir kedua mengantre hingga transaksi kasir pertama tuntas, mencegah status ganda (race condition).",
        "Fleksibilitas Penyelesaian Pesanan (readyOrder & completeOrder): Sekalipun urutan pengerjaan wajib berurutan sesuai FIFO, durasi pembuatan minuman berbeda-beda (misal espresso 2 menit vs blended coffee 7 menit). Barista diberi keleluasaan untuk mengubah status pesanan yang 'diproses' menjadi 'siap_diambil' dan 'selesai' sesuai urutan kesiapan riil di meja bar.",
        "Pemicu Alarm HP Pelanggan: Ketika status diubah ke 'siap_diambil', status pesanan di basis data ter-update. Polling di ponsel pelanggan yang membaca perubahan status ini langsung mengaktifkan sintaks Audio Oscillator (alarm berdering nyaring)."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.7 Controller Administrator & Pemilik", 2);

addFileExplanation(
    $section,
    "app/Http/Controllers/Admin/DashboardController.php",
    "Menyajikan metrik ringkasan kinerja penjualan kafe, statistik efisiensi algoritma FIFO, dan grafik transaksi harian/bulanan.",
    [
        "Kueri Agregasi Kinerja FIFO: Menghitung rata-rata waktu pengerjaan pesanan (durasi dari received_at hingga completed_at) untuk mengukur performa kecepatan barista.",
        "Menampilkan total omset kotor, jumlah pesanan sukses, dan perbandingan omset antar hari."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Controllers/Admin/MenuController.php",
    "Mengelola data master menu makanan dan minuman (CRUD: Tambah, Lihat, Edit, Hapus) serta upload foto sajian.",
    [
        "Penyimpanan Gambar Langsung (public/images/menus): File foto menu disimpan langsung ke direktori public/images/menus/ daripada ke symlink storage/app/public/. Hal ini menjamin foto menu selalu langsung muncul di server hosting gratis (InfinityFree) yang menonaktifkan fungsi php artisan storage:link.",
        "Validasi File: Memastikan file yang diunggah berupa citra valid (JPEG/PNG/WEBP) dengan batas ukuran maksimal 2MB untuk menjaga kecepatan loading menu di ponsel pelanggan."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Controllers/Admin/CategoryController.php",
    "Mengelola pengelompokan menu (kategori: Coffee, Non-Coffee, Meals, Snacks, Mocktails).",
    [
        "Kolom 'order': Menyediakan atribut urutan tampilan (sorting order) agar admin dapat menentukan kategori mana yang paling pertama dilihat pelanggan di layar ponsel.",
        "Fitur Slug Otomatis: Mengonversi nama kategori menjadi slug ramah URL menggunakan helper `Str::slug()`."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Controllers/Admin/OrderManagementController.php",
    "Mengelola seluruh rekaman riwayat transaksi pelanggan dengan filter rentang tanggal, status pesanan, dan pencarian nomor meja.",
    [
        "Fitur Pembatalan Pesanan: Memungkinkan admin membatalkan transaksi yang bermasalah disertai catatan alasan pembatalan yang tersimpan ke tabel audit `order_status_history`.",
        "Mendukung paginasi data (paginate(15)) untuk efisiensi memori server saat transaksi mencapai ribuan data."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Controllers/Admin/SettingController.php",
    "Mengatur parameter operasional kedai kopi, termasuk nama kafe, logo, waktu estimasi per antrean, token rahasia QR meja, dan tombol darurat 'Thesis Defense Mode'.",
    [
        "Penyimpanan Konfigurasi Dinamis (Key-Value Store): Seluruh parameter disimpan ke tabel `settings` dalam format teks terstruktur. Admin dapat mengubah estimasi waktu per cangkir kopi tanpa perlu merombak kodingan.",
        "Method generateQrTable(): Menghasilkan file cetak standee meja lengkap dengan nomor meja, logo Sekarsa Coffee, dan instruksi pemindaian untuk pelanggan."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Controllers/Admin/UserController.php",
    "Mengelola akun staf kedai kopi (Admin dan Kasir/Barista).",
    [
        "Hasing Kata Sandi: Menggunakan algoritma enkripsi satu arah `Hash::make()` (Bcrypt dengan cost factor standar Laravel) sehingga password tidak dapat didekripsi bahkan jika basis data bocor.",
        "Proteksi Akun Sendiri: Mencegah admin menghapus akunnya sendiri saat sedang login untuk menghindari hilangnya akses administratif sistem."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Http/Controllers/Auth/AuthController.php",
    "Menangani alur autentikasi petugas kafe (login, validasi kredensial, proteksi brute-force, dan logout aman).",
    [
        "Pemberian Tiket Sesi Otentik: Menggunakan `Auth::attempt(['username' => \$user, 'password' => \$pass, 'is_active' => true])`. Otomatis memeriksa kecocokan username, password, dan memastikan akun masih aktif.",
        "Regenerasi Sesi (session()->regenerate()): Mencegah serangan pembajakan sesi (Session Fixation Attack) dengan menerbitkan Session ID baru setiap kali login berhasil.",
        "Pengalihan Berdasarkan Peran: Jika role admin diarahkan ke /admin/dashboard, jika role kasir langsung diarahkan ke /pegawai/dashboard."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.8 Model & Layer Eloquent ORM", 2);

addFileExplanation(
    $section,
    "app/Models/Order.php",
    "Entitas utama basis data yang merepresentasikan transaksi pemesanan kopi oleh pelanggan.",
    [
        "Relasi details(): Mendefinisikan relasi One-to-Many ke model `OrderDetail` (`hasMany(OrderDetail::class)`).",
        "Relasi statusHistory(): Mendefinisikan relasi One-to-Many ke model `OrderStatusHistory` untuk audit trail.",
        "Scope activeFifo(): Kueri scope reusable untuk memfilter pesanan aktif ('menunggu' dan 'diproses') dengan pengurutan `orderBy('received_at', 'asc')->orderBy('id', 'asc')`.",
        "Accessor status_badge: Menghasilkan kode HTML warna badge (Kuning=Menunggu, Biru=Diproses, Hijau=Siap Diambil, Abu-abu=Selesai, Merah=Dibatalkan) untuk tampilan kasir."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Models/OrderDetail.php",
    "Merepresentasikan setiap item menu sajian yang dipesan dalam satu transaksi pesanan.",
    [
        "Relasi order(): BelongsTo ke model `Order` melalui foreign key `order_id`.",
        "Relasi menu(): BelongsTo ke model `Menu` melalui foreign key `menu_id`.",
        "Atribut Snapshot (menu_name & price): Menyimpan nama menu dan harga pada saat transaksi terjadi. Jika di masa depan harga menu diubah oleh admin, riwayat nota transaksi lama tidak akan terpengaruh (prinsip integritas data akuntansi)."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Models/Menu.php",
    "Merepresentasikan data sajian makanan dan minuman yang ditawarkan di Sekarsa Coffee.",
    [
        "Accessor image_url: Memeriksa fisik file gambar secara bertingkat: pertama di public/images/menus/, lalu storage/app/public/menus/, dan jika keduanya kosong, menggunakan placeholder SVG bawaan. Sistem kebal dari error gambar pecah (broken image).",
        "Relasi category(): BelongsTo ke model `Category`."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "app/Models/Setting.php",
    "Model pengambil dan penyimpan pengaturan global kafe.",
    [
        "Static Helper Setting::get(\$key, \$default): Memudahkan pemanggilan konfigurasi di bagian kodingan mana pun (misal: `Setting::get('cafe_name', 'Sekarsa Coffee')`) tanpa perlu menulis kueri SQL manual berulang kali."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.9 Layer Routing (routes/web.php)", 2);

addFileExplanation(
    $section,
    "routes/web.php",
    "Mendefinisikan seluruh pemetaan URL, metode HTTP (GET, POST), middleware proteksi, dan pengontrol yang bertugas.",
    [
        "Grup Publik Pelanggan: Menampung route pemesanan digital (`/pesan`, `/pesanan/{order}`, `/pesanan/{order}/status`, `/akses-qr-diperlukan`). Bebas dari autentikasi login tetapi dilindungi oleh QR token validation.",
        "Grup Otentikasi: Route login dan logout (`/login`, `/logout`).",
        "Grup Barista / Pegawai: Dilindungi middleware `auth` dan `role:kasir,admin` untuk pos monitoring dan pemrosesan pesanan FIFO (`/pegawai/dashboard`, `/pegawai/orders/{id}/process`, `/pegawai/orders/{id}/ready`, `/pegawai/orders/{id}/complete`).",
        "Grup Administrator: Dilindungi middleware `auth` dan `role:admin` untuk proteksi menu manajemen master data, laporan omset, dan pengaturan sistem kafe."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.10 Layer Tampilan Antarmuka (Blade Views & JavaScript Audio)", 2);

addFileExplanation(
    $section,
    "resources/views/customer/order.blade.php",
    "Antarmuka katalog menu digital di layar ponsel pelanggan.",
    [
        "Responsif Mobile-First: Didesain khusus untuk kenyamanan layar sentuh ponsel (tab kategori, foto sajian menarik, tombol kuantitas plus-minus, dan bottom floating bar untuk keranjang).",
        "Filter Kategori Instan: Menggunakan manipulasi DOM JavaScript untuk memfilter menu tanpa perlu me-reload halaman browser (Zero Page Refresh).",
        "Modal Keranjang: Menampilkan rincian pesanan, kolom input nama pemesan, catatan khusus menu, dan tombol konfirmasi kirim pesanan."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "resources/views/customer/track.blade.php",
    "Layar konfirmasi dan pelacakan live status antrean FIFO pelanggan.",
    [
        "Live Polling Otomatis: Setiap 4 detik, JavaScript mengeksekusi request `fetch()` ke endpoint `/pesanan/{order}/status` untuk memantau perubahan status tanpa me-reload halaman.",
        "Sintaks Audio Web API (Dual-Chime Alarm): Ketika server mengembalikan status 'siap_diambil', fungsi JavaScript `playBeepAlarm()` diaktifkan:",
        "Kodingan Audio: Menggunakan `AudioContext`, membuat dua nada frekuensi tinggi (880 Hz nada A5 dan 1174.66 Hz nada D6) bersuara nyaring berulang-ulang sampai pelanggan menekan tombol 'Saya Ambil'.",
        "Visual Animasi: Nomor antrean berkedip dengan animasi detak (pulse animation) berwarna hijau cerah untuk menarik perhatian visual pelanggan."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "resources/views/pegawai/dashboard.blade.php",
    "Antarmuka pos operasional barista di meja kasir / bar counter.",
    [
        "Tampilan Antrean FIFO 3-Kolom: Menampilkan antrean terbagi dalam 3 kolom status: 'Menunggu' (Kuning), 'Sedang Diproses' (Biru), dan 'Siap Diambil' (Hijau).",
        "Penguncian Tombol FIFO Interaktif: Tombol 'Mulai Proses' pada pesanan urutan kedua dan seterusnya dikunci secara visual atau jika diklik memunculkan SweetAlert2 yang mendidik kasir: 'Sesuai ketentuan FIFO, Anda wajib memproses pesanan Nomor Antrean #XXX terlebih dahulu'.",
        "Tombol 'Siap Diambil' & 'Selesai': Disediakan tombol aksi cepat untuk memanggil pelanggan dan menyelesaikan transaksi saat pesanan diserahkan."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "resources/views/admin/dashboard.blade.php & reports/index.blade.php",
    "Antarmuka ringkasan eksekutif dan pelaporan untuk pemilik kedai kopi.",
    [
        "Kartu Statistik Metrik: Total omset hari ini, total pesanan sukses, rata-rata durasi pengerjaan FIFO, dan menu terlaris.",
        "Fitur Cetak PDF / Laporan Nota: Dilengkapi tombol cetak bersih (Print Media CSS) yang secara otomatis menyembunyikan navigasi sidebar dan footer saat dicetak ke printer atau diekspor ke PDF."
    ],
    $pJustify, $fontCode
);

$section->addTitle("2.11 Layer Migrasi Basis Data (Database Migrations)", 2);

addFileExplanation(
    $section,
    "database/migrations/2026_11_05_000003_create_orders_table.php",
    "Membuat skema tabel `orders` yang menyimpan data kepala transaksi.",
    [
        "Kolom received_at: Bertipe `timestamp` dengan nilai default waktu sekarang saat insert. Merupakan kolom kunci yang di-indeks (`\$table->index('received_at')`) sebagai fondasi komputasi algoritma FIFO.",
        "Kolom queue_number: Menyimpan nomor antrean harian (001, 002, dst.).",
        "Kolom status: Menggunakan tipe enum dengan 5 status resmi ('menunggu', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan')."
    ],
    $pJustify, $fontCode
);

addFileExplanation(
    $section,
    "database/migrations/2026_11_05_200644_add_thesis_defense_mode_to_settings_table.php",
    "Menambahkan kolom mode pengujian sidang skripsi ke tabel pengaturan.",
    [
        "Kenapa dibuat: Menyediakan tombol toggle 'Thesis Defense Mode' di dasbor admin untuk keperluan simulasi live saat demonstrasi di hadapan dosen penguji, memungkinkan reset data demo secara aman tanpa merusak struktur basis data."
    ],
    $pJustify, $fontCode
);

$section->addPageBreak();

// -------------------------------------------------------------
// BAB III: PERANCANGAN DIAGRAM SISTEM (STANDAR SKRIPSI DENGAN GAMBAR VISUAL)
// -------------------------------------------------------------
$section->addTitle("BAB III : PERANCANGAN DIAGRAM SISTEM (STANDAR SKRIPSI)", 1);
$section->addText("Bab ini menyajikan seluruh diagram pemodelan berstandar baku akademik untuk naskah Skripsi, meliputi Kamus Simbol Diagram, Analisis Sistem Informasi (ASI / Flowmap), Flowchart ANSI/ISO 5807, Entity Relationship Diagram (ERD), dan Unified Modeling Language (UML). Seluruh diagram dilengkapi dengan gambar visual beresolusi tinggi dan spesifikasi tabel akademiknya.", [], $pJustify);

$section->addTitle("3.1 Standar Simbol Diagram yang Digunakan", 2);

$tableSymbols = $section->addTable('StandardTable');
$tableSymbols->addRow();
$tableSymbols->addCell(2000, $tableStyleHeader)->addText("Nama Simbol", $fontHeader, $pCenter);
$tableSymbols->addCell(2500, $tableStyleHeader)->addText("Bentuk Geometri Baku", $fontHeader, $pCenter);
$tableSymbols->addCell(4500, $tableStyleHeader)->addText("Fungsi & Penerapan dalam Sistem", $fontHeader, $pCenter);

$symbolData = [
    ["Terminator", "Oval / Rounded Rectangle", "Menandai titik awal (Start/Mulai) dan titik akhir (Stop/Selesai) dari suatu alur logika flowchart."],
    ["Process", "Persegi Panjang (Rectangle)", "Menyatakan proses kalkulasi komputasi, update status basis data, atau eksekusi nomor antrean FIFO."],
    ["Decision", "Belah Ketupat (Diamond)", "Menyatakan percabangan keputusan logika dengan dua jalur keluaran (misal: Token Valid?, Keranjang Kosong?, Urutan Pertama?)."],
    ["Data (I/O)", "Jajaran Genjang (Parallelogram)", "Menyatakan operasi masukan data (input nama, nomor meja) atau keluaran data (output nomor antrean)."],
    ["Predefined Process", "Persegi Panjang Bergaris Ganda", "Menyatakan pemanggilan sub-program atau service terpisah (misal: FifoQueueService)."],
    ["Document", "Bentuk Dokumen (Document Shape)", "Menyatakan keluaran fisik berupa nota cetak atau standee akrilik kode QR meja."],
    ["Actor (UML)", "Stick Figure Manusia", "Merepresentasikan pengguna atau entitas luar yang berinteraksi dengan sistem (Pelanggan, Kasir, Admin)."],
    ["Use Case (UML)", "Bentuk Elips / Oval Horizontal", "Merepresentasikan fungsionalitas atau layanan yang disediakan sistem untuk aktor."],
    ["Lifeline (UML)", "Kotak Bergaris Vertikal Putus-putus", "Menyatakan keberadaan suatu objek dalam urutan waktu interaksi sequence diagram."]
];

foreach ($symbolData as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableSymbols->addRow();
    $tableSymbols->addCell(2000, $rowStyle)->addText($row[0], $fontCellBold);
    $tableSymbols->addCell(2500, $rowStyle)->addText($row[1], $fontCell);
    $tableSymbols->addCell(4500, $rowStyle)->addText($row[2], $fontCell, $pJustify);
}

$section->addTextBreak(1);

// -------------------------------------------------------------
// 3.2 ANALISIS SISTEM INFORMASI (ASI / FLOWMAP)
// -------------------------------------------------------------
$section->addTitle("3.2 Analisis Sistem Informasi (ASI / Bagan Alir Dokumen Flowmap)", 2);
$section->addText("Analisis sistem membandingkan alur dokumen dan data operasional antara Sistem Berjalan (Konvensional/Manual) dengan Sistem Diusulkan (Sistem Pemesanan Digital Berbasis FIFO).", [], $pJustify);

// Sisipkan Gambar Visual ASI
$section->addText("Gambar 3.2 Bagan Alir Dokumen / ASI (Sistem Berjalan vs Sistem Diusulkan)", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_asi.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_asi.png', ['width' => 480, 'height' => 336, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);

$section->addText("Tabel 3.2 Perbandingan Mendalam Sistem Berjalan vs Sistem Diusulkan", ['bold' => true, 'size' => 10]);

$tableASI = $section->addTable('StandardTable');
$tableASI->addRow();
$tableASI->addCell(1500, $tableStyleHeader)->addText("Tahapan Alur", $fontHeader, $pCenter);
$tableASI->addCell(3700, $tableStyleHeader)->addText("Sistem Berjalan (Konvensional)", $fontHeader, $pCenter);
$tableASI->addCell(3800, $tableStyleHeader)->addText("Sistem Diusulkan (Digital Berbasis FIFO)", $fontHeader, $pCenter);

$asiData = [
    [
        "1. Pemilihan Menu",
        "Pelanggan datang ke kasir, berdiri membaca daftar menu cetak di atas meja kasir sehingga menyebabkan antrean fisik memanjang.",
        "Pelanggan langsung duduk di meja yang diinginkan, membuka kamera HP, dan memindai QR akrilik meja. Menu terbuka langsung dengan foto sajian dan status stok live."
    ],
    [
        "2. Pencatatan Pesanan",
        "Kasir mencatat pesanan pada kertas nota rangkap dua. Rawan salah tulis dan waktu kedatangan tidak tercatat secara presisi.",
        "Pelanggan memilih item sajian di layar HP, memasukkan catatan khusus (misal: 'less sugar'), lalu menekan 'Kirim Pesanan'. Sistem mencatat waktu received_at presisi milidetik."
    ],
    [
        "3. Penentuan Urutan",
        "Tumpukan kertas nota rawan berantakan atau tertukar di meja barista. Pesanan pelanggan yang datang belakangan berpotensi dibuat lebih dahulu.",
        "Algoritma FIFO mengurutkan pesanan secara otomatis berdasarkan timestamp server. Sistem menerbitkan Nomor Antrean unik (001, 002) yang transparan bagi pelanggan dan barista."
    ],
    [
        "4. Pelacakan Antrean",
        "Pelanggan tidak mengetahui urutan ke berapa pesanannya dan berapa lama waktu tunggu, menimbulkan kegelisahan dan komplain.",
        "Layar ponsel pelanggan memuat Live Tracking FIFO: menampilkan posisi antrean saat ini, sisa pesanan di depan, dan estimasi waktu selesai dalam hitungan menit."
    ],
    [
        "5. Notifikasi Siap",
        "Barista harus berteriak memanggil nama pelanggan di ruangan kafe. Jika pelanggan berada di area outdoor, panggilan sering tidak terdengar.",
        "Begitu barista mengubah status ke 'Siap Diambil', ponsel pelanggan otomatis membunyikan alarm suara berdering keras (Web Audio API) + notifikasi WhatsApp darurat."
    ],
    [
        "6. Rekapitulasi",
        "Owner harus menghitung tumpukan kertas nota manual setiap tutup toko. Rawan manipulasi nota dan tidak ada metrik waktu pelayanan.",
        "Sistem secara otomatis mengkalkulasi metrik evaluasi FIFO: total omset, rata-rata durasi pengerjaan per sajian, rekapitulasi per kategori, dan laporan cetak harian/bulanan."
    ]
];

foreach ($asiData as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableASI->addRow();
    $tableASI->addCell(1500, $rowStyle)->addText($row[0], $fontCellBold);
    $tableASI->addCell(3700, $rowStyle)->addText($row[1], $fontCell, $pJustify);
    $tableASI->addCell(3800, $rowStyle)->addText($row[2], $fontCell, $pJustify);
}

$section->addPageBreak();

// -------------------------------------------------------------
// 3.3 FLOWCHART SISTEM (ANSI / ISO STANDARD)
// -------------------------------------------------------------
$section->addTitle("3.3 Flowchart Sistem (ANSI / ISO Standard)", 2);
$section->addText("Flowchart berikut memodelkan alur logika pemrosesan data sistem menggunakan simbol geometri standar ISO 5807:", [], $pJustify);

$section->addTitle("3.3.1 Flowchart Pemesanan Pelanggan (Customer Flow)", 3);
$section->addText("Gambar 3.3.1 Flowchart Alur Pemesanan Pelanggan (Standar ANSI/ISO 5807)", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_flowchart_customer.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_flowchart_customer.png', ['width' => 450, 'height' => 525, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);

$section->addText("Penjelasan Alur Flowchart Pelanggan:", ['bold' => true]);
$section->addText("1. [START] Titik awal alur pemesanan oleh pelanggan.", [], $pJustify);
$section->addText("2. [Input/Output] Pelanggan memindai standee kode QR akrilik di meja kafe menggunakan kamera ponsel pintarnya.", [], $pJustify);
$section->addText("3. [Decision] Sistem memeriksa apakah query string membawa token QR meja yang sah. Jika TIDAK valid, dialihkan ke layar peringatan 'Akses QR Diperlukan' dan alur selesai (STOP). Jika YA valid, lanjut ke langkah 4.", [], $pJustify);
$section->addText("4. [Process] Sistem menyimpan tanda sesi sementara di browser (qr_scanned = true) dan menampilkan katalog menu digital lengkap dengan status stok terkini.", [], $pJustify);
$section->addText("5. [Process] Pelanggan memilih sajian menu, mengatur kuantitas, memasukkan catatan khusus, dan mengisi nama pemesan.", [], $pJustify);
$section->addText("6. [Input/Output] Pelanggan menekan tombol 'Kirim Pesanan Sekarang'.", [], $pJustify);
$section->addText("7. [Decision] Server memverifikasi validitas sesi dan memastikan keranjang tidak kosong. Jika kosong, muncul pesan validasi. Jika valid, lanjut ke database transaction.", [], $pJustify);
$section->addText("8. [Process] Database Transaction dieksekusi: Server mencatat waktu terima presisi (received_at), mengeksekusi algoritma FIFO untuk menerbitkan Nomor Antrean harian (001, 002, dst.), dan menyimpan rekaman ke tabel `orders` dan `order_details`.", [], $pJustify);
$section->addText("9. [Process] Sistem secara langsung MENGHANGUSKAN sesi QR meja pelanggan (session forget) untuk mencegah tautan dipakai ulang.", [], $pJustify);
$section->addText("10. [Display] Layar ponsel membuka halaman konfirmasi dan Live Tracking Antrean FIFO. [SELESAI].", [], $pJustify);

$section->addPageBreak();

$section->addTitle("3.3.2 Flowchart Operasional Kasir/Barista (FIFO Queue Dispatcher)", 3);
$section->addText("Gambar 3.3.2 Flowchart Dispatcher FIFO Kasir dan Barista (Standar ANSI/ISO 5807)", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_flowchart_barista.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_flowchart_barista.png', ['width' => 450, 'height' => 475, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);

$section->addText("Penjelasan Logika Pemrosesan Antrean FIFO Barista:", ['bold' => true]);
$section->addText("1. [START] Barista melakukan autentikasi login ke dasbor pos barista (/pegawai/dashboard).", [], $pJustify);
$section->addText("2. [Process] Dasbor memuat antrean aktif yang secara otomatis diurutkan oleh algoritma FIFO: `ORDER BY received_at ASC, id ASC`.", [], $pJustify);
$section->addText("3. [Input/Output] Barista memilih pesanan dan mengeklik tombol 'Mulai Proses'.", [], $pJustify);
$section->addText("4. [Decision] Server mengecek: 'Apakah pesanan yang diklik adalah pesanan yang paling awal masuk (FIFO terdepan)?'.", [], $pJustify);
$section->addText("   - Jika TIDAK (menyerobot): Aksi diblokir seketika, server mengembalikan status HTTP 422, dan SweetAlert peringatan muncul: 'Wajib memproses pesanan terdahulu sesuai urutan FIFO!'. Barista kembali ke daftar antrean.", [], $pJustify);
$section->addText("   - Jika YA (sah): Status pesanan diperbarui menjadi 'diproses' dan baris database dikunci menggunakan `lockForUpdate()`.", [], $pJustify);
$section->addText("5. [Process] Barista meracik minuman di bar counter. Begitu racikan selesai, barista mengeklik 'Siap Diambil' (diizinkan selesai fleksibel sesuai waktu peracikan riil).", [], $pJustify);
$section->addText("6. [Process] Server memicu sinyal notifikasi: Ponsel pelanggan langsung membunyikan alarm suara (Web Audio API) berulang kali dan mengirim notifikasi WhatsApp.", [], $pJustify);
$section->addText("7. [Input/Output] Pelanggan datang ke kasir menunjukkan nomor antreannya -> Barista mengeklik 'Pesanan Selesai' -> Status berubah menjadi 'selesai' dan durasi penyelesaian dicatat ke metrik evaluasi. [SELESAI].", [], $pJustify);

$section->addPageBreak();

// -------------------------------------------------------------
// 3.4 ENTITY RELATIONSHIP DIAGRAM (ERD) & DATA DICTIONARY
// -------------------------------------------------------------
$section->addTitle("3.4 Entity Relationship Diagram (ERD) & Kamus Data Basis Data", 2);
$section->addText("Basis data Sekarsa Coffee dirancang menggunakan relasi integritas referensial (Foreign Key Constraints) dengan notasi Crow's Foot. Relasi One-to-Many (1:N) diterapkan untuk menjamin konsistensi dan auditabilitas data transaksi pemesanan.", [], $pJustify);

$section->addText("Gambar 3.4 Entity Relationship Diagram (ERD) Basis Data Sekarsa Coffee", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_erd.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_erd.png', ['width' => 490, 'height' => 367, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);

$section->addTitle("3.4.1 Kamus Data Entitas Utama (Data Dictionary)", 3);

// Helper Tabel Kamus Data
function addDataDictionaryTable($section, $tableName, $desc, $columns, $tableStyleHeader, $tableStyleRowEven, $tableStyleRowOdd, $fontHeader, $fontCell, $fontCellBold, $pCenter, $pJustify) {
    $section->addText("Tabel Basis Data: " . $tableName . " (" . $desc . ")", ['bold' => true, 'size' => 10, 'color' => '166534']);
    $table = $section->addTable('StandardTable');
    $table->addRow();
    $table->addCell(1800, $tableStyleHeader)->addText("Nama Kolom", $fontHeader, $pCenter);
    $table->addCell(1600, $tableStyleHeader)->addText("Tipe Data", $fontHeader, $pCenter);
    $table->addCell(1200, $tableStyleHeader)->addText("Atribut", $fontHeader, $pCenter);
    $table->addCell(4400, $tableStyleHeader)->addText("Keterangan & Fungsi Akademik", $fontHeader, $pCenter);

    foreach ($columns as $i => $c) {
        $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
        $table->addRow();
        $table->addCell(1800, $rowStyle)->addText($c[0], $fontCellBold);
        $table->addCell(1600, $rowStyle)->addText($c[1], $fontCell);
        $table->addCell(1200, $rowStyle)->addText($c[2], $fontCell, $pCenter);
        $table->addCell(4400, $rowStyle)->addText($c[3], $fontCell, $pJustify);
    }
    $section->addTextBreak(1);
}

addDataDictionaryTable(
    $section, "orders", "Pusat Transaksi Pesanan & Antrean FIFO",
    [
        ["id", "BIGINT(20)", "PK, Auto", "Identifikator unik transaksi sekaligus tie-breaker urutan antrean FIFO jika timestamp identik."],
        ["order_number", "VARCHAR(50)", "Unique", "Nomor referensi kode unik pesanan pelanggan (contoh: ORD-20261106-001)."],
        ["queue_number", "VARCHAR(20)", "Indexed", "Nomor urut antrean harian pelanggan (001, 002, dst.) yang diterbitkan oleh FIFO service."],
        ["table_number", "VARCHAR(20)", "Not Null", "Nomor meja tempat standee kode QR dipindai oleh pelanggan."],
        ["customer_name", "VARCHAR(255)", "Not Null", "Nama pemesan yang diinput pelanggan saat konfirmasi keranjang."],
        ["status", "ENUM", "Indexed", "Status alur: 'menunggu', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'."],
        ["total_amount", "DECIMAL(12,2)", "Not Null", "Total nominal transaksi belanja pesanan pelanggan."],
        ["received_at", "TIMESTAMP", "Indexed", "Waktu presisi pesanan diterima server; kunci utama pengurutan FIFO (ORDER BY received_at ASC)."],
        ["handled_by", "VARCHAR(255)", "Nullable", "Nama atau username barista/kasir yang memproses pesanan."],
        ["completed_at", "TIMESTAMP", "Nullable", "Waktu pesanan diserahkan; digunakan untuk menghitung metrik durasi pengerjaan FIFO."]
    ],
    $tableStyleHeader, $tableStyleRowEven, $tableStyleRowOdd, $fontHeader, $fontCell, $fontCellBold, $pCenter, $pJustify
);

addDataDictionaryTable(
    $section, "order_details", "Rincian Item Menu dalam Pesanan",
    [
        ["id", "BIGINT(20)", "PK, Auto", "Identifikator unik baris item pesanan."],
        ["order_id", "BIGINT(20)", "FK, Cascade", "Foreign Key yang menghubungkan ke tabel orders(id)."],
        ["menu_id", "BIGINT(20)", "FK, Set Null", "Foreign Key yang menghubungkan ke tabel menus(id)."],
        ["menu_name", "VARCHAR(255)", "Not Null", "Snapshot nama menu saat pesanan dibuat (kebal dari perubahan nama menu di kemudian hari)."],
        ["price", "DECIMAL(12,2)", "Not Null", "Snapshot harga satuan menu saat dipesan (kebal dari fluktuasi harga)."],
        ["quantity", "INT(11)", "Not Null", "Jumlah porsi yang dipesan oleh pelanggan."],
        ["subtotal", "DECIMAL(12,2)", "Not Null", "Hasil perkalian (price * quantity)."],
        ["notes", "TEXT", "Nullable", "Catatan khusus pesanan pelanggan (contoh: 'extra ice', 'less sugar')."]
    ],
    $tableStyleHeader, $tableStyleRowEven, $tableStyleRowOdd, $fontHeader, $fontCell, $fontCellBold, $pCenter, $pJustify
);

addDataDictionaryTable(
    $section, "menus", "Master Data Sajian Makanan & Minuman",
    [
        ["id", "BIGINT(20)", "PK, Auto", "Identifikator unik sajian menu."],
        ["category_id", "BIGINT(20)", "FK, Cascade", "Foreign Key yang menghubungkan ke tabel categories(id)."],
        ["name", "VARCHAR(255)", "Not Null", "Nama sajian kopi/makanan."],
        ["price", "DECIMAL(12,2)", "Not Null", "Harga jual satuan menu."],
        ["image", "VARCHAR(255)", "Nullable", "Nama berkas gambar menu yang tersimpan di public/images/menus/."],
        ["is_available", "BOOLEAN", "Default 1", "Flag ketersediaan stok sajian (1 = Tersedia, 0 = Habis/Kosong)."],
        ["description", "TEXT", "Nullable", "Deskripsi komposisi sajian menu."]
    ],
    $tableStyleHeader, $tableStyleRowEven, $tableStyleRowOdd, $fontHeader, $fontCell, $fontCellBold, $pCenter, $pJustify
);

$section->addPageBreak();

// -------------------------------------------------------------
// 3.5 UNIFIED MODELING LANGUAGE (UML) LENGKAP
// -------------------------------------------------------------
$section->addTitle("3.5 Unified Modeling Language (UML) Lengkap", 2);
$section->addText("Pemodelan sistem berorientasi objek (Object-Oriented Modeling) disajikan menggunakan 4 diagram utama UML versi 2.5: Use Case Diagram, Activity Diagram, Sequence Diagram, dan Class Diagram.", [], $pJustify);

$section->addTitle("3.5.1 Use Case Diagram & Skenario Use Case", 3);
$section->addText("Gambar 3.5.1 Use Case Diagram Sistem Pemesanan Digital Sekarsa Coffee", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_usecase.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_usecase.png', ['width' => 480, 'height' => 404, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);

$section->addText("Tabel 3.5.1 Skenario Use Case: Memproses Pesanan Sesuai Prioritas FIFO (UC-07)", ['bold' => true, 'size' => 10]);

$tableUC = $section->addTable('StandardTable');
$ucScenario = [
    ["Nomor & Nama Use Case", "UC-07 : Memproses Pesanan Sesuai Prioritas FIFO"],
    ["Aktor Utama", "Kasir / Barista"],
    ["Deskripsi", "Barista memilih pesanan terdepan untuk diubah statusnya menjadi 'diproses' guna meracik sajian sesuai antrean waktu kedatangan."],
    ["Kondisi Awal (Pre-condition)", "Barista telah berhasil login dan membuka halaman Pos Operasional Barista; terdapat antrean pesanan berstatus 'menunggu'."],
    ["Kondisi Akhir (Post-condition)", "Status pesanan berubah menjadi 'diproses', kolom handled_by terisi nama barista, dan layar HP pelanggan menampilkan status 'Sedang Diproses'."],
    ["Skenario Utama (Main Flow)", "1. Barista mengamati kartu antrean FIFO pada kolom 'Menunggu'.\n2. Barista mengeklik tombol 'Mulai Proses' pada pesanan paling awal.\n3. Server memverifikasi bahwa tidak ada pesanan lain yang masuk lebih lama.\n4. Server mengunci baris pesanan (lockForUpdate) dan memperbarui status menjadi 'diproses'.\n5. Sistem memperbarui tampilan dasbor barista dan memicu update di HP pelanggan."],
    ["Skenario Alternatif (Exception)", "3a. Barista mengeklik pesanan urutan kedua atau ketiga saat pesanan pertama masih menunggu:\n   1. Server menolak eksekusi dan mengembalikan status HTTP 422.\n   2. Sistem menampilkan SweetAlert peringatan: 'Wajib memproses pesanan terdahulu sesuai urutan FIFO!'.\n   3. Data status di basis data tidak berubah."]
];

foreach ($ucScenario as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableUC->addRow();
    $tableUC->addCell(2500, $tableStyleHeader)->addText($row[0], $fontHeader, $pCenter);
    $tableUC->addCell(6500, $rowStyle)->addText($row[1], $fontCell, $pJustify);
}

$section->addPageBreak();

$section->addTitle("3.5.2 Activity Diagram (Swimlane 3 Partisi)", 3);
$section->addText("Gambar 3.5.2 Activity Diagram Alur Pemesanan & Pemrosesan FIFO (Swimlane)", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_activity.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_activity.png', ['width' => 480, 'height' => 430, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);
$section->addText("Activity Diagram di atas menggambarkan koordinasi kerja antara 3 partisi entitas:", ['bold' => true]);
$section->addText("1. Partisi Pelanggan: Melakukan pemindaian QR akrilik di meja, memilih sajian menu, mengirim pesanan, dan menerima alarm audio ketika pesanan telah siap.", [], $pJustify);
$section->addText("2. Partisi Sistem / Server: Memvalidasi query token QR meja, mengeksekusi algoritma antrean FIFO berbasis timestamp, mencatat pesanan atomik ke basis data, menghanguskan sesi QR, serta mengirim sinyal Web Audio API dan WhatsApp.", [], $pJustify);
$section->addText("3. Partisi Kasir / Barista: Memonitor daftar antrean, mengeksekusi tombol proses sesuai urutan kedatangan FIFO, meracik menu di bar counter, dan mengubah status menjadi 'Siap Diambil' untuk memanggil pelanggan.", [], $pJustify);

$section->addPageBreak();

$section->addTitle("3.5.3 Sequence Diagram (Penerbitan Nomor Antrean & Pemicu Alarm Siap)", 3);
$section->addText("Gambar 3.5.3 Sequence Diagram Transaksi Pemesanan & Pemicu Alarm Suara FIFO", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_sequence.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_sequence.png', ['width' => 480, 'height' => 379, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);
$section->addText("Sequence diagram memodelkan pesan berurutan waktu (time-ordered messages) antar objek:", ['bold' => true]);
$section->addText("• Fase Pemesanan: Pelanggan mengirim request HTTP POST membawa payload keranjang ke `CustomerOrderController`. Controller mendelegasikan proses ke `FifoQueueService`. Service membuka transaksi atomik database MySQL, mencatat timestamp `received_at`, dan menghasilkan nomor antrean harian unik.", [], $pJustify);
$section->addText("• Fase Penghapusan Sesi: Segera setelah database meng-commit transaksi, controller memanggil `session()->forget()` untuk menghanguskan hak akses QR pelanggan.", [], $pJustify);
$section->addText("• Fase Panggilan Alarm: Barista memanggil endpoint `readyOrder()`. Server meng-update status ke 'siap_diambil'. Polling live di ponsel pelanggan mendeteksi perubahan status dan langsung membunyikan Web Audio API synthesizer (dual-frequency chime) secara lokal.", [], $pJustify);

$section->addPageBreak();

$section->addTitle("3.5.4 Class Diagram (Arsitektur Model, Controller & Service Layer)", 3);
$section->addText("Gambar 3.5.4 Class Diagram Arsitektur MVC & Service Layer Sekarsa Coffee", ['bold' => true, 'size' => 10], $pCenter);
if (file_exists(__DIR__ . '/diagrams/diagram_class.png')) {
    $section->addImage(__DIR__ . '/diagrams/diagram_class.png', ['width' => 490, 'height' => 343, 'alignment' => Jc::CENTER]);
}
$section->addTextBreak(1);
$section->addText("Class Diagram di atas menyajikan pemisahan tanggung jawab (Separation of Concerns / SoC):", ['bold' => true]);
$section->addText("• Service Layer (`FifoQueueService`): Berdiri independen untuk menampung seluruh kalkulasi antrean FIFO tanpa tercampur dengan kode presentasi antarmuka.", [], $pJustify);
$section->addText("• Controller Layer (`CustomerOrderController`, `PegawaiDashboardController`): Bertindak sebagai pengatur lalu lintas HTTP dan otentikasi peran.", [], $pJustify);
$section->addText("• Eloquent Model Layer (`Order`, `OrderDetail`, `Menu`): Menyediakan enkapsulasi data dan pemetaan relasi antar tabel.", [], $pJustify);

$section->addPageBreak();

// -------------------------------------------------------------
// BAB IV: PANDUAN PENGUJIAN & ATURAN BISNIS FIFO
// -------------------------------------------------------------
$section->addTitle("BAB IV : PANDUAN PENGUJIAN BLACK-BOX & ATURAN BISNIS", 1);

$section->addTitle("4.1 Pembuktian Aturan Ketat Antrean Masuk FIFO (Strict Queue Ingress)", 2);
$section->addText("Salah satu temuan kunci dalam penelitian ini adalah bahwa algoritma First In First Out (FIFO) pada sistem pemesanan makanan/minuman harus ditegakkan secara KETAT pada fase pemrosesan awal (saat barista mengambil nota dari antrean), tetapi fleksibel pada fase penyelesaian.", [], $pJustify);
$section->addText("Di dalam kodingan `PegawaiDashboardController.php`, method `processOrder()` mengeksekusi pemeriksaan:", [], $pJustify);
$section->addText("```php
\$hasEarlierWaitingOrder = Order::where('status', 'menunggu')
    ->where(function (\$q) use (\$order) {
        \$q->where('received_at', '<', \$order->received_at)
          ->orWhere(function (\$q2) use (\$order) {
              \$q2->where('received_at', '=', \$order->received_at)
                  ->where('id', '<', \$order->id);
          });
    })->exists();

if (\$hasEarlierWaitingOrder) {
    return response()->json([
        'success' => false,
        'message' => 'Sesuai ketentuan FIFO, Anda wajib memproses pesanan nomor antrean terdahulu terlebih dahulu.'
    ], 422);
}
```", ['name' => 'Consolas', 'size' => 8.5, 'color' => '0F172A']);
$section->addText("Dengan proteksi ini, kasir tidak dapat melompati pesanan pelanggan pertama untuk memproses pesanan pelanggan kedua, mencegah diskriminasi pelayanan dan penyerobotan antrean.", [], $pJustify);

$section->addTitle("4.2 Fleksibilitas Penyelesaian Pesanan Selesai (Service Time Independence)", 2);
$section->addText("Dalam teori antrean kedai kopi, waktu pelayanan (Service Time) masing-masing sajian bersifat stokastik dan tidak identik:", [], $pJustify);
$section->addText("• Pesanan A (Masuk jam 10:00:00): Memesan 3 porsi makanan berat (durasi masak 15 menit, estimasi siap 10:15).", [], $pJustify);
$section->addText("• Pesanan B (Masuk jam 10:02:00): Memesan 1 cangkir Espresso (durasi racik 2 menit, siap 10:04).", [], $pJustify);
$section->addText("Jika sistem memaksakan penyelesaian harus urut FIFO, maka kopi Pesanan B yang sudah siap pada 10:04 akan dibiarkan dingin dan rusak citarasanya hanya untuk menunggu makanan Pesanan A selesai pada 10:15. Oleh karena itu, pada method `readyOrder()` dan `completeOrder()`, barista diizinkan menyelesaikan pesanan yang berstatus 'diproses' secara bebas sesuai waktu siap riil.", [], $pJustify);

$section->addTitle("4.3 Mekanisme Keamanan Single-Use QR Gate & Penghapusan Sesi", 2);
$section->addText("Untuk mengatasi pelanggan yang mencoba memesan makanan dari luar kedai kopi:", [], $pJustify);
$section->addText("1. Standee akrilik di meja kafe memuat URL dengan query token rahasia kafe (`/pesan?token=...&meja=XX`).", [], $pJustify);
$section->addText("2. Jika URL diketik manual di browser (`/pesan`), sistem mendeteksi ketiadaan token dan langsung me-redirect pengunjung ke `/akses-qr-diperlukan`.", [], $pJustify);
$section->addText("3. Begitu pesanan sukses dikirim, tiket sesi `qr_scanned` langsung dihapus dari sesi server dan cookie browser. Jika pelanggan ingin memesan menu tambahan, ia WAJIB memindai ulang QR akrilik di mejanya.", [], $pJustify);

$section->addTitle("4.4 Alasan Akademik Penghapusan Modul GPS Geofencing", 2);
$section->addText("Pada tahap awal perancangan, sistem sempat dilengkapi modul deteksi koordinat GPS Geofencing (HTML5 Geolocation API). Namun dalam pengujian empiris langsung di kafe, modul GPS dihapuskan karena tiga kelemahan fatal:", [], $pJustify);
$section->addText("• Deviasi Akurasi Indoor: Atap seng dan tembok beton kedai kopi menyebabkan pelemahan sinyal satelit GPS (attenuation), menghasilkan radius kesalahan (error margin) 30 hingga 70 meter sehingga pelanggan sah yang duduk di dalam kafe justru sering ditolak pemesanannya.", [], $pJustify);
$section->addText("• Hambatan Izin Peramban (Permission Block): Sekitar 40% pelanggan menolak pop-up 'Allow Location Access' di ponsel pintarnya karena alasan privasi, menyebabkan sistem macet total.", [], $pJustify);
$section->addText("• Keberadaan Standee QR Meja Sudah Mewakili Validasi Lokasi Fisik: Seseorang hanya bisa memindai standee QR jika secara fisik berada di depan meja kedai kopi. Oleh karena itu, Strict QR Token Gate terbukti jauh lebih reliabel dan ramah pengguna.", [], $pJustify);

$section->addTitle("4.5 Tabel Hasil Uji Coba Black-Box Sistem", 2);

$tableBB = $section->addTable('StandardTable');
$tableBB->addRow();
$tableBB->addCell(1500, $tableStyleHeader)->addText("ID Skenario", $fontHeader, $pCenter);
$tableBB->addCell(3000, $tableStyleHeader)->addText("Aksi / Kasus Uji", $fontHeader, $pCenter);
$tableBB->addCell(3000, $tableStyleHeader)->addText("Hasil yang Diharapkan", $fontHeader, $pCenter);
$tableBB->addCell(1500, $tableStyleHeader)->addText("Status Uji", $fontHeader, $pCenter);

$bbTests = [
    ["BB-01", "Pelanggan scan QR meja sah", "Halaman katalog menu terbuka; nomor meja terisi otomatis", "VALID [PASSED]"],
    ["BB-02", "Pelanggan ketik URL /pesan manual", "Akses ditolak; dialihkan ke /akses-qr-diperlukan", "VALID [PASSED]"],
    ["BB-03", "Kirim pesanan keranjang kosong", "Muncul pesan validasi; pesanan tidak disimpan", "VALID [PASSED]"],
    ["BB-04", "Submit pesanan sah", "Nomor antrean FIFO terbit berurutan; sesi QR hangus", "VALID [PASSED]"],
    ["BB-05", "Pelanggan coba pesan ulang tanpa scan", "Ditolak; sistem meminta scan QR meja kembali", "VALID [PASSED]"],
    ["BB-06", "Kasir proses pesanan urutan #1", "Status berubah jadi 'diproses'; baris DB dikunci", "VALID [PASSED]"],
    ["BB-07", "Kasir coba proses pesanan #2 duluan", "Ditolak HTTP 422; SweetAlert peringatan FIFO muncul", "VALID [PASSED]"],
    ["BB-08", "Pesanan #2 siap lebih dulu dari #1", "Status #2 berhasil jadi 'siap_diambil' tanpa hambatan", "VALID [PASSED]"],
    ["BB-09", "Barista klik 'Siap Diambil'", "HP pelanggan otomatis memutar nada alarm nyaring", "VALID [PASSED]"],
    ["BB-10", "Pelanggan tekan 'Saya Ambil'", "Alarm berhenti berdering; dialihkan ke ringkasan", "VALID [PASSED]"],
    ["BB-11", "Barista serahkan pesanan & klik Selesai", "Status berubah 'selesai'; metrik pengerjaan tercatat", "VALID [PASSED]"],
    ["BB-12", "Kasir non-admin buka /admin/settings", "Akses diblokir oleh RoleMiddleware; dialihkan", "VALID [PASSED]"]
];

foreach ($bbTests as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableBB->addRow();
    $tableBB->addCell(1500, $rowStyle)->addText($row[0], $fontCellBold);
    $tableBB->addCell(3000, $rowStyle)->addText($row[1], $fontCell);
    $tableBB->addCell(3000, $rowStyle)->addText($row[2], $fontCell);
    $tableBB->addCell(1500, $rowStyle)->addText($row[3], ['bold' => true, 'color' => '166534', 'size' => 9], $pCenter);
}

$section->addPageBreak();

// -------------------------------------------------------------
// BAB V: KESIMPULAN & PETUNJUK SIDANG SKRIPSI
// -------------------------------------------------------------
$section->addTitle("BAB V : KESIMPULAN & PETUNJUK SIDANG SKRIPSI", 1);

$section->addTitle("5.1 Kesimpulan Teknis Penelitian", 2);
$section->addText("Berdasarkan perancangan, implementasi, dan pengujian yang telah dilakukan pada sistem pemesanan Sekarsa Coffee, dapat disimpulkan bahwa:", [], $pJustify);
$section->addText("1. Penerapan algoritma First In First Out (FIFO) berbasis timestamp server (received_at) terbukti berhasil mengeliminasi masalah out-of-order execution dan penyerobotan antrean di meja kasir secara terukur.", [], $pJustify);
$section->addText("2. Pemisahan logika antara aturan ketat pemrosesan masuk (Strict Ingress FIFO) dengan fleksibilitas penyelesaian pesanan siap saji (Service Time Independence) memberikan solusi operasional yang realistis bagi operasional kedai kopi modern.", [], $pJustify);
$section->addText("3. Mekanisme Strict QR Token Gate berhasil mengamankan akses pemesanan tanpa membutuhkan modul GPS yang sering mengalami deviasi di area indoor kafe.", [], $pJustify);
$section->addText("4. Pemanfaatan Web Audio API synthesizer menghasilkan notifikasi alarm suara yang andal di ponsel pelanggan tanpa biaya langganan layanan cloud pihak ketiga.", [], $pJustify);

$section->addTitle("5.2 Petunjuk & Jawaban Menghadapi Pertanyaan Dosen Penguji Sidang", 2);

$qnas = [
    [
        "Q1: Mengapa menggunakan algoritma FIFO, bukan Priority Queue atau Round Robin?",
        "Jawaban Akademik: Sifat alami transaksi kedai kopi berlandaskan prinsip keadilan waktu kedatangan (First Come First Served). Menggunakan Priority Queue akan mendiskriminasi pelanggan biasa jika ada pelanggan VIP, sementara Round Robin lebih cocok untuk penjadwalan CPU komputer yang bersifat time-sharing, bukan pesanan fisik makanan."
    ],
    [
        "Q2: Bagaimana sistem mencegah terjadinya antrean ganda (Race Condition) jika dua kasir memproses pesanan di saat yang bersamaan?",
        "Jawaban Akademik: Sistem menerapkan Pessimistic Row Locking melalui method `lockForUpdate()` di dalam transaksi basis data InnoDB (`DB::transaction`). Ketika satu kasir mengeklik pesanan, baris data tersebut dikunci di tingkat storage engine MySQL hingga transaksi tuntas, sehingga kasir kedua tidak bisa menduplikasi pemrosesan."
    ],
    [
        "Q3: Kenapa tidak menggunakan WebSocket atau Firebase untuk update live tracking antrean?",
        "Jawaban Akademik: Lingkungan shared-hosting cPanel/InfinityFree umumnya menutup port kustom WebSocket dan tidak mengizinkan process manager daemon seperti NodeJS atau supervisord. Short-polling berbasis fetch HTTP setiap 4 detik terbukti sangat hemat resource, kompatibel di semua server hosting, dan tetap memberikan responsivitas waktu nyata bagi pelanggan."
    ],
    [
        "Q4: Kenapa modul pelacakan lokasi GPS akhirnya dihapuskan?",
        "Jawaban Akademik: Karena deviasi sinyal GPS di area indoor mencapai 30-70 meter akibat pelemahan material atap dan dinding beton kafe, serta sekitar 40% pelanggan memblokir izin akses lokasi di browser mereka. Penggunaan QR Code Meja Akrilik secara fisik sudah membuktikan keberadaan pelanggan di lokasi dengan akurasi 100%."
    ],
    [
        "Q5: Kenapa foto menu disimpan di public/images/menus/ daripada storage/app/public/?",
        "Jawaban Akademik: Server hosting murah/gratis mematikan fungsi pembuatan symlink `php artisan storage:link`. Menyimpan file langsung ke direktori publik menjamin foto menu selalu dapat diakses oleh browser tanpa kegagalan symlink broken-path."
    ]
];

foreach ($qnas as $i => $qna) {
    $section->addText(($i + 1) . ". " . $qna[0], ['bold' => true, 'color' => '166534', 'size' => 10.5]);
    $section->addText($qna[1], ['italic' => true, 'size' => 10], $pJustify);
    $section->addTextBreak(1);
}

// Simpan Dokumen
$targetDocx = __DIR__ . '/DOKUMENTASI_KODE_DAN_PERANCANGAN_SISTEM_SKRIPSI_SEKARSA_COFFEE.docx';
$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($targetDocx);

echo "Berhasil! Dokumen Word Skripsi Master telah disimpan di:\n$targetDocx\n";
echo "Ukuran Dokumen: " . filesize($targetDocx) . " bytes\n";
