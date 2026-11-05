<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;
use PhpOffice\PhpWord\Style\Table as TableStyle;
use PhpOffice\PhpWord\IOFactory;

Settings::setOutputEscapingEnabled(true);

echo "Memulai pembuatan Dokumen Word Lengkap Skripsi...\n";

$phpWord = new PhpWord();

// -------------------------------------------------------------
// KONFIGURASI DEFAULT DOKUMEN & FORMAT STANDAR SKRIPSI (A4, TNR 12)
// -------------------------------------------------------------
$phpWord->setDefaultFontName('Times New Roman');
$phpWord->setDefaultFontSize(11);

// Margin Standar Karya Tulis Ilmiah: Kiri 4cm, Atas 3cm, Kanan 3cm, Bawah 3cm (dalam twip: 1cm ≈ 567 twip)
$sectionStyle = [
    'paperSize' => 'A4',
    'marginLeft'   => 2268, // 4 cm
    'marginRight'  => 1701, // 3 cm
    'marginTop'    => 1701, // 3 cm
    'marginBottom' => 1701, // 3 cm
    'headerHeight' => 720,
    'footerHeight' => 720,
];

// Definisi Gaya Judul & Teks
$phpWord->addTitleStyle(1, ['name' => 'Times New Roman', 'size' => 16, 'bold' => true, 'color' => '0f172a'], ['spaceAfter' => 240, 'spaceBefore' => 240]);
$phpWord->addTitleStyle(2, ['name' => 'Times New Roman', 'size' => 13, 'bold' => true, 'color' => '166534'], ['spaceAfter' => 160, 'spaceBefore' => 200]);
$phpWord->addTitleStyle(3, ['name' => 'Times New Roman', 'size' => 11.5, 'bold' => true, 'color' => '1e293b'], ['spaceAfter' => 120, 'spaceBefore' => 140]);

$pJustify = ['alignment' => Jc::BOTH, 'spaceAfter' => 120, 'lineHeight' => 1.25];
$pCenter  = ['alignment' => Jc::CENTER, 'spaceAfter' => 120];
$pLeft    = ['alignment' => Jc::START, 'spaceAfter' => 100];

// Gaya Tabel Elegan
$tableStyleHeader = ['bgColor' => '166534', 'valign' => 'center'];
$tableStyleSubHeader = ['bgColor' => '047857', 'valign' => 'center'];
$tableStyleRowEven = ['bgColor' => 'F8FAFC'];
$tableStyleRowOdd = ['bgColor' => 'FFFFFF'];

$fontHeader = ['name' => 'Times New Roman', 'size' => 10, 'bold' => true, 'color' => 'FFFFFF'];
$fontSubHeader = ['name' => 'Times New Roman', 'size' => 9.5, 'bold' => true, 'color' => 'FFFFFF'];
$fontCell = ['name' => 'Times New Roman', 'size' => 9.5, 'color' => '1E293B'];
$fontCellBold = ['name' => 'Times New Roman', 'size' => 9.5, 'bold' => true, 'color' => '0F172A'];
$fontCode = ['name' => 'Consolas', 'size' => 9, 'color' => '0F172A'];

$tableBorders = [
    'borderColor' => 'CBD5E1',
    'borderSize'  => 4,
    'cellMargin'  => 80,
    'alignment'   => JcTable::CENTER
];
$phpWord->addTableStyle('StandardTable', $tableBorders);

// Section Utama
$section = $phpWord->addSection($sectionStyle);

// -------------------------------------------------------------
// HALAMAN SAMPUL / COVER RESMI SKRIPSI
// -------------------------------------------------------------
$section->addTextBreak(2);
$section->addText("DOKUMENTASI LENGKAP ARSITEKTUR KODE, ALGORITMA FIFO & PERANCANGAN SISTEM (FLOWCHART, ERD, ASI, UML)", ['name' => 'Times New Roman', 'size' => 15, 'bold' => true, 'color' => '0f172a'], $pCenter);
$section->addText("SKRIPSI / TUGAS AKHIR SISTEM INFORMASI & TEKNOLOGI INFORMASI", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true, 'color' => '166534'], $pCenter);

$section->addTextBreak(1);
$section->addText("Judul Penelitian:", ['name' => 'Times New Roman', 'size' => 11, 'italic' => true], $pCenter);
$section->addText("“Perancangan Sistem Pemesanan Digital dan Notifikasi Pengambilan Pesanan Berbasis Web dengan Algoritma First In First Out (FIFO) pada Sekarsa Coffee”", ['name' => 'Times New Roman', 'size' => 13, 'bold' => true, 'italic' => true, 'color' => '047857'], $pCenter);

$section->addTextBreak(2);

// Kotak Metadata Peneliti
$metaTable = $section->addTable(['borderColor' => '94A3B8', 'borderSize' => 6, 'alignment' => JcTable::CENTER]);
$metaTable->addRow();
$metaCell = $metaTable->addCell(8500, ['bgColor' => 'F1F5F9', 'valign' => 'center']);
$metaCell->addText("INFORMASI DEPLOYMENT & TEKNOLOGI SISTEM", ['size' => 10, 'bold' => true, 'color' => '0F172A'], $pCenter);
$metaCell->addText("• Domain Live Hosting: http://sekarsacoffee.gt.tc", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Framework & Bahasa: Laravel 11.x | PHP 8.2 | MySQL / MariaDB", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Frontend Engine: Tailwind CSS | Blade Components | Vanilla JS (Web Audio API)", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Repository GitHub: https://github.com/apriliandwianggara/skripsi.git", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Remote Sync: VS Code SFTP Extension (Auto-upload on Save)", ['size' => 9.5, 'color' => '1E293B']);
$metaCell->addText("• Tahun Penyusunan: 2026", ['size' => 9.5, 'color' => '1E293B']);

$section->addPageBreak();

// -------------------------------------------------------------
// DAFTAR ISI RINGKAS
// -------------------------------------------------------------
$section->addTitle("DAFTAR ISI DOKUMEN", 1);
$section->addText("Dokumen ini disusun sebagai panduan teknis akademik komprehensif untuk pengujian, presentasi, dan sidang skripsi, memuat penjelasan menyeluruh mengenai rancang bangun kode, alasan teknis pemilihan sintaks, serta diagram pemodelan berstandar internasional.", ['size' => 10.5, 'italic' => true], $pJustify);

$tocItems = [
    "BAB I : IKHTISAR SISTEM, LATAR BELAKANG & PEMILIHAN TEKNOLOGI",
    "BAB II : STRUKTUR DIREKTORI & PENJELASAN KODE PROGRAM 1 PER 1",
    "   2.1 Struktur Folder Proyek Laravel 11",
    "   2.2 Bedah Detail Layer Service (FifoQueueService, QrCodeService, NotificationService)",
    "   2.3 Bedah Detail Layer Controller (Customer, Pegawai/Kasir, Admin Setting)",
    "   2.4 Bedah Detail Layer Model & Hubungan Entitas Eloquent",
    "   2.5 Bedah Detail Routing, Middleware & Bootstrap Application",
    "   2.6 Bedah Detail Frontend View & Interaksi Interaktif Pelanggan/Kasir",
    "BAB III : PERANCANGAN DIAGRAM SISTEM STANDAR SKRIPSI",
    "   3.1 Kamus Standar Simbol Diagram (ISO 5807, ANSI, UML 2.5)",
    "   3.2 Bagan Alir Dokumen (ASI / Flowmap Sistem Berjalan vs Diusulkan)",
    "   3.3 Flowchart Sistem (Alur Pelanggan, Barista FIFO, & Validasi QR Meja)",
    "   3.4 Entity Relationship Diagram (ERD) & Kamus Data Relasional",
    "   3.5 Unified Modeling Language (UML) Lengkap:",
    "       - Use Case Diagram & Tabel Spesifikasi Skenario",
    "       - Activity Diagram (Pelanggan, Kasir FIFO, Owner)",
    "       - Sequence Diagram (Penerbitan Nomor Antrean & Notifikasi Suara Siap)",
    "       - Class Diagram (Arsitektur Model, Controller & Service)",
    "BAB IV : PANDUAN PENGUJIAN BLACK-BOX & ATURAN LOGIKA BISNIS FIFO",
    "   4.1 Pembuktian Aturan Ketat Antrean Masuk FIFO (Pencegahan Pemrosesan Loncat)",
    "   4.2 Fleksibilitas Penyelesaian Pesanan Selesai (Service Time Independence)",
    "   4.3 Mekanisme Keamanan Single-Use QR Gate & Penghapusan Cookie Persisten",
    "   4.4 Alasan Akademik Penghapusan Modul GPS Geofencing",
    "BAB V : KESIMPULAN & PETUNJUK KELENGKAPAN SIDANG SKRIPSI",
];

foreach ($tocItems as $item) {
    $isBab = str_starts_with($item, 'BAB');
    $section->addText($item, ['bold' => $isBab, 'color' => $isBab ? '166534' : '334155', 'size' => $isBab ? 10.5 : 10]);
}

$section->addPageBreak();

// -------------------------------------------------------------
// BAB I: IKHTISAR SISTEM & PEMILIHAN TEKNOLOGI
// -------------------------------------------------------------
$section->addTitle("BAB I : IKHTISAR SISTEM & PEMILIHAN TEKNOLOGI", 1);

$section->addTitle("1.1 Latar Belakang & Identifikasi Masalah", 2);
$section->addText("Sekarsa Coffee merupakan salah satu unit usaha kedai kopi modern yang memiliki volume transaksi pelanggan yang fluktuatif pada jam-jam sibuk (*peak hours*). Berdasarkan observasi awal, sistem operasional konvensional menghadapi tiga kendala utama:", [], $pJustify);

$section->addListItem("Antrean Manual di Kasir yang Menumpuk: Pelanggan harus berdiri mengantre di depan meja kasir hanya untuk melihat menu dan memesan, yang menyebabkan kerumunan di area pintu masuk kafe.", 0, null, null, $pJustify);
$section->addListItem("Penyerobotan Antrean & Human Error Peracikan: Pencatatan nota manual rawan mengalami pergeseran urutan (out-of-order execution), di mana pesanan pelanggan yang datang belakangan justru disajikan lebih dulu sehingga memicu ketidakpuasan pelanggan.", 0, null, null, $pJustify);
$section->addListItem("Pelanggan Tidak Mengetahui Status Pesanan: Pelanggan yang duduk di meja tidak memiliki kejelasan berapa lama mereka harus menunggu dan kapan minuman mereka siap diambil, menyebabkan barista berulang kali harus memanggil nama pelanggan dengan suara keras.", 0, null, null, $pJustify);

$section->addTitle("1.2 Solusi yang Diterapkan dalam Penelitian", 2);
$section->addText("Penelitian ini merancang sistem pemesanan digital berbasis website responsif dengan mengimplementasikan algoritma First In First Out (FIFO). Sistem ini menghubungkan pelanggan di meja dengan barista di bar counter melalui:", [], $pJustify);

$section->addListItem("Kode QR Standee Permanen di Meja: Pelanggan memindai QR akrilik di meja menggunakan ponsel pintarnya untuk membuka menu interaktif tanpa perlu instalasi aplikasi aplikasi native (Play Store/App Store).", 0, null, null, $pJustify);
$section->addListItem("Algoritma FIFO Berbasis Waktu Server: Setiap pesanan yang dikirim oleh pelanggan dicatat waktu terimanya secara presisi (timestamp received_at) oleh server. Nomor antrean harian (001, 002, dst.) diterbitkan secara berurutan dan transparan.", 0, null, null, $pJustify);
$section->addListItem("Sistem Peringatan Alarm Berdering Terus-Menerus: Ketika barista menandai pesanan telah 'Siap Diambil', ponsel pelanggan secara otomatis memutar suara dering keras dan getaran berulang hingga pelanggan menekan tombol 'Saya Ambil', memastikan pesanan segera dinikmati dalam suhu terbaiknya.", 0, null, null, $pJustify);

$section->addTitle("1.3 Alasan Pemilihan Stack Teknologi (Why This Stack?)", 2);

$tableTech = $section->addTable('StandardTable');
$tableTech->addRow();
$tableTech->addCell(2500, $tableStyleHeader)->addText("Komponen Teknologi", $fontHeader, $pCenter);
$tableTech->addCell(2000, $tableStyleHeader)->addText("Teknologi Dipilih", $fontHeader, $pCenter);
$tableTech->addCell(4500, $tableStyleHeader)->addText("Alasan Teknis & Keunggulan", $fontHeader, $pCenter);

$techData = [
    [
        "Backend Framework",
        "Laravel 11.x (PHP 8.2)",
        "Memiliki arsitektur MVC yang rapi, Eloquent ORM dengan performa tinggi, fitur database transaction & row locking anti-race condition, serta sintaks middleware modern yang sangat andal untuk sistem antrean transaksional."
    ],
    [
        "Database Engine",
        "MySQL / MariaDB (InnoDB Engine)",
        "Mendukung ACID Compliance secara penuh, row-level locking (SELECT ... FOR UPDATE) untuk mencegah multi-kasir memproses pesanan yang sama, serta foreign key cascading yang aman."
    ],
    [
        "Frontend Styling",
        "Tailwind CSS 3.x",
        "Utility-first CSS framework yang menghasilkan antarmuka mobile-responsive yang modern, ringan, dan cepat di-render oleh peramban ponsel tanpa beban library UI yang berat."
    ],
    [
        "Real-Time Mechanism",
        "Lightweight Polling (2.5s - 3s)",
        "Dipilih karena server free-hosting (shared hosting cPanel/InfinityFree) tidak mengizinkan background daemon process seperti Node.js WebSocket atau Pusher socket berbayar. Polling dengan endpoint JSON berukuran ultra-kecil (< 1 KB) mampu memberikan pengalaman real-time tanpa membebani kuota koneksi server."
    ],
    [
        "Audio Engine",
        "HTML5 Web Audio API",
        "Menghasilkan nada dering alarm frekuensi ganda (880 Hz / 1174 Hz) murni dari prosesor audio peramban tanpa perlu mengunduh file MP3/WAV besar, sehingga alarm dijamin berdering instan tanpa risiko gagal buffering."
    ],
    [
        "Cloud Hosting & Sync",
        "InfinityFree + VS Code SFTP",
        "Menyediakan akses hosting web 100% gratis selamanya tanpa batasan waktu uji coba kartu kredit, terintegrasi langsung dengan editor VS Code melalui protokol FTP port 21 sehingga perubahan kode lokal otomatis terunggah saat tombol simpan ditekan."
    ]
];

foreach ($techData as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableTech->addRow();
    $tableTech->addCell(2500, $rowStyle)->addText($row[0], $fontCellBold);
    $tableTech->addCell(2000, $rowStyle)->addText($row[1], $fontCell);
    $tableTech->addCell(4500, $rowStyle)->addText($row[2], $fontCell, $pJustify);
}

$section->addPageBreak();

// -------------------------------------------------------------
// BAB II: STRUKTUR DIREKTORI & PENJELASAN KODE PROGRAM 1 PER 1
// -------------------------------------------------------------
$section->addTitle("BAB II : STRUKTUR DIREKTORI & PENJELASAN KODE PROGRAM", 1);
$section->addText("Bagian ini menjelaskan struktur folder dan seluruh file kunci dalam proyek secara rinci, mengapa kodingan tersebut dirancang demikian, serta fungsi operasionalnya dalam mendukung algoritma FIFO.", [], $pJustify);

$section->addTitle("2.1 Hierarki Direktori Proyek", 2);
$treeText = "skripsi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php      (Statistik & Laporan Owner)
│   │   │   │   ├── MenuController.php           (Kelola Menu & Upload Foto)
│   │   │   │   ├── CategoryController.php       (Kelola Kategori Menu)
│   │   │   │   ├── OrderManagementController.php(Riwayat Transaksi)
│   │   │   │   ├── SettingController.php        (Pengaturan Kafe & QR Meja)
│   │   │   │   └── UserController.php           (Akun Petugas & Kasir)
│   │   │   ├── Customer/
│   │   │   │   └── CustomerOrderController.php  (Pemesanan, QR Gate & Tracking)
│   │   │   ├── Pegawai/
│   │   │   │   └── PegawaiDashboardController.php (Pos Operasional Barista FIFO)
│   │   │   └── Auth/
│   │   │       └── AuthController.php           (Login/Logout Petugas)
│   │   └── Middleware/
│   │       └── RoleMiddleware.php               (Hak Akses Admin vs Kasir)
│   ├── Models/
│   │   ├── Order.php                            (Entitas Pesanan & Mutator FIFO)
│   │   ├── OrderDetail.php                      (Rincian Menu dalam Pesanan)
│   │   ├── OrderStatusHistory.php               (Audit Log Riwayat Status)
│   │   ├── Menu.php                             (Entitas Sajian & URL Gambar)
│   │   ├── Category.php                         (Kategori Menu)
│   │   ├── Setting.php                          (Pengaturan Sistem & Kafe)
│   │   ├── User.php                             (Data Pegawai & Autentikasi)
│   │   └── Notification.php                     (Log Notifikasi WhatsApp)
│   └── Services/
│       ├── FifoQueueService.php                 (Inti Algoritma Antrean FIFO)
│       ├── QrCodeService.php                    (Generator SVG Standee QR Meja)
│       └── NotificationService.php              (Pemicu Notifikasi WA & Alarm)
├── bootstrap/
│   └── app.php                                  (Konfigurasi Exception CSRF)
├── config/
│   ├── app.php, database.php, session.php
├── database/
│   └── migrations/                              (Skema Tabel Relasional)
├── public/
│   ├── images/                                  (Logo, Banner, Foto Menu Langsung)
│   └── index.php                                (Front Controller)
├── resources/
│   └── views/
│       ├── customer/                            (Tampilan Layar Pelanggan)
│       ├── pegawai/                             (Dashboard Kasir/Barista)
│       ├── admin/                               (Dashboard Pemilik/Admin)
│       └── layouts/                             (Master Layouts)
└── routes/
    └── web.php                                  (Pemetaan URL & Endpoint API)";

$section->addText($treeText, ['name' => 'Consolas', 'size' => 8.5, 'color' => '0F172A']);

$section->addTitle("2.2 Pembahasan Mendalam Service Layer (Jantung Logika Bisnis)", 2);

$section->addText("1. app/Services/FifoQueueService.php", ['bold' => true, 'color' => '166534']);
$section->addText("• Tujuan File: Merupakan implementasi murni dari algoritma First In First Out (FIFO) yang menjadi fokus utama judul skripsi ini.", [], $pJustify);
$section->addText("• Kenapa Kodingannya Seperti Ini:
a. Pengurutan Kueri (getActiveFifoQueue): Menggunakan kueri SQL `ORDER BY received_at ASC, id ASC`. Kriteria utama adalah waktu pesanan diterima server (`received_at`), bukan waktu pelanggan membuka browser. Jika ada dua pesanan masuk pada detik yang persis sama, kolom `id ASC` bertindak sebagai tie-breaker penentu.
b. Penerbitan Nomor Antrean (generateQueueNumber): Mereset nomor antrean harian setiap tanggal baru berganti (contoh: 001, 002, 003). Hal ini menjamin nomor antrean di meja kasir selalu berurutan dari angka 1 setiap harinya.
c. Kalkulasi Metrik (getQueueInfo): Menghitung posisi antrean ke-X dari total antrean aktif, menghitung berapa pesanan yang ada di depan pelanggan (`pesanan_di_depan`), serta mengalikan posisi antrean dengan parameter estimasi durasi menit (`estimated_minutes_per_queue`) yang dapat diatur dinamis oleh admin.", [], $pJustify);

$section->addText("2. app/Services/QrCodeService.php", ['bold' => true, 'color' => '166534']);
$section->addText("• Tujuan File: Menghasilkan kode QR meja dalam format vektor SVG (Scalable Vector Graphics) dan Data URI beresolusi tinggi untuk dicetak pada standee akrilik meja.", [], $pJustify);
$section->addText("• Kenapa Kodingannya Seperti Ini:
a. Format SVG Vektor: Format SVG tidak menggunakan piksel bitmap, sehingga ketika dicetak dalam ukuran akrilik meja berapa pun (A5, A6, kartu meja kecil), kualitas QR Code tetap tajam 100% dan tidak pernah pecah atau buram.
b. Fallback Renderer: Dilengkapi penanganan fallback otomatis. Jika server hosting tidak memiliki ekstensi grafis GD, service secara otomatis beralih ke SVG Base64 encoding tanpa melempar fatal error 500.
c. Penanaman Token Keamanan: URL yang dihasilkan otomatis menempelkan query token rahasia kafe (`/pesan?token=...&meja=XX`) sehingga hanya mereka yang memindai QR fisik di meja yang bisa mengakses menu pemesanan.", [], $pJustify);

$section->addTitle("2.3 Pembahasan Mendalam Controller Layer (Logika Transaksional)", 2);

$section->addText("1. app/Http/Controllers/Customer/CustomerOrderController.php", ['bold' => true, 'color' => '166534']);
$section->addText("• Gerbang QR Meja (Strict QR Gate): Menolak pelanggan yang mencoba mengetik manual alamat URL `/pesan` di peramban tanpa memindai QR code di meja. Akses manual otomatis dialihkan ke halaman peringatan edukatif `/akses-qr-diperlukan`.", [], $pJustify);
$section->addText("• Tiket Sesi Sekali Pakai (Single-Use Session): Begitu pelanggan menekan tombol 'Kirim Pesanan' dan transaksi berhasil dicatat ke basis data (`DB::commit()`), server langsung mengeksekusi:
`session()->forget(['qr_scanned', 'qr_token', 'table_number']);`
dan menghapus cookie. Hal ini mencegah pelanggan menyalahgunakan tautan yang sama untuk memesan berkali-kali tanpa berada di meja kafe.", [], $pJustify);

$section->addText("2. app/Http/Controllers/Pegawai/PegawaiDashboardController.php", ['bold' => true, 'color' => '166534']);
$section->addText("• Aturan Ketat Pemrosesan Antrean FIFO: Pada method `processOrder()`, kasir/barista HANYA BISA mengklik tombol proses pada pesanan yang masuk paling awal. Kodingan memeriksa apakah masih ada pesanan berstatus 'menunggu' yang memiliki waktu masuk lebih lama. Jika ada, sistem mengembalikan status HTTP 422 dan pesan peringatan bahwa pesanan terdahulu harus diproses lebih dahulu.", [], $pJustify);
$section->addText("• Fleksibilitas Penyelesaian Pesanan (readyOrder & completeOrder): Sekalipun urutan pengerjaan wajib FIFO, durasi peracikan menu berbeda-beda (misal: 1 kopi espresso selesai dalam 2 menit, sedangkan 3 porsi makanan berat membutuhkan 12 menit). Barista diizinkan menyelesaikan dan memanggil pesanan yang sudah berstatus 'diproses' secara fleksibel begitu sajian tersebut telah siap saji.", [], $pJustify);
$section->addText("• Database Locking (Anti-Race Condition Multi-Kasir): Menggunakan `Order::where('id', \$id)->lockForUpdate()` di dalam transaksi database (`DB::transaction`). Jika ada dua kasir mengeklik tombol pesanan yang sama secara bersamaan, pesanan dikunci sehingga hanya satu kasir yang berhasil mengeksekusi.", [], $pJustify);

$section->addTitle("2.4 Pembahasan Model & Eloquent Layer", 2);
$section->addText("1. Model Order (app/Models/Order.php):
Mendefinisikan relasi `details()` (one-to-many ke OrderDetail), relasi `statusHistory()` (one-to-many ke OrderStatusHistory), dan relasi `pegawai()` (belongs-to ke User). Menyediakan mutator `status_label` untuk menerjemahkan status teknis menjadi label bahasa Indonesia yang ramah pengguna.", [], $pJustify);

$section->addText("2. Model Menu (app/Models/Menu.php):
Menyediakan accessor pintar `image_url` yang memeriksa keberadaan file di `public/images/menus/` terlebih dahulu, lalu fallback ke `storage/app/public/menus/`, dan akhirnya ke `images/menu-placeholder.svg` jika foto belum diunggah. Pendekatan ini membuat sistem kebal dari masalah symlink hosting.", [], $pJustify);

$section->addPageBreak();

// -------------------------------------------------------------
// BAB III: PERANCANGAN DIAGRAM SISTEM STANDAR SKRIPSI
// -------------------------------------------------------------
$section->addTitle("BAB III : PERANCANGAN DIAGRAM SISTEM (STANDAR SKRIPSI)", 1);
$section->addText("Bab ini menyajikan seluruh diagram pemodelan berstandar baku akademik untuk keperluan penulisan naskah Skripsi, meliputi Flowchart ANSI/ISO, Analisis Sistem Informasi (ASI/Flowmap), Entity Relationship Diagram (ERD), dan Unified Modeling Language (UML).", [], $pJustify);

$section->addTitle("3.1 Standar Simbol Diagram yang Digunakan", 2);

$tableSymbols = $section->addTable('StandardTable');
$tableSymbols->addRow();
$tableSymbols->addCell(2000, $tableStyleHeader)->addText("Nama Simbol", $fontHeader, $pCenter);
$tableSymbols->addCell(2500, $tableStyleHeader)->addText("Bentuk Geometri Standar", $fontHeader, $pCenter);
$tableSymbols->addCell(4500, $tableStyleHeader)->addText("Fungsi & Penerapan dalam Sistem", $fontHeader, $pCenter);

$symbolData = [
    ["Terminator", "Oval / Rounded Rectangle", "Menandai titik awal (Start/Mulai) dan titik akhir (Stop/Selesai) dari suatu alur flowchart."],
    ["Process", "Persegi Panjang (Rectangle)", "Menyatakan proses komputasi, operasi logika, update status database, atau kalkulasi nomor antrean FIFO."],
    ["Decision", "Belah Ketupat (Diamond)", "Menyatakan percabangan keputusan dengan dua atau lebih jalur keluaran (misal: Token Valid?, Stok Ada?, Antrean Kosong?)."],
    ["Data / I/O", "Jajaran Genjang (Parallelogram)", "Menyatakan operasi masukan data (input nama, meja) atau keluaran data (output nomor antrean)."],
    ["Display", "Bentuk Display / Layar", "Menyatakan informasi yang ditampilkan pada antarmuka pengguna (layar monitor kasir / HP pelanggan)."],
    ["Document", "Persegi Dasar Gelombang (Document)", "Menyatakan output cetak dokumen fisik (contoh: Akrilik Standee QR Meja Kafe)."],
    ["Actor (UML)", "Stickman Figure", "Mempresentasikan entitas pengguna yang berinteraksi dengan sistem (Pelanggan, Barista/Kasir, Owner/Admin)."],
    ["Use Case (UML)", "Bentuk Elips Horizontal", "Menyatakan fungsionalitas atau layanan yang disediakan oleh sistem perangkat lunak."],
    ["Entity (ERD)", "Persegi Panjang (Rectangle)", "Menyatakan objek data utama dalam basis data (Users, Menus, Orders, Settings)."],
    ["Relationship (ERD)", "Belah Ketupat / Crow's Foot", "Menyatakan hubungan keterkaitan antar entitas dengan kardinalitas One-to-Many (1:N)."]
];

foreach ($symbolData as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableSymbols->addRow();
    $tableSymbols->addCell(2000, $rowStyle)->addText($row[0], $fontCellBold);
    $tableSymbols->addCell(2500, $rowStyle)->addText($row[1], $fontCell);
    $tableSymbols->addCell(4500, $rowStyle)->addText($row[2], $fontCell, $pJustify);
}

$section->addTitle("3.2 Analisis Sistem Informasi (ASI / Bagan Alir Dokumen Flowmap)", 2);
$section->addText("Analisis sistem membandingkan alur dokumen dan informasi antara Sistem Berjalan (Konvensional) dengan Sistem Diusulkan (Sistem Pemesanan Digital FIFO).", [], $pJustify);

$section->addText("Tabel 3.2.1 Perbandingan Bagan Alir Dokumen (Sistem Berjalan vs Sistem Diusulkan)", ['bold' => true, 'size' => 10]);

$tableASI = $section->addTable('StandardTable');
$tableASI->addRow();
$tableASI->addCell(1500, $tableStyleHeader)->addText("Tahapan Alur", $fontHeader, $pCenter);
$tableASI->addCell(3700, $tableStyleHeader)->addText("Sistem Berjalan (Konvensional/Manual)", $fontHeader, $pCenter);
$tableASI->addCell(3800, $tableStyleHeader)->addText("Sistem Diusulkan (Digital Berbasis FIFO)", $fontHeader, $pCenter);

$asiData = [
    [
        "1. Pemilihan Menu",
        "Pelanggan datang ke counter kasir, berdiri membaca daftar menu cetak di atas meja kasir, menyebabkan antrean fisik memanjang.",
        "Pelanggan langsung duduk di meja yang diinginkan, membuka kamera HP, dan memindai Kode QR Meja. Menu terbuka langsung secara digital dengan foto sajian dan status ketersediaan live."
    ],
    [
        "2. Pencatatan Pesanan",
        "Kasir mencatat pesanan pada kertas nota rangkap dua. Rawan salah tulis menu dan tidak tercatat waktu kedatangan secara presisi.",
        "Pelanggan memilih item sajian di layar HP, memasukkan catatan khusus (misal: 'less ice'), lalu menekan 'Kirim Pesanan'. Sistem mencatat waktu received_at hingga milidetik."
    ],
    [
        "3. Penentuan Urutan (Algoritma)",
        "Tumpukan kertas nota rawan berantakan atau tertukar di meja barista. Pesanan pelanggan yang datang belakangan berpotensi dibuat lebih dahulu.",
        "Algoritma FIFO mengurutkan pesanan secara otomatis berdasarkan timestamp server. Sistem menerbitkan Nomor Antrean unik (001, 002, dst.) yang transparan bagi pelanggan dan barista."
    ],
    [
        "4. Pelacakan Antrean",
        "Pelanggan tidak mengetahui urutan ke berapa pesanannya dan berapa lama waktu tunggu, menimbulkan kegelisahan dan komplain.",
        "Layar ponsel pelanggan memuat Live Tracking FIFO: menampilkan posisi antrean saat ini, berapa pesanan di depan, dan estimasi waktu selesai dalam hitungan menit."
    ],
    [
        "5. Notifikasi Siap Diambil",
        "Barista harus berteriak memanggil nomor/nama pelanggan di ruangan kafe. Jika pelanggan berada di area outdoor, panggilan sering tidak terdengar.",
        "Begitu barista mengubah status ke 'Siap Diambil', ponsel pelanggan otomatis membunyikan alarm suara berdering keras + bergetar terus-menerus + notifikasi WhatsApp darurat."
    ],
    [
        "6. Pelaporan & Evaluasi",
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

$section->addTitle("3.3 Flowchart Sistem (ANSI / ISO Standard)", 2);
$section->addText("Berikut adalah alur logika pemrosesan data sistem dalam bentuk terstruktur:", [], $pJustify);

$flowchartText = "========================================================================
A. FLOWCHART SISTEM PEMESANAN PELANGGAN (CUSTOMER FLOW)
========================================================================
[ START ] (Terminator)
    │
    ▼
[ Pelanggan Pindai Standee QR Akrilik di Meja Kafe ] (Input/Output)
    │
    ▼
< Apakah Query Membawa Token QR Valid? > (Decision)
    ├── [ TIDAK ] ──► [ Tampilkan Halaman 'Akses QR Diperlukan' ] ──► [ END ]
    └── [ YA ]
          │
          ▼
    [ Buat Tiket Sesi Sementera di Browser (qr_scanned = true) ] (Process)
          │
          ▼
    [ Tampilkan Halaman Menu Digital & Status Stok Terkini ] (Display)
          │
          ▼
    [ Pelanggan Memilih Menu & Masukkan Catatan Sajian ] (Process)
          │
          ▼
    [ Buka Modal Keranjang & Isi Nama Pemesan ] (Process)
          │
          ▼
    [ Klik Tombol 'Kirim Pesanan Sekarang' ] (Input/Output)
          │
          ▼
    < Apakah Sesi QR Valid & Keranjang Tidak Kosong? > (Decision)
          ├── [ TIDAK ] ──► [ Tampilkan Pesan Peringatan Validasi ] ──► (Kembali ke Form)
          └── [ YA ]
                │
                ▼
          [ Database Transaction: Catat Waktu Masuk (received_at) ] (Process)
                │
                ▼
          [ Algoritma FIFO: Hitung Nomor Antrean Hari Ini (001, 002, ...) ] (Process)
                │
                ▼
          [ Simpan Data Pesanan ke Tabel 'orders' & 'order_details' ] (Process)
                │
                ▼
          [ HANGUSKAN TIKET SESI QR (session forget & delete cookie) ] (Process)
                │
                ▼
          [ Buka Layar Konfirmasi & Live Tracking Antrean FIFO ] (Display)
                │
                ▼
          [ STOP / Selesai Pemesanan ] (Terminator)

========================================================================
B. FLOWCHART OPERASIONAL KASIR/BARISTA (FIFO DISPATCHER FLOW)
========================================================================
[ START ] (Terminator)
    │
    ▼
[ Barista Login ke Dashboard Pos Operasional (/pegawai/dashboard) ] (Process)
    │
    ▼
[ Sistem Menampilkan Daftar Antrean Aktif (ORDER BY received_at ASC) ] (Display)
    │
    ▼
[ Barista Memilih Pesanan yang Hendak Dikerjakan ] (Input/Output)
    │
    ▼
< Apakah Pesanan Berada di Urutan Terdepan (Status 'menunggu')? > (Decision)
    ├── [ BUKAN TERDEPAN ] ──► [ Kunci Tombol: 'Menunggu Antrean Sebelumnya' ]
    │                           [ Tampilkan SweetAlert: Wajib Urut FIFO ] ──► (Kembali)
    └── [ YA (PRIORITAS 1) ]
          │
          ▼
    [ Eksekusi lockForUpdate() & Ubah Status Menjadi 'diproses' ] (Process)
          │
          ▼
    [ Barista Meracik Sajian Minuman / Makanan di Bar Counter ] (Process)
          │
          ▼
    < Apakah Racikan Sajian Sudah Matang/Selesai? > (Decision)
          └── [ YA ]
                │
                ▼
          [ Klik Tombol 'Pesanan Siap Diambil' ] (Input/Output)
                │
                ▼
          [ Ubah Status: 'siap_diambil' & Catat Timestamp ready_at ] (Process)
                │
                ▼
          [ PICU ALARM SUARA KERAS & GETARAN DI PONSEL PELANGGAN ] (Process)
                │
                ▼
          [ Pelanggan Datang ke Counter Kasir Menunjukkan Nomor Antrean ] (Input/Output)
                │
                ▼
          [ Barista Klik 'Pesanan Selesai' (Status: 'selesai', completed_at) ] (Process)
                │
                ▼
          [ Catat ke Rekapitulasi Metrik Evaluasi FIFO Hari Ini ] (Process)
                │
                ▼
[ STOP / Selesai Pelayanan ] (Terminator)";

$section->addText($flowchartText, ['name' => 'Consolas', 'size' => 8, 'color' => '0F172A']);

$section->addPageBreak();

// -------------------------------------------------------------
// 3.4 ENTITY RELATIONSHIP DIAGRAM (ERD) & DATA DICTIONARY
// -------------------------------------------------------------
$section->addTitle("3.4 Entity Relationship Diagram (ERD) & Kamus Data Basis Data", 2);
$section->addText("Basis data Sekarsa Coffee dirancang menggunakan relasi integritas referensial (Foreign Key Constraints) dengan derajat kardinalitas One-to-Many (1:N) untuk mendukung pelacakan alur pesanan FIFO.", [], $pJustify);

$erdAscii = "
+---------------+          1:N          +-------------------+          1:N          +-----------------------+
|  CATEGORIES   | --------------------< |       MENUS       | --------------------< |     ORDER_DETAILS     |
+---------------+                       +-------------------+                       +-----------------------+
| PK id         |                       | PK id             |                       | PK id                 |
|    name       |                       | FK category_id    |                       | FK order_id           |
|    slug       |                       |    name           |                       | FK menu_id            |
|    order      |                       |    price          |                       |    menu_name          |
+---------------+                       |    image          |                       |    price              |
                                        |    is_available   |                       |    quantity           |
                                        +-------------------+                       |    subtotal           |
                                                                                    |    notes              |
                                                                                    +-----------------------+
                                                                                                ^
                                                                                                | 1:N
+---------------+          1:N          +-------------------------------------------------------+
|     USERS     | --------------------< |                        ORDERS                         |
+---------------+                       +-------------------------------------------------------+
| PK id         |                       | PK id                                                 |
|    name       |                       |    order_number   (Contoh: ORD-20261106-001)          |
|    email      |                       |    queue_number   (Contoh: 001, 002, 003)             |
|    password   |                       |    customer_name                                      |
|    role       |                       |    customer_phone                                     |
+---------------+                       |    table_number                                       |
        |                               |    customer_notes                                     |
        | 1:N                           |    total_price                                        |
        v                               |    status         ('menunggu','diproses','siap_diambil','selesai')
+-----------------------+               |    received_at    (Timestamp Algoritma FIFO)          |
| ORDER_STATUS_HISTORY  | >------------ |    processed_at                                       |
+-----------------------+         1:N   |    ready_at                                           |
| PK id                 |               |    completed_at                                       |
| FK order_id           |               | FK handled_by                                         |
|    previous_status    |               |    tracking_token (Token 40 Karakter Acak)            |
|    new_status         |               +-------------------------------------------------------+
|    changed_by         |                                           |
|    notes              |                                           | 1:N
|    created_at         |                                           v
+-----------------------+                       +-----------------------------------------------+
                                                |                 NOTIFICATIONS                 |
+-----------------------+                       +-----------------------------------------------+
|       SETTINGS        |                       | PK id                                         |
+-----------------------+                       | FK order_id                                   |
| PK id                 |                       |    phone_number                               |
|    cafe_name          |                       |    message_content                            |
|    cafe_logo          |                       |    status                                     |
|    cafe_banner        |                       |    sent_at                                    |
|    qr_access_key      |                       +-----------------------------------------------+
|    estimated_minutes  |
|    require_qr_scan    |
+-----------------------+
";

$section->addText($erdAscii, ['name' => 'Consolas', 'size' => 7.5, 'color' => '0F172A']);

$section->addTitle("3.4.1 Kamus Data Entitas Utama (Data Dictionary)", 3);

$tableKamus = $section->addTable('StandardTable');
$tableKamus->addRow();
$tableKamus->addCell(1800, $tableStyleHeader)->addText("Nama Kolom", $fontHeader, $pCenter);
$tableKamus->addCell(1600, $tableStyleHeader)->addText("Tipe Data", $fontHeader, $pCenter);
$tableKamus->addCell(1000, $tableStyleHeader)->addText("Null / Key", $fontHeader, $pCenter);
$tableKamus->addCell(4600, $tableStyleHeader)->addText("Fungsi & Penerapan dalam Algoritma FIFO", $fontHeader, $pCenter);

$kamusOrders = [
    ["id", "BIGINT UNSIGNED", "NO / PK", "Identifikator baris unik dalam tabel basis data."],
    ["order_number", "VARCHAR(30)", "NO / Unique", "Kode faktur transaksi kafe unik (format: ORD-YYYYMMDD-XXX)."],
    ["queue_number", "VARCHAR(10)", "NO", "Nomor antrean harian algoritma FIFO (format 3 digit: 001, 002, dst.)."],
    ["customer_name", "VARCHAR(100)", "NO", "Nama pelanggan yang memesan, dipanggil barista pada saat pesanan siap."],
    ["table_number", "VARCHAR(20)", "YES", "Nomor meja fisik pelanggan (diperoleh otomatis dari scan QR standee meja)."],
    ["status", "ENUM", "NO", "Status siklus hidup pesanan: 'menunggu', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan'."],
    ["received_at", "DATETIME", "NO", "WAKTU KUNCI FIFO: Waktu saat server menerima pesanan, menjadi dasar utama sorting."],
    ["processed_at", "DATETIME", "YES", "Waktu saat barista mengambil dan mulai meracik sajian."],
    ["ready_at", "DATETIME", "YES", "Waktu saat sajian selesai diracik dan alarm HP pelanggan dibunyikan."],
    ["completed_at", "DATETIME", "YES", "Waktu saat pelanggan menerima sajian di counter dan transaksi ditutup."],
    ["handled_by", "BIGINT UNSIGNED", "YES / FK", "Relasi ke tabel users: ID kasir/barista yang menangani pesanan."],
    ["tracking_token", "VARCHAR(64)", "NO / Unique", "Hash acak 40 karakter untuk URL pelacakan live pelanggan tanpa perlu registrasi akun."]
];

foreach ($kamusOrders as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableKamus->addRow();
    $tableKamus->addCell(1800, $rowStyle)->addText($row[0], $fontCellBold);
    $tableKamus->addCell(1600, $rowStyle)->addText($row[1], $fontCell);
    $tableKamus->addCell(1000, $rowStyle)->addText($row[2], $fontCell);
    $tableKamus->addCell(4600, $rowStyle)->addText($row[3], $fontCell, $pJustify);
}

$section->addPageBreak();

// -------------------------------------------------------------
// 3.5 UNIFIED MODELING LANGUAGE (UML) LENGKAP
// -------------------------------------------------------------
$section->addTitle("3.5 Unified Modeling Language (UML) Lengkap", 2);

$section->addTitle("3.5.1 Use Case Diagram & Skenario Use Case", 3);
$section->addText("Use Case Diagram memodelkan interaksi antara tiga aktor sistem: Pelanggan (Customer), Pegawai (Barista/Kasir), dan Admin (Owner).", [], $pJustify);

$useCaseAscii = "
                       SISTEM PEMESANAN DIGITAL FIFO SEKARSA COFFEE
  +-----------------------------------------------------------------------------------+
  |                                                                                   |
  |    ( UC-01: Pindai Kode QR Meja ) <───────────────────────────────── [ Pelanggan ]
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-02: Buka Katalog Menu Digital ) <───────────────────────────────────┤
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-03: Kirim Pesanan & Menerima No. Antrean FIFO ) <───────────────────┤
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-04: Pantau Posisi Antrean Live (Real-Time Tracking) ) <─────────────┤
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-05: Menerima Notifikasi Alarm Suara & Pop-up Siap Diambil ) <───────┘
  |
  |
  |    ( UC-06: Autentikasi / Login Petugas ) <───────────────────────── [ Pegawai / Barista ]
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-07: Monitoring Antrean FIFO Real-Time di Pos Barista ) <────────────┤
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-08: Proses Pesanan Terdepan Sesuai Prioritas FIFO ) <───────────────┤
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-09: Ubah Status 'Siap Diambil' & Picu Alarm HP Pelanggan ) <────────┤
  |                 │                                                           │
  |                 ▼ <<include>>                                               │
  |    ( UC-10: Serahkan Pesanan & Ubah Status 'Selesai' ) <────────────────────┘
  |
  |
  |    ( UC-11: Manajemen Data Menu & Ketersediaan Stok ) <────────────── [ Admin / Owner ]
  |    ( UC-12: Cetak Standee Akrilik Kode QR Meja ) <──────────────────────────┤
  |    ( UC-13: Pengaturan Tampilan & Waktu Estimasi Antrean FIFO ) <────────────┤
  |    ( UC-14: Rekapitulasi Laporan Penjualan & Evaluasi Kinerja FIFO ) <──────┘
  |                                                                                   |
  +-----------------------------------------------------------------------------------+
";

$section->addText($useCaseAscii, ['name' => 'Consolas', 'size' => 7.5, 'color' => '0F172A']);

$section->addTitle("3.5.2 Tabel Skenario Use Case Kunci (Spesifikasi Akademik)", 3);

$tableUC = $section->addTable('StandardTable');
$tableUC->addRow();
$tableUC->addCell(2500, $tableStyleHeader)->addText("Atribut Skenario", $fontHeader, $pCenter);
$tableUC->addCell(6500, $tableStyleHeader)->addText("Deskripsi Rinci", $fontHeader, $pCenter);

$ucRows = [
    ["Nomor & Nama Use Case", "UC-08 : Proses Pesanan Terdepan Sesuai Prioritas FIFO"],
    ["Aktor Terlibat", "Pegawai / Barista"],
    ["Deskripsi Singkat", "Barista mengubah status pesanan dari 'menunggu' menjadi 'diproses' untuk mulai meracik pesanan di bar kafe."],
    ["Kondisi Awal (Pre-condition)", "Barista sudah login dan berada di layar `/pegawai/dashboard`. Terdapat minimal satu pesanan dengan status 'menunggu'."],
    ["Alur Utama (Main Flow)", "1. Barista mengidentifikasi kartu pesanan pada antrean terdepan (Nomor Antrean prioritas #1).\n2. Barista menekan tombol biru 'Mulai Proses (Prioritas FIFO)'.\n3. Server melakukan penguncian baris database (lockForUpdate) dan memverifikasi bahwa tidak ada pesanan lain yang masuk lebih awal.\n4. Server memperbarui status menjadi 'diproses' dan mencatat nama barista pada kolom `handled_by`.\n5. Status pesanan di layar HP pelanggan otomatis berubah menjadi 'Sedang Diproses'."],
    ["Alur Alternatif (Alternative Flow)", "Jika barista mencoba mengeklik pesanan urutan kedua atau ketiga yang berstatus 'menunggu':\n1. Sistem memblokir aksi klik dan menampilkan SweetAlert peringatan: 'Sesuai ketentuan FIFO, Anda wajib memproses pesanan Nomor Antrean #XXX terlebih dahulu'.\n2. Status database tidak berubah."],
    ["Kondisi Akhir (Post-condition)", "Pesanan berada pada status 'diproses', riwayat status tercatat di tabel `order_status_history`, dan pesanan urutan berikutnya otomatis membuka kuncinya menjadi antrean prioritas."]
];

foreach ($ucRows as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableUC->addRow();
    $tableUC->addCell(2500, $rowStyle)->addText($row[0], $fontCellBold);
    $tableUC->addCell(6500, $rowStyle)->addText($row[1], $fontCell, $pJustify);
}

$section->addTitle("3.5.3 Activity Diagram Alur Sistem", 3);
$section->addText("Activity Diagram memodelkan aliran aktivitas dan transisi status antar swimlane aktor.", [], $pJustify);

$actText = "
+───────────────────────────+───────────────────────────+───────────────────────────+
|         PELANGGAN         |     SISTEM / SERVER       |      BARISTA / KASIR      |
+───────────────────────────+───────────────────────────+───────────────────────────+
| (•) Mulai                 |                           |                           |
|  │                        |                           |                           |
| [ Scan QR Meja ]          |                           |                           |
|  │                        |                           |                           |
|  └───────────────────────►| [ Verifikasi QR Token ]   |                           |
|                           |  │                        |                           |
|                           | < Valid? >                |                           |
|                           |  ├── [ Tidak ] ──► (X)    |                           |
|                           |  └── [ Ya ]               |                           |
|                           |       │                   |                           |
|                           | [ Beri Sesi Menu ]        |                           |
|                           |  │                        |                           |
|◄──────────────────────────┘                           |                           |
|  │                                                    |                           |
| [ Pilih Menu & Catatan ]                              |                           |
|  │                                                    |                           |
| [ Klik Kirim Pesanan ]                                |                           |
|  │                                                    |                           |
|  └───────────────────────►| [ Jalankan Transaksi DB ] |                           |
|                           | [ Hitung FIFO Queue # ]   |                           |
|                           | [ Hapus Tiket Sesi QR ]   |                           |
|                           |  │                        |                           |
|◄──────────────────────────┼──────────────────────────►| [ Notifikasi Antrean Masuk ]|
| [ Pantau Posisi Antrean ] |                           |  │                        |
|                           |                           | < Cek Urutan FIFO >       |
|                           |                           |  ├── [ Bukan #1 ] ──► (X) |
|                           |                           |  └── [ Urutan #1 ]        |
|                           |                           |       │                   |
|                           |                           | [ Klik 'Mulai Proses' ]   |
|                           |                           |  │                        |
|                           |◄──────────────────────────┘                           |
|                           | [ Update: 'diproses' ]                                |
|                           |  │                                                    |
|                           |  └───────────────────────►| [ Meracik Sajian di Bar ] |
|                           |                           |  │                        |
|                           |                           | [ Sajian Selesai ]        |
|                           |                           |  │                        |
|                           |                           | [ Klik 'Siap Diambil' ]   |
|                           |                           |  │                        |
|                           |◄──────────────────────────┘                           |
|                           | [ Update: 'siap_diambil'] |                           |
|                           | [ Kirim Sinyal Alarm ]    |                           |
|                           |  │                        |                           |
|◄──────────────────────────┘                           |                           |
| [ HP Berdering Keras ]                                |                           |
| [ Tekan 'Ambil Sekarang']                             |                           |
|  │                                                    |                           |
| [ Menuju Counter Kasir ]  |                           |                           |
|  │                                                    |                           |
|  └───────────────────────────────────────────────────►| [ Serahkan Sajian ]       |
|                                                       | [ Klik 'Selesai' ]        |
|                                                       |  │                        |
|                                                       | (•) Selesai               |
+───────────────────────────+───────────────────────────+───────────────────────────+
";
$section->addText($actText, ['name' => 'Consolas', 'size' => 7.5, 'color' => '0F172A']);

$section->addTitle("3.5.4 Sequence Diagram Pemesanan & Pemicu Alarm Siap", 3);

$seqText = "
Pelanggan             Browser HP              WebServer / FIFO          Database MySQL            Kasir / Barista
   │                      │                         │                         │                         │
   │─── Scan QR Meja ────►│                         │                         │                         │
   │                      │─── GET /pesan?token ───►│                         │                         │
   │                      │                         │── Validasi QR Gate ────►│                         │
   │                      │◄── Render Katalog Menu ─│                         │                         │
   │─── Pilih Menu & Kirim│                         │                         │                         │
   │                      │─── POST /pesan/kirim ──►│                         │                         │
   │                      │                         │── DB::beginTransaction ─►                         │
   │                      │                         │── FIFO::generateQueue ──►                         │
   │                      │                         │── INSERT INTO orders ──►│                         │
   │                      │                         │── DB::commit ──────────►│                         │
   │                      │                         │── session()->forget() ──│                         │
   │                      │◄── Return URL Confirm ──│                         │                         │
   │◄── No. Antrean FIFO ─│                         │                         │── Polling Antrean Baru ─│
   │                      │                         │                         │◄── GET /api/antrean ────│
   │                      │                         │                         │                         │
   │                      │                         │                         │◄── Klik 'Mulai Proses' ─│
   │                      │                         │                         │─── Cek FIFO Urutan #1 ──│
   │                      │                         │                         │─── UPDATE: 'diproses' ──│
   │                      │                         │                         │                         │
   │                      │                         │                         │◄── Klik 'Siap Diambil' ─│
   │                      │                         │                         │─── UPDATE: 'siap_ambil'─│
   │                      │                         │                         │                         │
   │                      │◄── Polling Status Siap ─│                         │                         │
   │◄── DERINGKAN ALARM ──│ (Web Audio API Berbunyi)│                         │                         │
   │─── Klik 'Saya Ambil'►│ (Alarm Mati)            │                         │                         │
   │                      │                         │                         │◄── Klik 'Selesai' ──────│
   │                      │                         │                         │─── UPDATE: 'selesai' ───│
";
$section->addText($seqText, ['name' => 'Consolas', 'size' => 8, 'color' => '0F172A']);

$section->addPageBreak();

// -------------------------------------------------------------
// BAB IV: PANDUAN PENGUJIAN BLACK-BOX & ATURAN LOGIKA BISNIS
// -------------------------------------------------------------
$section->addTitle("BAB IV : PANDUAN PENGUJIAN BLACK-BOX & ATURAN BISNIS", 1);
$section->addText("Bab ini menyajikan tabel pengujian fungsionalitas sistem (Black-Box Testing) yang membuktikan kebenaran implementasi logika FIFO di hadapan Dosen Penguji.", [], $pJustify);

$tableTesting = $section->addTable('StandardTable');
$tableTesting->addRow();
$tableTesting->addCell(800, $tableStyleHeader)->addText("No", $fontHeader, $pCenter);
$tableTesting->addCell(2000, $tableStyleHeader)->addText("Skenario Pengujian", $fontHeader, $pCenter);
$tableTesting->addCell(2600, $tableStyleHeader)->addText("Aksi / Uji Coba Dilakukan", $fontHeader, $pCenter);
$tableTesting->addCell(2600, $tableStyleHeader)->addText("Hasil yang Diharapkan", $fontHeader, $pCenter);
$tableTesting->addCell(1000, $tableStyleHeader)->addText("Status", $fontHeader, $pCenter);

$tests = [
    [
        "1",
        "Akses Manual Tanpa Scan QR Meja",
        "Mengetikkan langsung URL 'http://sekarsacoffee.gt.tc/pesan' di peramban browser HP tanpa membawa query token.",
        "Sistem menolak akses dan langsung mengalihkan ke halaman '/akses-qr-diperlukan' (Akses Terkunci).",
        "VALID [OK]"
    ],
    [
        "2",
        "Pemindaian Kode QR Meja Fisik",
        "Memindai standee akrilik QR Code di Meja 02 yang membawa token resmi kafe.",
        "Sistem memvalidasi token, membuka katalog menu digital, dan otomatis mengunci nomor meja '02'.",
        "VALID [OK]"
    ],
    [
        "3",
        "Penerbitan Nomor Antrean FIFO",
        "Pelanggan pertama mengirim pesanan jam 08:00, pelanggan kedua mengirim pesanan jam 08:02.",
        "Pelanggan pertama mendapat No. Antrean #001 (Posisi 1), pelanggan kedua mendapat No. Antrean #002 (Posisi 2).",
        "VALID [OK]"
    ],
    [
        "4",
        "Uji Coba Single-Use QR Ticket",
        "Setelah mengirim pesanan, pelanggan mencoba menekan tombol 'Back' atau mengetik ulang URL '/pesan'.",
        "Sistem menolak karena tiket sesi sudah dihapus (session forget). Pelanggan wajib scan QR meja kembali.",
        "VALID [OK]"
    ],
    [
        "5",
        "Pencegahan Pemrosesan Loncat (Kasir)",
        "Terdapat pesanan #001 dan #002 (keduanya status 'menunggu'). Kasir mencoba mengeklik tombol proses pada pesanan #002.",
        "Sistem mengunci tombol pesanan #002 ('Menunggu Antrean #001') dan menolak aksi dengan pesan wajib urut FIFO.",
        "VALID [OK]"
    ],
    [
        "6",
        "Pemrosesan Tertib FIFO (Kasir)",
        "Kasir mengeklik tombol 'Mulai Proses (Prioritas FIFO)' pada pesanan terdepan #001.",
        "Pesanan #001 berubah status menjadi 'diproses'. Seketika itu juga, tombol pesanan #002 otomatis membuka kuncinya.",
        "VALID [OK]"
    ],
    [
        "7",
        "Penyelesaian Fleksibel (Service Time)",
        "Pesanan #001 (makanan berat) dan #002 (es kopi) sama-sama status 'diproses'. Es kopi jadi lebih cepat.",
        "Kasir berhasil mengubah pesanan #002 menjadi 'siap_diambil' dan 'selesai' tanpa harus menunggu pesanan #001.",
        "VALID [OK]"
    ],
    [
        "8",
        "Pemicu Alarm Suara Kencang di Ponsel",
        "Kasir menekan tombol 'Pesanan Siap Diambil' pada dashboard pegawai.",
        "Ponsel pelanggan memutar nada dering kencang Web Audio API, memunculkan modal darurat, dan berdering hingga ditekan tombol ambil.",
        "VALID [OK]"
    ],
    [
        "9",
        "Sinkronisasi VS Code SFTP & Git",
        "Developer mengubah file di VS Code lokal lalu menekan Ctrl + S.",
        "File terunggah otomatis ke server hosting InfinityFree dalam 1-2 detik tanpa perlu upload file manual di cPanel.",
        "VALID [OK]"
    ]
];

foreach ($tests as $row) {
    $rowStyle = ((int)$row[0] % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $tableTesting->addRow();
    $tableTesting->addCell(800, $rowStyle)->addText($row[0], $fontCellBold, $pCenter);
    $tableTesting->addCell(2000, $rowStyle)->addText($row[1], $fontCellBold);
    $tableTesting->addCell(2600, $rowStyle)->addText($row[2], $fontCell, $pJustify);
    $tableTesting->addCell(2600, $rowStyle)->addText($row[3], $fontCell, $pJustify);
    $tableTesting->addCell(1000, $rowStyle)->addText($row[4], ['name' => 'Consolas', 'size' => 8.5, 'bold' => true, 'color' => '166534'], $pCenter);
}

$section->addPageBreak();

// -------------------------------------------------------------
// BAB V: KESIMPULAN & PETUNJUK KELENGKAPAN SIDANG SKRIPSI
// -------------------------------------------------------------
$section->addTitle("BAB V : KESIMPULAN & PETUNJUK SIDANG SKRIPSI", 1);

$section->addTitle("5.1 Kesimpulan Teknis", 2);
$section->addText("1. Penerapan Algoritma First In First Out (FIFO) pada Sekarsa Coffee telah berhasil diimplementasikan secara komputasional menggunakan parameter server timestamp (`received_at ASC, id ASC`), memberikan transparansi antrean nomor urut bagi pelanggan dan barista.", [], $pJustify);
$section->addText("2. Gerbang Keamanan QR Meja Tunggal (Single-Use QR Gate) berhasil mencegah pemesanan fiktif dari luar kafe tanpa perlu mengandalkan sinyal GPS ponsel yang kerap tidak stabil di dalam ruangan (*indoor*).", [], $pJustify);
$section->addText("3. Pembagian peran operasional kasir (aturan pemrosesan masuk ketat FIFO vs aturan penyelesaian fleksibel sesuai kompleksitas menu) menjawab dinamika operasional industri F&B modern secara ilmiah dan praktis.", [], $pJustify);

$section->addTitle("5.2 Petunjuk Saat Menghadapi Pertanyaan Dosen Penguji", 2);

$qnaTable = $section->addTable('StandardTable');
$qnaTable->addRow();
$qnaTable->addCell(3500, $tableStyleHeader)->addText("Potensi Pertanyaan Dosen Penguji", $fontHeader, $pCenter);
$qnaTable->addCell(5500, $tableStyleHeader)->addText("Jawaban Akademik & Teknis Terbaik", $fontHeader, $pCenter);

$qnaData = [
    [
        "Kenapa memilih algoritma FIFO, bukan Shortest Job First (SJF) atau Priority Scheduling?",
        "Algoritma FIFO menjamin keadilan pelayanan (fairness). Pada bisnis kedai kopi, pelanggan yang datang lebih awal memiliki ekspektasi psikologis untuk dilayani terlebih dahulu. Jika menggunakan SJF, pelanggan yang memesan menu kompleks akan mengalami starvation (tertunda terus-menerus jika ada pesanan kecil yang datang belakangan)."
    ],
    [
        "Bagaimana jika dua pesanan masuk pada detik yang persis sama?",
        "Sistem kami menggunakan tie-breaker deterministik pada level database kueri: `ORDER BY received_at ASC, id ASC`. Kolom auto-increment ID yang bersifat unik dan sekuensial menjamin bahwa urutan antrean tetap konsisten 100% dan tidak akan pernah menghasilkan nomor antrean ganda."
    ],
    [
        "Kenapa fitur GPS geofencing dihapus dari sistem kafe?",
        "GPS ponsel mengandalkan sinyal satelit line-of-sight yang mengalami pelemahan drastis (attenuation) saat terhalang atap dak beton gedung kafe, menghasilkan margin error 15 - 50 meter yang sering menolak pelanggan sah di meja. Proteksi fisik Kode QR Akrilik Meja jauh lebih presisi (tingkat kepastian lokasi meja 100%) dan tidak membebani baterai ponsel pelanggan."
    ],
    [
        "Kenapa kasir tidak bisa memproses pesanan ke-2 sebelum pesanan ke-1 diproses?",
        "Ini adalah pembuktian penegakan aturan FIFO pada tahap antrean masuk (input queue). Jika kasir bebas mengeklik pesanan mana pun yang ingin ia buat terlebih dahulu, maka esensi algoritma FIFO akan gugur oleh preferensi subjektif manusia. Sistem mengunci pemrosesan agar barista wajib mengambil pesanan terdepan."
    ]
];

foreach ($qnaData as $i => $row) {
    $rowStyle = ($i % 2 === 0) ? $tableStyleRowEven : $tableStyleRowOdd;
    $qnaTable->addRow();
    $qnaTable->addCell(3500, $rowStyle)->addText($row[0], $fontCellBold, $pJustify);
    $qnaTable->addCell(5500, $rowStyle)->addText($row[1], $fontCell, $pJustify);
}

// -------------------------------------------------------------
// PENYIMPANAN DOKUMEN WORD (.DOCX)
// -------------------------------------------------------------
$fileName = 'DOKUMENTASI_KODE_DAN_PERANCANGAN_SISTEM_SKRIPSI_SEKARSA_COFFEE.docx';
$outputPath = __DIR__ . '/' . $fileName;

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($outputPath);

echo "Berhasil! Dokumen Word telah disimpan di:\n$outputPath\nUkuran File: " . filesize($outputPath) . " bytes\n";
