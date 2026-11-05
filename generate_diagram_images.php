<?php

// Script Pembuat Diagram Visual Beresolusi Tinggi (PNG) untuk Skripsi Sekarsa Coffee
// Menggunakan PHP GD

echo "Memulai pembuatan 8 gambar diagram visual berstandar akademik...\n";

if (!is_dir(__DIR__ . '/diagrams')) {
    mkdir(__DIR__ . '/diagrams', 0777, true);
}

// Helper: Buat Gambar dengan Anti-Aliasing & Background Halus
function createBaseImage($w, $h) {
    $img = imagecreatetruecolor($w, $h);
    imagealphablending($img, true);
    imagesavealpha($img, true);
    $bg = imagecolorallocate($img, 255, 255, 255);
    imagefilledrectangle($img, 0, 0, $w, $h, $bg);
    return $img;
}

// Helper: Kotak Sudut Membulat (Rounded Rectangle)
function drawRoundedRect($img, $x1, $y1, $x2, $y2, $radius, $fillColor, $borderColor = null) {
    // Isi
    imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $fillColor);
    imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $fillColor);
    imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $fillColor);
    imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $fillColor);
    imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $fillColor);
    imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $fillColor);

    if ($borderColor) {
        imageline($img, $x1 + $radius, $y1, $x2 - $radius, $y1, $borderColor);
        imageline($img, $x1 + $radius, $y2, $x2 - $radius, $y2, $borderColor);
        imageline($img, $x1, $y1 + $radius, $x1, $y2 - $radius, $borderColor);
        imageline($img, $x2, $y1 + $radius, $x2, $y2 - $radius, $borderColor);
        imagearc($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, 180, 270, $borderColor);
        imagearc($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, 270, 360, $borderColor);
        imagearc($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, 90, 180, $borderColor);
        imagearc($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, 0, 90, $borderColor);
    }
}

// Helper: Belah Ketupat (Decision Diamond)
function drawDiamond($img, $cx, $cy, $w, $h, $fillColor, $borderColor) {
    $points = [
        $cx, $cy - $h / 2,
        $cx + $w / 2, $cy,
        $cx, $cy + $h / 2,
        $cx - $w / 2, $cy
    ];
    imagefilledpolygon($img, $points, $fillColor);
    imagepolygon($img, $points, $borderColor);
}

// Helper: Jajaran Genjang (Data I/O Parallelogram)
function drawParallelogram($img, $x, $y, $w, $h, $skew, $fillColor, $borderColor) {
    $points = [
        $x + $skew, $y,
        $x + $w, $y,
        $x + $w - $skew, $y + $h,
        $x, $y + $h
    ];
    imagefilledpolygon($img, $points, $fillColor);
    imagepolygon($img, $points, $borderColor);
}

// Helper: Panah Turun
function drawDownArrow($img, $x, $y1, $y2, $color, $label = '') {
    imageline($img, $x, $y1, $x, $y2, $color);
    imageline($img, $x - 1, $y1, $x - 1, $y2, $color);
    // Kepala Panah
    $points = [$x, $y2, $x - 5, $y2 - 10, $x + 5, $y2 - 10];
    imagefilledpolygon($img, $points, $color);
    if ($label) {
        imagestring($img, 3, $x + 8, ($y1 + $y2) / 2 - 8, $label, $color);
    }
}

// Helper: Panah Kanan
function drawRightArrow($img, $x1, $x2, $y, $color, $label = '') {
    imageline($img, $x1, $y, $x2, $y, $color);
    imageline($img, $x1, $y - 1, $x2, $y - 1, $color);
    $points = [$x2, $y, $x2 - 10, $y - 5, $x2 - 10, $y + 5];
    imagefilledpolygon($img, $points, $color);
    if ($label) {
        imagestring($img, 3, ($x1 + $x2) / 2 - (strlen($label)*4), $y - 16, $label, $color);
    }
}

// -------------------------------------------------------------------------
// 1. DIAGRAM FLOWCHART PEMESANAN PELANGGAN (ANSI STANDARD)
// -------------------------------------------------------------------------
echo "1. Membuat Diagram Flowchart Pemesanan Pelanggan (ANSI)...\n";
$w = 900; $h = 1050;
$img1 = createBaseImage($w, $h);

$cNavy   = imagecolorallocate($img1, 15, 23, 42);
$cGreen  = imagecolorallocate($img1, 22, 101, 52);
$cGreenL = imagecolorallocate($img1, 240, 253, 244);
$cBlueL  = imagecolorallocate($img1, 239, 246, 255);
$cBlueB  = imagecolorallocate($img1, 37, 99, 235);
$cGoldL  = imagecolorallocate($img1, 254, 252, 232);
$cGoldB  = imagecolorallocate($img1, 202, 138, 4);
$cRedL   = imagecolorallocate($img1, 254, 242, 242);
$cRedB   = imagecolorallocate($img1, 220, 38, 38);
$cGrayB  = imagecolorallocate($img1, 100, 116, 139);
$cWhite  = imagecolorallocate($img1, 255, 255, 255);

// Judul Flowchart
imagestring($img1, 5, 220, 20, "FLOWCHART ALUR PEMESANAN PELANGGAN (ANSI/ISO 5807)", $cNavy);
imageline($img1, 50, 45, 850, 45, $cGreen);

// 1. START
drawRoundedRect($img1, 375, 60, 525, 100, 20, $cGreenL, $cGreen);
imagestring($img1, 4, 425, 72, "START", $cGreen);
drawDownArrow($img1, 450, 100, 140, $cNavy);

// 2. Scan QR Meja
drawParallelogram($img1, 275, 140, 350, 50, 25, $cBlueL, $cBlueB);
imagestring($img1, 3, 310, 158, "Pelanggan Pindai QR Akrilik di Meja", $cNavy);
drawDownArrow($img1, 450, 190, 230, $cNavy);

// 3. Decision: Token Valid?
drawDiamond($img1, 450, 270, 260, 80, $cGoldL, $cGoldB);
imagestring($img1, 3, 375, 263, "Token QR Valid?", $cNavy);

// Jalur Tidak Valid
drawRightArrow($img1, 580, 680, 270, $cRedB, "TIDAK");
drawRoundedRect($img1, 680, 245, 870, 295, 8, $cRedL, $cRedB);
imagestring($img1, 2, 695, 255, "Dialihkan ke Halaman", $cRedB);
imagestring($img1, 2, 695, 270, "'Akses QR Diperlukan'", $cRedB);

drawDownArrow($img1, 775, 295, 340, $cRedB);
drawRoundedRect($img1, 725, 340, 825, 375, 17, $cRedL, $cRedB);
imagestring($img1, 3, 755, 350, "END", $cRedB);

// Jalur Valid (YA)
drawDownArrow($img1, 450, 310, 360, $cGreen, "YA");

// 4. Buka Katalog Menu
drawRoundedRect($img1, 275, 360, 625, 410, 8, $cBlueL, $cBlueB);
imagestring($img1, 3, 295, 378, "Simpan Sesi QR & Buka Katalog Menu Digital", $cNavy);
drawDownArrow($img1, 450, 410, 450, $cNavy);

// 5. Pilih Menu & Isi Nama
drawRoundedRect($img1, 275, 450, 625, 500, 8, $cBlueL, $cBlueB);
imagestring($img1, 3, 305, 468, "Pilih Menu, Atur Catatan & Isi Nama Pemesan", $cNavy);
drawDownArrow($img1, 450, 500, 540, $cNavy);

// 6. Klik Kirim Pesanan
drawParallelogram($img1, 300, 540, 300, 50, 20, $cBlueL, $cBlueB);
imagestring($img1, 3, 335, 558, "Klik 'Kirim Pesanan Sekarang'", $cNavy);
drawDownArrow($img1, 450, 590, 630, $cNavy);

// 7. Decision: Keranjang Kosong?
drawDiamond($img1, 450, 670, 280, 80, $cGoldL, $cGoldB);
imagestring($img1, 3, 350, 663, "Keranjang Berisi Item?", $cNavy);

// Jalur Kosong
drawRightArrow($img1, 590, 690, 670, $cRedB, "TIDAK");
drawRoundedRect($img1, 690, 645, 860, 695, 8, $cRedL, $cRedB);
imagestring($img1, 2, 705, 663, "Peringatan: Keranjang Kosong", $cRedB);
// Garis kembali ke atas
imageline($img1, 775, 645, 775, 475, $cRedB);
drawRightArrow($img1, 775, 625, 475, $cRedB);

// Jalur Isi (YA)
drawDownArrow($img1, 450, 710, 760, $cGreen, "YA");

// 8. DB Transaction & Algoritma FIFO
drawRoundedRect($img1, 220, 760, 680, 820, 8, $cGreenL, $cGreen);
imagestring($img1, 3, 240, 775, "DB Transaction: Catat received_at, Eksekusi FIFO Queue", $cGreen);
imagestring($img1, 3, 260, 795, "Generate No. Antrean Harian (001, 002) & Simpan Order", $cGreen);
drawDownArrow($img1, 450, 820, 860, $cNavy);

// 9. Hanguskan Tiket Sesi QR
drawRoundedRect($img1, 250, 860, 650, 910, 8, $cGoldL, $cGoldB);
imagestring($img1, 3, 275, 878, "Hanguskan Sesi QR (Single-Use Token Hangus)", $cNavy);
drawDownArrow($img1, 450, 910, 950, $cNavy);

// 10. Live Tracking & Selesai
drawRoundedRect($img1, 260, 950, 640, 995, 8, $cBlueL, $cBlueB);
imagestring($img1, 3, 285, 968, "Buka Layar Konfirmasi & Live Tracking FIFO", $cNavy);
drawDownArrow($img1, 450, 995, 1020, $cGreen);

drawRoundedRect($img1, 385, 1020, 515, 1045, 12, $cGreenL, $cGreen);
imagestring($img1, 3, 435, 1026, "SELESAI", $cGreen);

imagepng($img1, __DIR__ . '/diagrams/diagram_flowchart_customer.png');
imagedestroy($img1);

// -------------------------------------------------------------------------
// 2. DIAGRAM FLOWCHART OPERASIONAL KASIR/BARISTA (FIFO DISPATCHER)
// -------------------------------------------------------------------------
echo "2. Membuat Diagram Flowchart Operasional Kasir/Barista (FIFO)...\n";
$w = 900; $h = 950;
$img2 = createBaseImage($w, $h);

imagestring($img2, 5, 190, 20, "FLOWCHART DISPATCHER FIFO KASIR & BARISTA (ANSI/ISO 5807)", $cNavy);
imageline($img2, 50, 45, 850, 45, $cGreen);

// 1. START
drawRoundedRect($img2, 375, 60, 525, 100, 20, $cGreenL, $cGreen);
imagestring($img2, 4, 425, 72, "START", $cGreen);
drawDownArrow($img2, 450, 100, 140, $cNavy);

// 2. Login Kasir/Barista
drawRoundedRect($img2, 280, 140, 620, 190, 8, $cBlueL, $cBlueB);
imagestring($img2, 3, 310, 158, "Barista/Kasir Login ke Dashboard Pos Barista", $cNavy);
drawDownArrow($img2, 450, 190, 230, $cNavy);

// 3. Monitor Antrean Live
drawRoundedRect($img2, 260, 230, 640, 280, 8, $cBlueL, $cBlueB);
imagestring($img2, 3, 280, 248, "Sistem Muat Antrean FIFO Berurutan Waktu Masuk", $cNavy);
drawDownArrow($img2, 450, 280, 320, $cNavy);

// 4. Barista Memilih Pesanan untuk Diproses
drawParallelogram($img2, 275, 320, 350, 50, 25, $cBlueL, $cBlueB);
imagestring($img2, 3, 305, 338, "Barista Klik 'Mulai Proses' pada Pesanan", $cNavy);
drawDownArrow($img2, 450, 370, 410, $cNavy);

// 5. Decision: Apakah Pesanan Urutan Pertama (Paling Awal)?
drawDiamond($img2, 450, 455, 320, 90, $cGoldL, $cGoldB);
imagestring($img2, 3, 320, 448, "Apakah Pesanan Terlama (FIFO Terdepan)?", $cNavy);

// Jika Menyerobot (TIDAK)
drawRightArrow($img2, 610, 700, 455, $cRedB, "TIDAK");
drawRoundedRect($img2, 700, 425, 885, 485, 8, $cRedL, $cRedB);
imagestring($img2, 2, 715, 440, "Blokir Aksi & Tampilkan", $cRedB);
imagestring($img2, 2, 715, 455, "Peringatan Wajib FIFO!", $cRedB);
// Kembali ke monitoring
imageline($img2, 792, 425, 792, 255, $cRedB);
drawRightArrow($img2, 792, 640, 255, $cRedB);

// Jika Valid Sesuai FIFO (YA)
drawDownArrow($img2, 450, 500, 550, $cGreen, "YA");

// 6. Ubah status 'diproses'
drawRoundedRect($img2, 250, 550, 650, 600, 8, $cGreenL, $cGreen);
imagestring($img2, 3, 270, 568, "Update status: 'diproses' & Kunci lockForUpdate", $cGreen);
drawDownArrow($img2, 450, 600, 640, $cNavy);

// 7. Barista Meracik Menu Selesai -> Klik 'Siap Diambil'
drawParallelogram($img2, 260, 640, 380, 50, 25, $cBlueL, $cBlueB);
imagestring($img2, 3, 290, 658, "Barista Klik 'Siap Diambil' (Bebas Urutan Siap)", $cNavy);
drawDownArrow($img2, 450, 690, 730, $cNavy);

// 8. Picu Alarm Suara & Notifikasi WA
drawRoundedRect($img2, 220, 730, 680, 785, 8, $cGoldL, $cGoldB);
imagestring($img2, 3, 240, 745, "Kirim Sinyal Audio: Ponsel Pelanggan Bunyikan Alarm!", $cNavy);
imagestring($img2, 3, 280, 762, "(Web Audio API Ringtone + Pesan WhatsApp)", $cNavy);
drawDownArrow($img2, 450, 785, 825, $cNavy);

// 9. Pelanggan Mengambil Sajian & Status 'Selesai'
drawRoundedRect($img2, 240, 825, 660, 875, 8, $cBlueL, $cBlueB);
imagestring($img2, 3, 260, 843, "Pelanggan Tunjukkan No. Antrean -> Status 'Selesai'", $cNavy);
drawDownArrow($img2, 450, 875, 905, $cGreen);

// 10. STOP
drawRoundedRect($img2, 385, 905, 515, 935, 15, $cGreenL, $cGreen);
imagestring($img2, 3, 435, 912, "SELESAI", $cGreen);

imagepng($img2, __DIR__ . '/diagrams/diagram_flowchart_barista.png');
imagedestroy($img2);

// -------------------------------------------------------------------------
// 3. DIAGRAM ENTITY RELATIONSHIP (ERD - CROW'S FOOT NOTATION)
// -------------------------------------------------------------------------
echo "3. Membuat Diagram Entity Relationship (ERD)...\n";
$w = 1000; $h = 750;
$img3 = createBaseImage($w, $h);

imagestring($img3, 5, 250, 20, "ENTITY RELATIONSHIP DIAGRAM (ERD) - BASIS DATA SEKARSA COFFEE", $cNavy);
imageline($img3, 50, 45, 950, 45, $cGreen);

// Helper Kotak Entitas ERD
function drawERDBox($img, $x, $y, $w, $tableName, $attrs, $cHeader, $cHeaderBg, $cBodyBg, $cBorder, $cText) {
    $rowH = 20;
    $totalH = 26 + (count($attrs) * $rowH);
    // Header
    imagefilledrectangle($img, $x, $y, $x + $w, $y + 26, $cHeaderBg);
    imagerectangle($img, $x, $y, $x + $w, $y + 26, $cBorder);
    imagestring($img, 4, $x + ($w / 2) - (strlen($tableName) * 4), $y + 5, $tableName, $cHeader);

    // Body
    imagefilledrectangle($img, $x, $y + 26, $x + $w, $y + $totalH, $cBodyBg);
    imagerectangle($img, $x, $y + 26, $x + $w, $y + $totalH, $cBorder);

    foreach ($attrs as $i => $attr) {
        $cy = $y + 28 + ($i * $rowH);
        imagestring($img, 2, $x + 10, $cy, $attr, $cText);
    }
}

// 1. users
drawERDBox($img3, 50, 80, 240, "users", [
    "PK id : bigint(20)",
    "   name : varchar(255)",
    "   username : varchar(255)",
    "   role : enum('admin','kasir')",
    "   password : varchar(255)",
    "   is_active : boolean"
], $cWhite, $cGreen, $cGreenL, $cGreen, $cNavy);

// 2. categories
drawERDBox($img3, 50, 300, 240, "categories", [
    "PK id : bigint(20)",
    "   name : varchar(255)",
    "   slug : varchar(255)",
    "   is_active : boolean",
    "   order : int(11)"
], $cWhite, $cBlueB, $cBlueL, $cBlueB, $cNavy);

// 3. menus
drawERDBox($img3, 380, 280, 250, "menus", [
    "PK id : bigint(20)",
    "FK category_id : bigint(20)",
    "   name : varchar(255)",
    "   price : decimal(12,2)",
    "   image : varchar(255)",
    "   is_available : boolean",
    "   description : text"
], $cWhite, $cBlueB, $cBlueL, $cBlueB, $cNavy);

// 4. orders
drawERDBox($img3, 380, 80, 250, "orders", [
    "PK id : bigint(20)",
    "   order_number : varchar(50)",
    "   queue_number : varchar(20)",
    "   table_number : varchar(20)",
    "   customer_name : varchar(255)",
    "   status : enum(5 status)",
    "   total_amount : decimal(12,2)",
    "   received_at : timestamp",
    "   handled_by : varchar(255)",
    "   completed_at : timestamp"
], $cWhite, $cGreen, $cGreenL, $cGreen, $cNavy);

// 5. order_details
drawERDBox($img3, 720, 180, 240, "order_details", [
    "PK id : bigint(20)",
    "FK order_id : bigint(20)",
    "FK menu_id : bigint(20)",
    "   menu_name : varchar(255)",
    "   price : decimal(12,2)",
    "   quantity : int(11)",
    "   subtotal : decimal(12,2)",
    "   notes : text"
], $cWhite, $cGoldB, $cGoldL, $cGoldB, $cNavy);

// 6. order_status_history
drawERDBox($img3, 50, 520, 240, "order_status_history", [
    "PK id : bigint(20)",
    "FK order_id : bigint(20)",
    "   status : varchar(50)",
    "   notes : text",
    "   created_at : timestamp"
], $cWhite, $cGrayB, $cBlueL, $cGrayB, $cNavy);

// 7. settings
drawERDBox($img3, 380, 540, 250, "settings", [
    "PK id : bigint(20)",
    "   key : varchar(255)",
    "   value : longtext",
    "   type : varchar(50)",
    "   description : text"
], $cWhite, $cGrayB, $cBlueL, $cGrayB, $cNavy);

// 8. notifications
drawERDBox($img3, 720, 520, 240, "notifications", [
    "PK id : bigint(20)",
    "FK order_id : bigint(20)",
    "   recipient : varchar(50)",
    "   channel : varchar(50)",
    "   status : varchar(50)",
    "   sent_at : timestamp"
], $cWhite, $cGrayB, $cBlueL, $cGrayB, $cNavy);

// Relasi Crow's Foot Garis
// categories -> menus (1:N)
imageline($img3, 290, 360, 380, 360, $cNavy);
imagestring($img3, 3, 320, 342, "1 : N", $cNavy);

// menus -> order_details (1:N)
imageline($img3, 630, 360, 675, 360, $cNavy);
imageline($img3, 675, 360, 675, 250, $cNavy);
imageline($img3, 675, 250, 720, 250, $cNavy);
imagestring($img3, 3, 650, 230, "1 : N", $cNavy);

// orders -> order_details (1:N)
imageline($img3, 630, 160, 720, 210, $cNavy);
imagestring($img3, 3, 655, 170, "1 : N", $cNavy);

// orders -> order_status_history (1:N)
imageline($img3, 380, 220, 340, 220, $cNavy);
imageline($img3, 340, 220, 340, 560, $cNavy);
imageline($img3, 340, 560, 290, 560, $cNavy);
imagestring($img3, 3, 305, 542, "1 : N", $cNavy);

// orders -> notifications (1:N)
imageline($img3, 505, 280, 505, 480, $cNavy);
imageline($img3, 505, 480, 840, 480, $cNavy);
imageline($img3, 840, 480, 840, 520, $cNavy);
imagestring($img3, 3, 650, 460, "1 : N", $cNavy);

imagepng($img3, __DIR__ . '/diagrams/diagram_erd.png');
imagedestroy($img3);

// -------------------------------------------------------------------------
// 4. DIAGRAM UML USE CASE
// -------------------------------------------------------------------------
echo "4. Membuat Diagram UML Use Case...\n";
$w = 950; $h = 800;
$img4 = createBaseImage($w, $h);

imagestring($img4, 5, 280, 20, "USE CASE DIAGRAM - SISTEM PEMESANAN FIFO SEKARSA COFFEE", $cNavy);
imageline($img4, 50, 45, 900, 45, $cGreen);

// Batas Sistem (System Boundary)
drawRoundedRect($img4, 230, 70, 720, 760, 15, $cWhite, $cNavy);
imagestring($img4, 4, 350, 80, "SISTEM SEKARSA COFFEE", $cGreen);

// Helper Aktor UML
function drawActor($img, $cx, $cy, $label, $color) {
    // Kepala
    imageellipse($img, $cx, $cy, 28, 28, $color);
    // Badan
    imageline($img, $cx, $cy + 14, $cx, $cy + 55, $color);
    imageline($img, $cx - 1, $cy + 14, $cx - 1, $cy + 55, $color);
    // Tangan
    imageline($img, $cx - 25, $cy + 28, $cx + 25, $cy + 28, $color);
    // Kaki
    imageline($img, $cx, $cy + 55, $cx - 20, $cy + 90, $color);
    imageline($img, $cx, $cy + 55, $cx + 20, $cy + 90, $color);
    // Label
    imagestring($img, 3, $cx - (strlen($label) * 4), $cy + 98, $label, $color);
}

// Aktor 1: Pelanggan (Kiri)
drawActor($img4, 110, 150, "Pelanggan", $cGreen);

// Aktor 2: Barista / Kasir (Kanan Atas)
drawActor($img4, 840, 260, "Kasir / Barista", $cBlueB);

// Aktor 3: Pemilik / Admin (Kanan Bawah)
drawActor($img4, 840, 580, "Owner / Admin", $cGoldB);

// Use Cases (Oval)
function drawUseCase($img, $cx, $cy, $w, $h, $label, $bgColor, $borderColor, $textColor) {
    imagefilledellipse($img, $cx, $cy, $w, $h, $bgColor);
    imageellipse($img, $cx, $cy, $w, $h, $borderColor);
    imagestring($img, 2, $cx - (strlen($label) * 3), $cy - 6, $label, $textColor);
}

// Pelanggan Use Cases
drawUseCase($img4, 470, 130, 280, 45, "UC-01: Pindai Kode QR Meja", $cGreenL, $cGreen, $cNavy);
drawUseCase($img4, 470, 190, 280, 45, "UC-02: Buka Katalog Menu Digital", $cGreenL, $cGreen, $cNavy);
drawUseCase($img4, 470, 250, 280, 45, "UC-03: Kirim Pesanan (Tiket FIFO)", $cGreenL, $cGreen, $cNavy);
drawUseCase($img4, 470, 310, 280, 45, "UC-04: Pantau Posisi Antrean Live", $cGreenL, $cGreen, $cNavy);
drawUseCase($img4, 470, 370, 280, 45, "UC-05: Terima Notifikasi Alarm Siap", $cGreenL, $cGreen, $cNavy);

// Barista Use Cases
drawUseCase($img4, 470, 440, 280, 45, "UC-06: Monitoring Pos Antrean FIFO", $cBlueL, $cBlueB, $cNavy);
drawUseCase($img4, 470, 500, 280, 45, "UC-07: Proses Antrean Terdepan (Strict)", $cBlueL, $cBlueB, $cNavy);
drawUseCase($img4, 470, 560, 280, 45, "UC-08: Tandai Siap & Picu Alarm HP", $cBlueL, $cBlueB, $cNavy);

// Admin Use Cases
drawUseCase($img4, 470, 630, 280, 45, "UC-09: Kelola Menu & Ketersediaan Stok", $cGoldL, $cGoldB, $cNavy);
drawUseCase($img4, 470, 690, 280, 45, "UC-10: Cetak Standee QR Meja Kafe", $cGoldL, $cGoldB, $cNavy);
drawUseCase($img4, 470, 740, 280, 40, "UC-11: Laporan Omset & Evaluasi FIFO", $cGoldL, $cGoldB, $cNavy);

// Garis Asosiasi Pelanggan
imageline($img4, 140, 170, 330, 130, $cGreen);
imageline($img4, 140, 170, 330, 190, $cGreen);
imageline($img4, 140, 170, 330, 250, $cGreen);
imageline($img4, 140, 170, 330, 310, $cGreen);
imageline($img4, 140, 170, 330, 370, $cGreen);

// Garis Asosiasi Barista
imageline($img4, 800, 280, 610, 440, $cBlueB);
imageline($img4, 800, 280, 610, 500, $cBlueB);
imageline($img4, 800, 280, 610, 560, $cBlueB);

// Garis Asosiasi Admin
imageline($img4, 800, 600, 610, 630, $cGoldB);
imageline($img4, 800, 600, 610, 690, $cGoldB);
imageline($img4, 800, 600, 610, 740, $cGoldB);

imagepng($img4, __DIR__ . '/diagrams/diagram_usecase.png');
imagedestroy($img4);

// -------------------------------------------------------------------------
// 5. DIAGRAM UML ACTIVITY (SWIMLANE 3 PARTISI)
// -------------------------------------------------------------------------
echo "5. Membuat Diagram UML Activity (Swimlane)...\n";
$w = 950; $h = 850;
$img5 = createBaseImage($w, $h);

imagestring($img5, 5, 230, 20, "ACTIVITY DIAGRAM ALUR PEMESANAN FIFO SEKARSA COFFEE", $cNavy);
imageline($img5, 50, 45, 900, 45, $cGreen);

// Partisi / Swimlane
$colW = 280;
// Kolom 1: Pelanggan
drawRoundedRect($img5, 50, 60, 330, 820, 8, $cWhite, $cGreen);
imagefilledrectangle($img5, 50, 60, 330, 95, $cGreen);
imagestring($img5, 4, 130, 70, "PELANGGAN", $cWhite);

// Kolom 2: Server / Sistem
drawRoundedRect($img5, 340, 60, 620, 820, 8, $cWhite, $cBlueB);
imagefilledrectangle($img5, 340, 60, 620, 95, $cBlueB);
imagestring($img5, 4, 400, 70, "SISTEM / SERVER", $cWhite);

// Kolom 3: Kasir / Barista
drawRoundedRect($img5, 630, 60, 910, 820, 8, $cWhite, $cGoldB);
imagefilledrectangle($img5, 630, 60, 910, 95, $cGoldB);
imagestring($img5, 4, 700, 70, "KASIR / BARISTA", $cWhite);

// 1. Initial State (Titik Hitam Bulat)
imagefilledellipse($img5, 190, 125, 22, 22, $cNavy);
drawDownArrow($img5, 190, 136, 170, $cNavy);

// 2. Scan QR
drawRoundedRect($img5, 75, 170, 305, 210, 8, $cGreenL, $cGreen);
imagestring($img5, 2, 95, 185, "Pindai QR Akrilik di Meja", $cNavy);
drawRightArrow($img5, 305, 410, 190, $cNavy);

// 3. Validasi Token QR (Sistem)
drawRoundedRect($img5, 365, 170, 595, 210, 8, $cBlueL, $cBlueB);
imagestring($img5, 2, 385, 185, "Validasi Token & Terbitkan Sesi", $cNavy);
drawDownArrow($img5, 480, 210, 250, $cNavy);

// 4. Buka Katalog Menu
drawRoundedRect($img5, 365, 250, 595, 290, 8, $cBlueL, $cBlueB);
imagestring($img5, 2, 395, 265, "Kirim Tampilan Katalog Menu", $cNavy);
imageline($img5, 365, 270, 190, 270, $cNavy);
drawDownArrow($img5, 190, 270, 320, $cNavy);

// 5. Pelanggan Memilih Menu & Kirim
drawRoundedRect($img5, 75, 320, 305, 365, 8, $cGreenL, $cGreen);
imagestring($img5, 2, 90, 335, "Pilih Menu & Klik 'Kirim Pesanan'", $cNavy);
drawRightArrow($img5, 305, 410, 342, $cNavy);

// 6. Eksekusi Antrean FIFO
drawRoundedRect($img5, 365, 320, 595, 375, 8, $cBlueL, $cBlueB);
imagestring($img5, 2, 380, 335, "Catat received_at, Generate No. FIFO", $cNavy);
imagestring($img5, 2, 395, 350, "Hanguskan Token Sesi QR", $cNavy);

// Cabang ke Kasir & Pelanggan
drawRightArrow($img5, 595, 680, 345, $cNavy);
drawRoundedRect($img5, 680, 320, 890, 365, 8, $cGoldL, $cGoldB);
imagestring($img5, 2, 700, 335, "Antrean Masuk ke Pos Barista", $cNavy);

// Kasir Proses Sesuai FIFO
drawDownArrow($img5, 785, 365, 420, $cNavy);
drawRoundedRect($img5, 660, 420, 890, 470, 8, $cGoldL, $cGoldB);
imagestring($img5, 2, 675, 435, "Klik 'Proses' Urutan Terdepan (FIFO)", $cNavy);
imagestring($img5, 2, 700, 450, "& Racik Pesanan", $cNavy);

// Kasir Klik Siap Diambil
drawDownArrow($img5, 785, 470, 520, $cNavy);
drawRoundedRect($img5, 660, 520, 890, 565, 8, $cGoldL, $cGoldB);
imagestring($img5, 2, 690, 535, "Ubah Status 'Siap Diambil'", $cNavy);

// Kirim Sinyal Alarm ke Sistem lalu ke Pelanggan
imageline($img5, 660, 542, 480, 542, $cNavy);
drawDownArrow($img5, 480, 542, 600, $cNavy);
drawRoundedRect($img5, 365, 600, 595, 645, 8, $cBlueL, $cBlueB);
imagestring($img5, 2, 385, 615, "Picu Web Audio API & Notif WA", $cNavy);

imageline($img5, 365, 622, 190, 622, $cNavy);
drawDownArrow($img5, 190, 622, 680, $cNavy);

// HP Pelanggan Berdering Keras
drawRoundedRect($img5, 75, 680, 305, 730, 8, $cGreenL, $cGreen);
imagestring($img5, 2, 90, 695, "HP Berdering Keras & Ambil Pesanan", $cNavy);

// Final State
drawDownArrow($img5, 190, 730, 775, $cNavy);
imagefilledellipse($img5, 190, 790, 26, 26, $cNavy);
imagefilledellipse($img5, 190, 790, 18, 18, $cWhite);
imagefilledellipse($img5, 190, 790, 12, 12, $cNavy);
imagestring($img5, 3, 155, 805, "SELESAI", $cGreen);

imagepng($img5, __DIR__ . '/diagrams/diagram_activity.png');
imagedestroy($img5);

// -------------------------------------------------------------------------
// 6. DIAGRAM UML SEQUENCE (INTERAKSI WAKTU PEMESANAN & ALARM FIFO)
// -------------------------------------------------------------------------
echo "6. Membuat Diagram UML Sequence...\n";
$w = 950; $h = 750;
$img6 = createBaseImage($w, $h);

imagestring($img6, 5, 200, 20, "SEQUENCE DIAGRAM - TRANSAKSI PEMESANAN & ALARM FIFO", $cNavy);
imageline($img6, 50, 45, 900, 45, $cGreen);

// Entitas Lifeline
$lifelines = [
    ["Pelanggan (HP)", 100, $cGreenL, $cGreen],
    ["CustomerOrderCtrl", 300, $cBlueL, $cBlueB],
    ["FifoQueueService", 500, $cBlueL, $cBlueB],
    ["Database (MySQL)", 700, $cGoldL, $cGoldB],
    ["Dashboard Barista", 870, $cRedL, $cRedB]
];

foreach ($lifelines as $lf) {
    drawRoundedRect($img6, $lf[1] - 65, 60, $lf[1] + 65, 95, 6, $lf[2], $lf[3]);
    imagestring($img6, 2, $lf[1] - (strlen($lf[0]) * 3), 72, $lf[0], $cNavy);
    // Garis putus-putus lifeline
    for ($y = 100; $y < 700; $y += 12) {
        imageline($img6, $lf[1], $y, $lf[1], $y + 6, $cGrayB);
    }
}

// 1. Scan QR
drawRightArrow($img6, 100, 300, 130, $cNavy, "1. GET /pesan?token=XX&meja=05");
drawRightArrow($img6, 300, 100, 165, $cNavy, "2. 200 OK (Render Menu & Simpan Sesi)");

// 2. Submit Pesanan
drawRightArrow($img6, 100, 300, 210, $cNavy, "3. POST /pesan (Cart Items, Meja, Nama)");
drawRightArrow($img6, 300, 500, 250, $cNavy, "4. processNewOrder(data)");
drawRightArrow($img6, 500, 700, 290, $cNavy, "5. DB::transaction (lock & insert orders)");
drawRightArrow($img6, 700, 500, 330, $cNavy, "6. Commit & Return Order (No. Antrean 001)");
drawRightArrow($img6, 500, 300, 370, $cNavy, "7. Return Queue Info");
drawRightArrow($img6, 300, 100, 410, $cNavy, "8. Redirect /pesanan/{order} + Hapus Sesi QR");

// 3. Barista Monitoring & Proses
drawRightArrow($img6, 870, 700, 460, $cNavy, "9. GET /pegawai/dashboard/orders (Polling FIFO)");
drawRightArrow($img6, 870, 300, 510, $cBlueB, "10. POST /pegawai/orders/{id}/process (Urutan #1)");
drawRightArrow($img6, 300, 700, 545, $cBlueB, "11. Update status='diproses', handled_by");

// 4. Barista Ubah Status Siap & Picu Alarm
drawRightArrow($img6, 870, 300, 595, $cGreen, "12. POST /pegawai/orders/{id}/ready");
drawRightArrow($img6, 300, 700, 625, $cGreen, "13. Update status='siap_diambil'");
drawRightArrow($img6, 700, 100, 665, $cRedB, "14. Polling Live: Status='siap' -> Bunyikan Web Audio Alarm!");

imagepng($img6, __DIR__ . '/diagrams/diagram_sequence.png');
imagedestroy($img6);

// -------------------------------------------------------------------------
// 7. DIAGRAM UML CLASS (ARSITEKTUR MODEL & SERVICE LAYER)
// -------------------------------------------------------------------------
echo "7. Membuat Diagram UML Class...\n";
$w = 1000; $h = 700;
$img7 = createBaseImage($w, $h);

imagestring($img7, 5, 230, 20, "CLASS DIAGRAM - ARSITEKTUR MVC & SERVICE LAYER FIFO", $cNavy);
imageline($img7, 50, 45, 950, 45, $cGreen);

// Helper Class Box (3 Kompartemen Standar UML)
function drawClassBox($img, $x, $y, $w, $className, $props, $methods, $cHeaderBg, $cHeader, $cBodyBg, $cBorder, $cText) {
    $rowH = 16;
    $headH = 24;
    $propsH = count($props) * $rowH + 6;
    $methsH = count($methods) * $rowH + 6;
    $totalH = $headH + $propsH + $methsH;

    // Header Kompartemen 1: Nama Class
    imagefilledrectangle($img, $x, $y, $x + $w, $y + $headH, $cHeaderBg);
    imagerectangle($img, $x, $y, $x + $w, $y + $headH, $cBorder);
    imagestring($img, 3, $x + ($w / 2) - (strlen($className) * 4), $y + 4, $className, $cHeader);

    // Kompartemen 2: Atribut / Properti
    imagefilledrectangle($img, $x, $y + $headH, $x + $w, $y + $headH + $propsH, $cBodyBg);
    imagerectangle($img, $x, $y + $headH, $x + $w, $y + $headH + $propsH, $cBorder);
    foreach ($props as $i => $p) {
        imagestring($img, 2, $x + 8, $y + $headH + 4 + ($i * $rowH), $p, $cText);
    }

    // Kompartemen 3: Method / Operasi
    $methY = $y + $headH + $propsH;
    imagefilledrectangle($img, $x, $methY, $x + $w, $y + $totalH, $cBodyBg);
    imagerectangle($img, $x, $methY, $x + $w, $y + $totalH, $cBorder);
    foreach ($methods as $i => $m) {
        imagestring($img, 2, $x + 8, $methY + 4 + ($i * $rowH), $m, $cText);
    }
}

// 1. FifoQueueService
drawClassBox($img7, 40, 80, 280, "FifoQueueService", [
    "- orderModel : Order",
    "- settingModel : Setting"
], [
    "+ getActiveFifoQueue() : Collection",
    "+ generateQueueNumber() : string",
    "+ getQueueInfo(Order) : array",
    "+ canProcessOrder(Order) : bool",
    "+ calculateEstimatedTime(int) : int"
], $cGreen, $cWhite, $cGreenL, $cGreen, $cNavy);

// 2. CustomerOrderController
drawClassBox($img7, 360, 80, 280, "CustomerOrderController", [
    "- fifoService : FifoQueueService",
    "- notifService : NotificationService"
], [
    "+ index(Request) : View",
    "+ store(OrderRequest) : Redirect",
    "+ confirmation(Order) : View",
    "+ track(Order) : View",
    "+ getLiveStatus(Order) : JsonResponse"
], $cBlueB, $cWhite, $cBlueL, $cBlueB, $cNavy);

// 3. PegawaiDashboardController
drawClassBox($img7, 680, 80, 280, "PegawaiDashboardController", [
    "- fifoService : FifoQueueService"
], [
    "+ index() : View",
    "+ getActiveOrders() : JsonResponse",
    "+ processOrder(Request, int) : Json",
    "+ readyOrder(Request, int) : Json",
    "+ completeOrder(Request, int) : Json"
], $cGoldB, $cWhite, $cGoldL, $cGoldB, $cNavy);

// 4. Model Order
drawClassBox($img7, 40, 360, 280, "Order (Model)", [
    "+ id : int",
    "+ order_number : string",
    "+ queue_number : string",
    "+ table_number : string",
    "+ customer_name : string",
    "+ status : string",
    "+ received_at : Carbon",
    "+ handled_by : string"
], [
    "+ details() : HasMany",
    "+ statusHistory() : HasMany",
    "+ scopeActiveFifo() : Builder",
    "+ getStatusLabelAttribute() : string"
], $cGreen, $cWhite, $cGreenL, $cGreen, $cNavy);

// 5. Model OrderDetail
drawClassBox($img7, 360, 360, 280, "OrderDetail (Model)", [
    "+ id : int",
    "+ order_id : int",
    "+ menu_id : int",
    "+ menu_name : string",
    "+ price : float",
    "+ quantity : int",
    "+ subtotal : float"
], [
    "+ order() : BelongsTo",
    "+ menu() : BelongsTo"
], $cBlueB, $cWhite, $cBlueL, $cBlueB, $cNavy);

// 6. Model Menu
drawClassBox($img7, 680, 360, 280, "Menu (Model)", [
    "+ id : int",
    "+ category_id : int",
    "+ name : string",
    "+ price : float",
    "+ image : string",
    "+ is_available : bool"
], [
    "+ category() : BelongsTo",
    "+ orderDetails() : HasMany",
    "+ getImageUrlAttribute() : string"
], $cBlueB, $cWhite, $cBlueL, $cBlueB, $cNavy);

// Garis Asosiasi Antar Class
// FifoQueueService <-> Order
imageline($img7, 180, 230, 180, 360, $cNavy);
imagestring($img7, 3, 190, 290, "manages", $cNavy);

// CustomerOrderController -> FifoQueueService
imageline($img7, 360, 140, 320, 140, $cNavy);
imagestring($img7, 3, 330, 122, "uses", $cNavy);

// PegawaiDashboardController -> FifoQueueService
imageline($img7, 680, 140, 640, 140, $cNavy);

// Order -> OrderDetail (1 to Many)
imageline($img7, 320, 420, 360, 420, $cNavy);
imagestring($img7, 3, 330, 402, "1 : *", $cNavy);

// OrderDetail -> Menu
imageline($img7, 640, 420, 680, 420, $cNavy);
imagestring($img7, 3, 650, 402, "* : 1", $cNavy);

imagepng($img7, __DIR__ . '/diagrams/diagram_class.png');
imagedestroy($img7);

echo "Semua 7 gambar diagram visual berstandar akademik BERHASIL dibuat di folder diagrams/!\n";
