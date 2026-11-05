<?php

require_once __DIR__ . '/generate_diagram_images.php';

echo "Membuat Diagram ASI (Bagan Alir Dokumen Flowmap)...\n";

$w = 1000; $h = 700;
$img = createBaseImage($w, $h);

$cNavy   = imagecolorallocate($img, 15, 23, 42);
$cGreen  = imagecolorallocate($img, 22, 101, 52);
$cGreenL = imagecolorallocate($img, 240, 253, 244);
$cBlueL  = imagecolorallocate($img, 239, 246, 255);
$cBlueB  = imagecolorallocate($img, 37, 99, 235);
$cRedL   = imagecolorallocate($img, 254, 242, 242);
$cRedB   = imagecolorallocate($img, 220, 38, 38);
$cWhite  = imagecolorallocate($img, 255, 255, 255);
$cGrayB  = imagecolorallocate($img, 148, 163, 184);

imagestring($img, 5, 180, 20, "BAGAN ALIR DOKUMEN / ASI (SISTEM BERJALAN VS SISTEM DIUSULKAN)", $cNavy);
imageline($img, 50, 45, 950, 45, $cGreen);

// Kolom 1: Sistem Berjalan (Kiri)
drawRoundedRect($img, 50, 60, 480, 660, 10, $cWhite, $cRedB);
imagefilledrectangle($img, 50, 60, 480, 95, $cRedB);
imagestring($img, 4, 110, 70, "SISTEM BERJALAN (KONVENSIONAL / MANUAL)", $cWhite);

// Kolom 2: Sistem Diusulkan (Kanan)
drawRoundedRect($img, 510, 60, 950, 660, 10, $cWhite, $cGreen);
imagefilledrectangle($img, 510, 60, 950, 95, $cGreen);
imagestring($img, 4, 570, 70, "SISTEM DIUSULKAN (DIGITAL BERBASIS FIFO)", $cWhite);

$steps = [
    [
        "1. Pemilihan Menu",
        "Pelanggan antre berdiri di kasir membaca menu cetak",
        "Pelanggan duduk di meja & scan QR Akrilik Meja"
    ],
    [
        "2. Pencatatan Pesanan",
        "Kasir mencatat nota manual rangkap 2 (rawan salah tulis)",
        "Pelanggan kirim pesanan dari HP (timestamp presisi ms)"
    ],
    [
        "3. Penentuan Urutan",
        "Nota kertas bertumpuk acak (rawan diserobot/tertukar)",
        "Algoritma FIFO mengunci urutan masuk server secara otomatis"
    ],
    [
        "4. Pelacakan Antrean",
        "Pelanggan cemas tidak tahu sisa waktu & urutan",
        "Live Tracking di HP: menampilkan sisa antrean & menit"
    ],
    [
        "5. Panggilan Pengambilan",
        "Barista teriak panggil nama di kafe (sering tak dengar)",
        "Alarm Suara Nyaring berdering di HP pelanggan + WA"
    ],
    [
        "6. Evaluasi & Laporan",
        "Owner rekap nota sobekan fisik tiap tutup kasir",
        "Sistem cetak laporan metrik omset & durasi FIFO otomatis"
    ]
];

$y = 110;
foreach ($steps as $st) {
    // Label Langkah
    drawRoundedRect($img, 65, $y, 465, $y + 70, 6, $cRedL, $cRedB);
    imagestring($img, 3, 75, $y + 8, $st[0], $cRedB);
    imagestring($img, 2, 75, $y + 28, substr($st[1], 0, 52), $cNavy);
    imagestring($img, 2, 75, $y + 44, substr($st[1], 52), $cNavy);

    drawRoundedRect($img, 525, $y, 935, $y + 70, 6, $cGreenL, $cGreen);
    imagestring($img, 3, 535, $y + 8, $st[0], $cGreen);
    imagestring($img, 2, 535, $y + 28, substr($st[2], 0, 52), $cNavy);
    imagestring($img, 2, 535, $y + 44, substr($st[2], 52), $cNavy);

    // Panah alur turun
    if ($y < 500) {
        drawDownArrow($img, 265, $y + 70, $y + 90, $cRedB);
        drawDownArrow($img, 730, $y + 70, $y + 90, $cGreen);
    }
    $y += 90;
}

imagepng($img, __DIR__ . '/diagrams/diagram_asi.png');
imagedestroy($img);
echo "Diagram ASI berhasil dibuat!\n";
