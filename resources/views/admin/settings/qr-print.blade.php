<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standee QR Code Meja - {{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- html2canvas untuk Unduh Gambar Otomatis -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* Desain Kartu Standee Proporsional Meja Kafe (Rasio Akrilik A5 / Meja) */
        .cafe-standee {
            width: 440px;
            background: #ffffff;
            border-radius: 28px;
            overflow: hidden;
            border: 2px solid #064e3b;
            box-shadow: 0 16px 36px -10px rgba(6, 78, 59, 0.22);
            text-align: center;
            display: flex;
            flex-direction: column;
            margin: 0 auto;
        }

        /* Format Penuh A4 */
        .layout-a4-size {
            width: 520px;
        }

        /* Header Mewah Sekarsa Coffee (Gradien Emerald Solid - Bebas Noise) */
        .standee-header {
            background: linear-gradient(145deg, #04251b 0%, #064e3b 55%, #022017 100%);
            padding: 26px 24px 20px;
            color: #ffffff;
            position: relative;
        }

        /* Frame Logo Kafe */
        .logo-box {
            width: 72px;
            height: 72px;
            background: #ffffff;
            border-radius: 20px;
            border: 2.5px solid #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            margin: 0 auto 10px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        /* Badge Meja Elegan */
        .table-pill {
            background: #f59e0b;
            color: #0f172a;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: 2px;
            padding: 6px 20px;
            border-radius: 9999px;
            display: inline-block;
            margin-top: 10px;
            border: 1.5px solid #fbbf24;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        /* Area Konten QR Code */
        .standee-body {
            padding: 22px 24px 18px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
        }

        /* Bingkai QR Code Bersih */
        .qr-frame {
            background: #f0fdf4;
            padding: 14px;
            border-radius: 24px;
            border: 2px solid #a7f3d0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-inner {
            width: 220px;
            height: 220px;
            background: #ffffff;
            padding: 10px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-inner svg {
            width: 100% !important;
            height: 100% !important;
            display: block !important;
        }

        /* 3 Langkah Alur Pemesanan */
        .steps-container {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            padding-top: 12px;
            border-top: 1.5px solid #e2e8f0;
        }

        .step-item {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            padding: 8px 4px;
            text-align: center;
        }

        .step-num {
            width: 22px;
            height: 22px;
            background: #064e3b;
            color: #ffffff;
            font-size: 11px;
            font-weight: 800;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3px;
        }

        /* Footer Standee */
        .standee-footer {
            background: #022017;
            color: #a7f3d0;
            padding: 10px 20px;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #064e3b;
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
                margin: 6mm;
            }

            .cafe-standee {
                box-shadow: none !important;
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800 flex flex-col items-center">

    <!-- ================= TOOLBAR KONTROL CETAK & UNDUH (NO-PRINT) ================= -->
    <header class="no-print w-full bg-white border-b border-slate-200 sticky top-0 z-50 shadow-md">
        <div class="max-w-5xl mx-auto px-4 py-3 flex flex-wrap items-center justify-between gap-3">
            
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-800 text-white flex items-center justify-center font-bold text-lg shadow">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900">Standee QR Code Meja</h1>
                    <p class="text-[11px] text-slate-500">Format proporsional meja kafe (Akrilik A5 / A6)</p>
                </div>
            </div>

            <!-- Pilihan Ukuran & Input Nomor Meja -->
            <div class="flex flex-wrap items-center gap-2">
                
                <!-- Ganti Nomor Meja -->
                <div class="flex items-center space-x-1.5 bg-slate-50 border border-slate-300 rounded-xl px-2.5 py-1">
                    <span class="text-[11px] font-bold text-slate-500">Meja:</span>
                    <input type="text" id="inputTableNumber" value="{{ $tableNumber ?? '' }}"
                           placeholder="Contoh: 01"
                           oninput="updateTableBadge(this.value)"
                           class="w-24 bg-transparent text-xs font-black text-emerald-900 focus:outline-none"
                           title="Ketik nomor meja untuk mengganti nomor meja pada kartu">
                </div>

                <!-- Pilihan Ukuran Kartu -->
                <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-xl">
                    <button type="button" onclick="setSize('standard')" id="btn-size-standard"
                            class="size-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-700 text-white shadow-sm transition">
                        Standar Meja (A5)
                    </button>
                    <button type="button" onclick="setSize('large')" id="btn-size-large"
                            class="size-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-900 transition">
                        Ukuran Besar (A4)
                    </button>
                </div>

                <!-- Tombol Utama: UNDUH GAMBAR PNG -->
                <button type="button" onclick="downloadStandeeImage()" id="btnDownloadImage"
                        class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white rounded-xl text-xs font-extrabold transition flex items-center space-x-1.5 shadow-md shadow-emerald-700/30 cursor-pointer">
                    <i class="fa-solid fa-download text-sm"></i>
                    <span>Unduh Gambar (PNG HD)</span>
                </button>

                <!-- Tombol Cetak Langsung -->
                <button type="button" onclick="window.print()"
                        class="px-3 py-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow">
                    <i class="fa-solid fa-print"></i>
                    <span>Cetak</span>
                </button>
            </div>

        </div>

        <div class="bg-emerald-50 border-t border-emerald-100 px-4 py-1.5 text-center text-[11px] text-emerald-900">
            <i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i>
            <strong>Hasil Sempurna:</strong> Desain di bawah menggunakan rasio meja kafe standar tanpa ruang kosong berlebih. Klik <strong>"Unduh Gambar (PNG HD)"</strong> untuk menyimpan gambar tajam siap cetak.
        </div>
    </header>


    <!-- ================= AREA KARTU STANDEE MEJA KAFE ================= -->
    <main class="py-8 px-4 w-full flex justify-center items-center">

        <!-- KARTU STANDEE MEJA PROPORSIONAL -->
        <div id="standeeCard" class="cafe-standee">
            
            <!-- 1. HEADER ATAS KAFE (Solid Emerald Gradient - Bersih & Elegan) -->
            <div class="standee-header">
                <!-- Logo Box -->
                <div class="logo-box">
                    <img src="{{ $setting->logo_url }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>

                <!-- Nama Kafe -->
                <h2 class="text-2xl font-black text-white tracking-wide uppercase drop-shadow-sm">
                    {{ $setting->cafe_name ?? 'SEKARSA COFFEE' }}
                </h2>

                <!-- Tagline -->
                <p class="text-[11px] text-emerald-200 mt-0.5 tracking-wider font-semibold">
                    {{ $setting->cafe_tagline ?? 'Warmth, Taste & Harmony in Every Sip' }}
                </p>

                <!-- Badge Nomor Meja -->
                <div>
                    <div id="tableBadgeDisplay" class="table-pill">
                        {{ $tableNumber ? 'MEJA ' . $tableNumber : 'MEJA SEKARSA COFFEE' }}
                    </div>
                </div>
            </div>

            <!-- 2. BODY KONTEN (QR CODE & PANDUAN) -->
            <div class="standee-body">
                
                <!-- Judul Ajakan Scan -->
                <div>
                    <h3 class="text-base font-extrabold text-emerald-950 uppercase tracking-wide">
                        Pindai untuk Memesan
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">
                        Arahkan kamera HP Anda ke QR Code untuk pesan langsung
                    </p>
                </div>

                <!-- Frame QR Code Proporsional & Besar -->
                <div class="qr-frame">
                    <div class="qr-inner">
                        {!! $qrSvg !!}
                    </div>
                </div>

                <!-- 3 Langkah Alur Pemesanan Cepat -->
                <div class="steps-container">
                    <div class="step-item">
                        <span class="step-num">1</span>
                        <p class="font-bold text-[11px] text-slate-800">Scan QR</p>
                        <p class="text-[9px] text-slate-500">Pilih menu sajian</p>
                    </div>
                    <div class="step-item">
                        <span class="step-num">2</span>
                        <p class="font-bold text-[11px] text-slate-800">Order & Bayar</p>
                        <p class="text-[9px] text-slate-500">QRIS / Online / Kasir</p>
                    </div>
                    <div class="step-item">
                        <span class="step-num">3</span>
                        <p class="font-bold text-[11px] text-slate-800">Santai Sejenak</p>
                        <p class="text-[9px] text-slate-500">Sajian siap diantar</p>
                    </div>
                </div>

            </div>

            <!-- 3. FOOTER STANDEE -->
            <div class="standee-footer">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-wifi text-amber-400"></i>
                    <span>Wi-Fi: <strong>Sekarsa-Guest</strong></span>
                </span>
                <span class="font-bold text-emerald-300">
                    Sekarsa Coffee &bull; Dine-In
                </span>
            </div>

        </div>

    </main>


    <!-- ================= SCRIPT KONTROL ================= -->
    <script>
        // Update Teks Badge Meja secara Instan
        function updateTableBadge(val) {
            const trimmed = val.trim();
            const badge = document.getElementById('tableBadgeDisplay');
            if (badge) {
                badge.innerText = trimmed ? ('MEJA ' + trimmed) : 'MEJA SEKARSA COFFEE';
            }
        }

        // Ganti Ukuran Kartu (Standar / Besar)
        function setSize(size) {
            const card = document.getElementById('standeeCard');
            const btnStandard = document.getElementById('btn-size-standard');
            const btnLarge = document.getElementById('btn-size-large');

            if (size === 'large') {
                card.classList.add('layout-a4-size');
                btnLarge.classList.add('bg-emerald-700', 'text-white', 'shadow-sm');
                btnLarge.classList.remove('text-slate-600');
                btnStandard.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm');
                btnStandard.classList.add('text-slate-600');
            } else {
                card.classList.remove('layout-a4-size');
                btnStandard.classList.add('bg-emerald-700', 'text-white', 'shadow-sm');
                btnStandard.classList.remove('text-slate-600');
                btnLarge.classList.remove('bg-emerald-700', 'text-white', 'shadow-sm');
                btnLarge.classList.add('text-slate-600');
            }
        }

        // Fungsi Unduh Gambar PNG Beresolusi Tinggi (HD / 300 DPI)
        async function downloadStandeeImage() {
            const btn = document.getElementById('btnDownloadImage');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i> Memproses HD...';

            const card = document.getElementById('standeeCard');

            try {
                // Gunakan html2canvas dengan scale 3 (menghasilkan gambar tajam ~1320px x 1860px)
                const canvas = await html2canvas(card, {
                    scale: 3,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                });

                const tableVal = document.getElementById('inputTableNumber').value.trim();
                const tableSuffix = tableVal ? ('_Meja_' + tableVal.replace(/[^a-zA-Z0-9]/g, '_')) : '_Umum';
                const fileName = `Standee_QR_Sekarsa${tableSuffix}.png`;

                const link = document.createElement('a');
                link.download = fileName;
                link.href = canvas.toDataURL('image/png', 1.0);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                Swal.fire({
                    icon: 'success',
                    title: 'Gambar Siap!',
                    text: `File ${fileName} berhasil diunduh dengan kualitas HD tajam!`,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } catch (err) {
                console.error('Download image error:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengunduh',
                    text: 'Terjadi kendala saat memproses gambar: ' + (err.message || 'Silakan coba lagi.')
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
        }
    </script>

</body>
</html>
