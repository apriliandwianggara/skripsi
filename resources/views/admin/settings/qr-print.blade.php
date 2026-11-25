<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak & Unduh Standee QR Code Meja - {{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- html2canvas untuk Unduh Gambar Otomatis -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- SweetAlert2 untuk notifikasi unduh sukses -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        .font-serif-title {
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* Mode Standar (A4 Penuh) */
        .layout-a4-full {
            width: 175mm;
            min-height: 250mm;
            max-width: 100%;
        }

        /* Mode Akrilik Meja (A5) */
        .layout-a5-acrylic {
            width: 135mm;
            min-height: 195mm;
        }

        /* Mode Hemat Kertas (2 per Halaman) */
        .layout-2up {
            width: 120mm;
            min-height: 170mm;
        }

        /* Mode Tent Card Lipat Berdiri */
        .layout-tent-card {
            width: 100%;
            max-width: 270mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            @page {
                size: auto;
                margin: 4mm 6mm;
            }

            .print-container {
                padding: 0 !important;
                margin: 0 auto !important;
                box-shadow: none !important;
            }

            .standee-card {
                box-shadow: none !important;
                border: 1.5px solid #064e3b !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

    <!-- ================= TOOLBAR KONTROL CETAK & UNDUH (NO-PRINT) ================= -->
    <header class="no-print bg-white border-b border-slate-200 sticky top-0 z-50 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
            
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-800 text-white flex items-center justify-center font-bold text-lg shadow">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 font-serif-title">Standee QR Code Meja</h1>
                    <p class="text-[11px] text-slate-500">Pilih format lalu klik <strong>Unduh Gambar</strong> atau Cetak</p>
                </div>
            </div>

            <!-- Pilihan Format Tampilan / Desain -->
            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="text-xs font-semibold text-slate-600 hidden md:inline">Format:</span>
                
                <button type="button" onclick="setLayout('a4-full')" id="btn-a4-full"
                        class="layout-btn px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 bg-emerald-700 text-white shadow-sm">
                    <i class="fa-regular fa-file"></i>
                    <span>A4 Penuh</span>
                </button>

                <button type="button" onclick="setLayout('a5-acrylic')" id="btn-a5-acrylic"
                        class="layout-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200">
                    <i class="fa-solid fa-border-all"></i>
                    <span>Akrilik Meja (A5)</span>
                </button>

                <button type="button" onclick="setLayout('tent-card')" id="btn-tent-card"
                        class="layout-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200">
                    <i class="fa-solid fa-tent"></i>
                    <span>Tent Card (Lipat 2 Sisi)</span>
                </button>

                <button type="button" onclick="setLayout('2up')" id="btn-2up"
                        class="layout-btn px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200">
                    <i class="fa-solid fa-columns"></i>
                    <span>2 Kartu per A4</span>
                </button>
            </div>

            <!-- Input Ubah Meja & Tombol Aksi Utama -->
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <input type="text" id="inputTableNumber" value="{{ $tableNumber ?? '' }}"
                           placeholder="No. Meja (misal: 01)"
                           oninput="updateTableBadge(this.value)"
                           class="w-32 px-2.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white"
                           title="Ketik nomor meja untuk mengubah nomor meja di kartu secara langsung">
                </div>

                <!-- Tombol Utama: UNDUH GAMBAR (PNG HD) -->
                <button type="button" onclick="downloadAsImage()" id="btnDownloadImage"
                        class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 shadow-md shadow-emerald-700/30 cursor-pointer">
                    <i class="fa-solid fa-download text-sm"></i>
                    <span>Unduh Gambar (PNG)</span>
                </button>

                <!-- Tombol Sekunder: Cetak Langsung -->
                <button type="button" onclick="window.print()"
                        class="px-3.5 py-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak</span>
                </button>
            </div>

        </div>

        <!-- Baris Info Status & Tips Unduh -->
        <div class="bg-emerald-50/90 border-t border-emerald-100 px-4 py-1.5 text-center text-[11px] text-emerald-900 flex items-center justify-center gap-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900">
                <i class="fa-solid fa-bolt mr-1"></i> Format Gambar HD
            </span>
            <span>Klik tombol <strong>"Unduh Gambar (PNG)"</strong> untuk langsung menyimpan gambar standee beresolusi tinggi ke HP / Komputer Anda tanpa melalui dialog PDF browser.</span>
        </div>
    </header>


    <!-- ================= AREA KERTAS DOKUMEN CETAK & TANGKAPAN GAMBAR ================= -->
    <main class="print-container py-6 px-4 flex flex-col items-center justify-center">

        <!-- ================= MODE 1: A4 PENUH / STAND BESAR ================= -->
        <div id="view-a4-full" class="print-view w-full flex justify-center">
            <div class="standee-card layout-a4-full bg-white rounded-3xl shadow-xl border-2 border-emerald-900/15 overflow-hidden flex flex-col text-center transition-all">
                
                <!-- Header Atas Kafe (Hijau Mewah & Logo) -->
                <div class="w-full relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-950 text-white pt-9 pb-7 px-8">
                    <!-- Foto Banner Latar Belakang -->
                    <img src="{{ $setting->banner_url }}" crossorigin="anonymous" alt="Sekarsa Cafe" class="absolute inset-0 w-full h-full object-cover object-center filter brightness-[0.35] opacity-50">
                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-950/90 via-emerald-900/80 to-emerald-950/95"></div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-20 h-20 bg-white p-1.5 rounded-2xl shadow-2xl mx-auto mb-3.5 border-2 border-white/60 overflow-hidden flex items-center justify-center">
                            <img src="{{ $setting->logo_url }}" crossorigin="anonymous" alt="Sekarsa Logo" class="w-full h-full object-contain">
                        </div>
                        <h2 class="font-bold text-3xl font-serif-title tracking-wider text-white drop-shadow-md">
                            {{ $setting->cafe_name ?? 'SEKARSA COFFEE' }}
                        </h2>
                        <p class="text-xs text-emerald-200 mt-1 tracking-wider uppercase drop-shadow font-medium">
                            {{ $setting->cafe_tagline ?? 'Warmth, Taste & Harmony in Every Sip' }}
                        </p>

                        <!-- Badge Nomor Meja -->
                        <div id="badge-a4-full" class="table-badge mt-3.5 inline-block bg-amber-400 text-slate-950 text-xs font-black px-5 py-1.5 rounded-full uppercase tracking-widest shadow-lg border border-amber-300">
                            {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA COFFEE' }}
                        </div>
                    </div>
                </div>

                <!-- Bagian Konten QR Code Utama -->
                <div class="p-8 flex-1 flex flex-col items-center justify-center bg-white space-y-5">
                    <div>
                        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-900 text-[11px] font-extrabold uppercase tracking-widest rounded-full mb-1">
                            PANDUAN PEMESANAN PRAKTIS
                        </span>
                        <h3 class="text-xl font-extrabold text-slate-900 tracking-tight font-serif-title mt-1">
                            Pindai QR Code untuk Memesan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5 max-w-sm mx-auto">
                            Buka kamera ponsel Anda, arahkan ke kode QR di bawah untuk melihat menu dan memesan langsung dari meja.
                        </p>
                    </div>

                    <!-- QR Code Besar Resolusi Tajam -->
                    <div class="p-5 bg-gradient-to-br from-emerald-50 via-white to-emerald-50 rounded-3xl border-2 border-emerald-300 shadow-inner flex items-center justify-center">
                        <div class="w-64 h-64 bg-white p-3 rounded-2xl shadow-md overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:block">
                            {!! $qrSvg !!}
                        </div>
                    </div>

                    <!-- 3 Langkah Sederhana Pelanggan -->
                    <div class="w-full max-w-md grid grid-cols-3 gap-3 pt-3 border-t border-slate-200">
                        <div class="text-center p-2 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-emerald-800 text-white font-bold text-xs inline-flex items-center justify-center mb-1 shadow-sm">1</span>
                            <p class="font-bold text-xs text-slate-800">Scan QR</p>
                            <p class="text-[10px] text-slate-500">Pilih sajian menu</p>
                        </div>
                        <div class="text-center p-2 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-emerald-800 text-white font-bold text-xs inline-flex items-center justify-center mb-1 shadow-sm">2</span>
                            <p class="font-bold text-xs text-slate-800">Bayar Online</p>
                            <p class="text-[10px] text-slate-500">QRIS / E-Wallet / Kasir</p>
                        </div>
                        <div class="text-center p-2 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-emerald-800 text-white font-bold text-xs inline-flex items-center justify-center mb-1 shadow-sm">3</span>
                            <p class="font-bold text-xs text-slate-800">Santai Sejenak</p>
                            <p class="text-[10px] text-slate-500">Sajian siap diantar</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Card -->
                <div class="w-full bg-emerald-950 text-white py-3.5 px-6 text-xs text-emerald-200 flex items-center justify-between border-t border-emerald-900">
                    <span class="flex items-center gap-1.5">
                        <i class="fa-solid fa-wifi text-amber-400"></i>
                        <span>Free Wi-Fi: <strong>Sekarsa-Guest</strong></span>
                    </span>
                    <span class="font-semibold text-emerald-300">
                        Sekarsa Coffee &bull; Smart Dine-In
                    </span>
                </div>

            </div>
        </div>


        <!-- ================= MODE 2: UKURAN STANDAR MEJA AKRILIK (A5) ================= -->
        <div id="view-a5-acrylic" class="print-view w-full hidden justify-center">
            <div class="standee-card layout-a5-acrylic bg-white rounded-3xl shadow-xl border-2 border-emerald-900/15 overflow-hidden flex flex-col text-center">
                
                <!-- Header A5 -->
                <div class="w-full relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-emerald-950 text-white pt-6 pb-5 px-6">
                    <img src="{{ $setting->banner_url }}" crossorigin="anonymous" alt="Sekarsa Cafe" class="absolute inset-0 w-full h-full object-cover object-center filter brightness-[0.35] opacity-50">
                    <div class="absolute inset-0 bg-gradient-to-b from-emerald-950/90 via-emerald-900/80 to-emerald-950/95"></div>

                    <div class="relative z-10 flex flex-col items-center">
                        <div class="w-14 h-14 bg-white p-1 rounded-2xl shadow-xl mx-auto mb-2.5 border-2 border-white/60 overflow-hidden flex items-center justify-center">
                            <img src="{{ $setting->logo_url }}" crossorigin="anonymous" alt="Sekarsa Logo" class="w-full h-full object-contain">
                        </div>
                        <h2 class="font-bold text-2xl font-serif-title tracking-wider text-white drop-shadow-md">
                            {{ $setting->cafe_name ?? 'SEKARSA COFFEE' }}
                        </h2>
                        <p class="text-[10px] text-emerald-200 tracking-wider uppercase font-medium">
                            {{ $setting->cafe_tagline ?? 'Warmth, Taste & Harmony in Every Sip' }}
                        </p>

                        <!-- Badge Meja A5 -->
                        <div id="badge-a5-acrylic" class="table-badge mt-2.5 inline-block bg-amber-400 text-slate-950 text-[11px] font-black px-4 py-1 rounded-full uppercase tracking-widest shadow-md border border-amber-300">
                            {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA COFFEE' }}
                        </div>
                    </div>
                </div>

                <!-- Konten QR A5 -->
                <div class="p-5 flex-1 flex flex-col items-center justify-center bg-white space-y-3.5">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight font-serif-title">
                            Pindai untuk Memesan
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Arahkan kamera HP ke QR Code untuk pesan langsung
                        </p>
                    </div>

                    <div class="p-3.5 bg-emerald-50 rounded-2xl border-2 border-emerald-300 shadow-inner flex items-center justify-center">
                        <div class="w-48 h-48 bg-white p-2.5 rounded-xl shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:block">
                            {!! $qrSvg !!}
                        </div>
                    </div>

                    <!-- 3 Langkah A5 -->
                    <div class="w-full grid grid-cols-3 gap-2 pt-2 border-t border-slate-200 text-[10px]">
                        <div class="text-center">
                            <span class="w-5 h-5 rounded-full bg-emerald-800 text-white font-bold inline-flex items-center justify-center mb-0.5">1</span>
                            <p class="font-bold text-slate-800">Scan QR</p>
                        </div>
                        <div class="text-center">
                            <span class="w-5 h-5 rounded-full bg-emerald-800 text-white font-bold inline-flex items-center justify-center mb-0.5">2</span>
                            <p class="font-bold text-slate-800">Order & Bayar</p>
                        </div>
                        <div class="text-center">
                            <span class="w-5 h-5 rounded-full bg-emerald-800 text-white font-bold inline-flex items-center justify-center mb-0.5">3</span>
                            <p class="font-bold text-slate-800">Sajian Diantar</p>
                        </div>
                    </div>
                </div>

                <!-- Footer A5 -->
                <div class="w-full bg-emerald-950 text-white py-2.5 px-4 text-[10px] text-emerald-200 flex items-center justify-between">
                    <span>&bull; Kode QR Meja</span>
                    <span>Sekarsa Coffee &bull;</span>
                </div>

            </div>
        </div>


        <!-- ================= MODE 3: TENT CARD MEJA LIPAT 2 SISI ================= -->
        <div id="view-tent-card" class="print-view w-full hidden justify-center">
            <div class="layout-tent-card bg-white p-4 rounded-3xl shadow-xl border-2 border-slate-300 flex flex-col items-center">
                
                <div class="w-full text-center text-xs font-bold text-slate-400 pb-2 border-b-2 border-dashed border-slate-300">
                    <i class="fa-solid fa-scissors mr-1"></i> POTONG DI SINI / GARIS TEPI KERTAS
                </div>

                <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-6 my-4">
                    
                    <!-- SISI DEPAN -->
                    <div class="standee-card bg-white rounded-3xl border-2 border-emerald-900/15 overflow-hidden flex flex-col text-center">
                        <div class="bg-emerald-900 text-white pt-6 pb-4 px-4 text-center">
                            <div class="w-12 h-12 bg-white p-1 rounded-xl shadow-lg mx-auto mb-2 flex items-center justify-center">
                                <img src="{{ $setting->logo_url }}" crossorigin="anonymous" alt="Sekarsa Logo" class="w-full h-full object-contain">
                            </div>
                            <h3 class="font-bold text-lg font-serif-title">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h3>
                            <div id="badge-tent-front" class="table-badge mt-2 inline-block bg-amber-400 text-slate-950 text-[10px] font-black px-3 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA' }}
                            </div>
                        </div>

                        <div class="p-4 flex flex-col items-center justify-center space-y-2">
                            <p class="text-xs font-extrabold text-emerald-900 uppercase">Pindai untuk Memesan</p>
                            <div class="w-40 h-40 bg-emerald-50 p-2 rounded-xl border border-emerald-200 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                                {!! $qrSvg !!}
                            </div>
                            <p class="text-[10px] text-slate-500">Scan dengan kamera HP untuk memilih sajian</p>
                        </div>
                        <div class="bg-emerald-950 text-white py-1.5 px-3 text-[9px] text-center">
                            SISI DEPAN (FRONT)
                        </div>
                    </div>

                    <!-- SISI BELAKANG -->
                    <div class="standee-card bg-white rounded-3xl border-2 border-emerald-900/15 overflow-hidden flex flex-col text-center">
                        <div class="bg-emerald-900 text-white pt-6 pb-4 px-4 text-center">
                            <div class="w-12 h-12 bg-white p-1 rounded-xl shadow-lg mx-auto mb-2 flex items-center justify-center">
                                <img src="{{ $setting->logo_url }}" crossorigin="anonymous" alt="Sekarsa Logo" class="w-full h-full object-contain">
                            </div>
                            <h3 class="font-bold text-lg font-serif-title">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h3>
                            <div id="badge-tent-back" class="table-badge mt-2 inline-block bg-amber-400 text-slate-950 text-[10px] font-black px-3 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA' }}
                            </div>
                        </div>

                        <div class="p-4 flex flex-col items-center justify-center space-y-2">
                            <p class="text-xs font-extrabold text-emerald-900 uppercase">Pindai untuk Memesan</p>
                            <div class="w-40 h-40 bg-emerald-50 p-2 rounded-xl border border-emerald-200 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                                {!! $qrSvg !!}
                            </div>
                            <p class="text-[10px] text-slate-500">Scan dengan kamera HP untuk memilih sajian</p>
                        </div>
                        <div class="bg-emerald-950 text-white py-1.5 px-3 text-[9px] text-center">
                            SISI BELAKANG (BACK)
                        </div>
                    </div>

                </div>

                <!-- Petunjuk Lipat -->
                <div class="w-full text-center text-xs font-extrabold text-emerald-800 py-2 border-t-2 border-dashed border-emerald-300 bg-emerald-50 rounded-xl">
                    <i class="fa-solid fa-arrows-up-down mr-1"></i> LIPAT DI TENGAH: Kertas langsung dapat berdiri berbentuk segitiga di atas meja tanpa membutuhkan tempat akrilik!
                </div>

            </div>
        </div>


        <!-- ================= MODE 4: 2 KARTU PER LEMBAR A4 (HEMAT KERTAS) ================= -->
        <div id="view-2up" class="print-view w-full hidden justify-center">
            <div id="container-2up" class="w-full max-w-4xl grid grid-cols-1 sm:grid-cols-2 gap-8 items-center justify-center">
                
                <!-- KARTU 1 -->
                <div class="standee-card layout-2up bg-white rounded-3xl shadow-lg border-2 border-emerald-900/15 overflow-hidden flex flex-col text-center mx-auto">
                    <div class="bg-emerald-900 text-white pt-5 pb-3 px-4 text-center">
                        <div class="w-10 h-10 bg-white p-1 rounded-xl shadow mx-auto mb-1 flex items-center justify-center">
                            <img src="{{ $setting->logo_url }}" crossorigin="anonymous" alt="Sekarsa Logo" class="w-full h-full object-contain">
                        </div>
                        <h3 class="font-bold text-base font-serif-title">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h3>
                        <div id="badge-2up-1" class="table-badge mt-1.5 inline-block bg-amber-400 text-slate-950 text-[10px] font-black px-3 py-0.5 rounded-full uppercase tracking-wider">
                            {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA' }}
                        </div>
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center space-y-2">
                        <p class="text-[11px] font-extrabold text-emerald-900 uppercase">Pindai untuk Memesan</p>
                        <div class="w-36 h-36 bg-emerald-50 p-2 rounded-xl border border-emerald-200 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                            {!! $qrSvg !!}
                        </div>
                        <p class="text-[9px] text-slate-500">Scan untuk buka menu & bayar online</p>
                    </div>
                    <div class="bg-emerald-950 text-white py-1.5 px-3 text-[9px] flex justify-between">
                        <span>Kode QR Meja</span>
                        <span>Sekarsa Coffee</span>
                    </div>
                </div>

                <!-- KARTU 2 -->
                <div class="standee-card layout-2up bg-white rounded-3xl shadow-lg border-2 border-emerald-900/15 overflow-hidden flex flex-col text-center mx-auto">
                    <div class="bg-emerald-900 text-white pt-5 pb-3 px-4 text-center">
                        <div class="w-10 h-10 bg-white p-1 rounded-xl shadow mx-auto mb-1 flex items-center justify-center">
                            <img src="{{ $setting->logo_url }}" crossorigin="anonymous" alt="Sekarsa Logo" class="w-full h-full object-contain">
                        </div>
                        <h3 class="font-bold text-base font-serif-title">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h3>
                        <div id="badge-2up-2" class="table-badge mt-1.5 inline-block bg-amber-400 text-slate-950 text-[10px] font-black px-3 py-0.5 rounded-full uppercase tracking-wider">
                            {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA' }}
                        </div>
                    </div>
                    <div class="p-4 flex flex-col items-center justify-center space-y-2">
                        <p class="text-[11px] font-extrabold text-emerald-900 uppercase">Pindai untuk Memesan</p>
                        <div class="w-36 h-36 bg-emerald-50 p-2 rounded-xl border border-emerald-200 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                            {!! $qrSvg !!}
                        </div>
                        <p class="text-[9px] text-slate-500">Scan untuk buka menu & bayar online</p>
                    </div>
                    <div class="bg-emerald-950 text-white py-1.5 px-3 text-[9px] flex justify-between">
                        <span>Kode QR Meja</span>
                        <span>Sekarsa Coffee</span>
                    </div>
                </div>

            </div>
        </div>

    </main>


    <!-- SCRIPT INTERAKTIF: GANTI FORMAT, GANTI MEJA & UNDUH GAMBAR PNG -->
    <script>
        let currentLayout = 'a4-full';

        function setLayout(layout) {
            currentLayout = layout;

            // Sembunyikan semua view
            document.querySelectorAll('.print-view').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('flex');
            });

            // Tampilkan view yang dipilih
            const target = document.getElementById('view-' + layout);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('flex');
            }

            // Update active state tombol toolbar
            document.querySelectorAll('.layout-btn').forEach(btn => {
                btn.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm');
                btn.classList.add('bg-slate-100', 'text-slate-700');
            });

            const activeBtn = document.getElementById('btn-' + layout);
            if (activeBtn) {
                activeBtn.classList.remove('bg-slate-100', 'text-slate-700');
                activeBtn.classList.add('bg-emerald-700', 'text-white', 'shadow-sm');
            }
        }

        function updateTableBadge(val) {
            const trimmed = val.trim();
            const text = trimmed ? ('MEJA ' + trimmed) : 'MEJA SEKARSA COFFEE';
            document.querySelectorAll('.table-badge').forEach(badge => {
                badge.innerText = text;
            });
        }

        // ================= FUNGSI UNDUH GAMBAR PNG HD =================
        async function downloadAsImage() {
            const btn = document.getElementById('btnDownloadImage');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Sedang Memproses...';

            // Ambil elemen yang sedang aktif tampil
            const activeView = document.getElementById('view-' + currentLayout);
            let targetEl = activeView.querySelector('.standee-card');

            if (currentLayout === 'tent-card') {
                targetEl = activeView.querySelector('.layout-tent-card');
            } else if (currentLayout === '2up') {
                targetEl = document.getElementById('container-2up');
            }

            if (!targetEl) {
                targetEl = activeView;
            }

            try {
                // Konversi DOM menjadi Canvas beresolusi tinggi (scale 3x = 300 DPI ultra-tajam)
                const canvas = await html2canvas(targetEl, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    imageTimeout: 10000,
                });

                const tableVal = document.getElementById('inputTableNumber').value.trim();
                const tableSuffix = tableVal ? ('_Meja_' + tableVal.replace(/[^a-zA-Z0-9]/g, '_')) : '_Umum';
                const fileName = `Standee_QR_Sekarsa_${currentLayout}${tableSuffix}.png`;

                // Buat link download otomatis
                const link = document.createElement('a');
                link.download = fileName;
                link.href = canvas.toDataURL('image/png', 1.0);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                Swal.fire({
                    icon: 'success',
                    title: 'Gambar Berhasil Diunduh!',
                    text: `File ${fileName} telah tersimpan. Gambar siap dicetak atau digunakan langsung!`,
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } catch (err) {
                console.error('Download image error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunduh',
                    text: 'Terjadi kendala saat memproses gambar: ' + (err.message || 'Coba gunakan tombol Cetak.')
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }
    </script>

</body>
</html>
