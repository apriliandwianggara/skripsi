<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Standee QR Code - {{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            @page { margin: 1cm; size: auto; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen p-6 flex flex-col items-center justify-center">

    <!-- Action Toolbar No-Print -->
    <div class="no-print mb-6 max-w-sm w-full bg-white p-4 rounded-2xl shadow-md border border-gray-200 flex items-center justify-between">
        <div class="text-xs text-gray-600">
            <strong>Standee Meja QR Code</strong>
            <p class="text-[10px] text-gray-400">Siap dicetak di kertas tebal / akrilik</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1 shadow">
                <i class="fa-solid fa-print"></i>
                <span>Cetak</span>
            </button>
            <button onclick="window.close()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold">
                Tutup
            </button>
        </div>
    </div>

    <!-- STANDEE CARD (Ukuran Ideal Akrilik A6 / Meja) -->
    <div class="w-80 bg-white rounded-3xl shadow-2xl overflow-hidden border-2 border-emerald-900/10 text-center flex flex-col items-center">

        <!-- Top Header Card with Sekarsa Photo & Logo -->
        <div class="w-full relative overflow-hidden text-white pt-8 pb-6 px-6">
            <!-- Background Foto Kafe Sekarsa -->
            <img src="{{ $setting->banner_url }}" alt="Sekarsa Cafe" class="absolute inset-0 w-full h-full object-cover object-center filter brightness-50">
            <!-- Overlay Gradien Hijau Emerald Sekarsa -->
            <div class="absolute inset-0 bg-gradient-to-b from-emerald-950/90 via-emerald-900/85 to-emerald-950/95"></div>

            <div class="relative z-10 flex flex-col items-center">
                <!-- Logo Sekarsa Coffee -->
                <div class="w-16 h-16 bg-white p-1 rounded-2xl shadow-xl mx-auto mb-3 border border-white/40 overflow-hidden flex items-center justify-center">
                    <img src="{{ $setting->logo_url }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="font-bold text-2xl font-serif tracking-wider text-white drop-shadow-md">
                    {{ $setting->cafe_name ?? 'SEKARSA COFFEE' }}
                </h1>
                <p class="text-xs text-emerald-200 mt-0.5 tracking-wide drop-shadow">
                    {{ $setting->cafe_tagline ?? 'Coffee & Space' }}
                </p>

                @if($tableNumber)
                    <div class="mt-3 inline-block bg-amber-400 text-slate-950 text-xs font-extrabold px-4 py-1 rounded-full uppercase tracking-widest shadow-md">
                        MEJA {{ $tableNumber }}
                    </div>
                @endif
            </div>
        </div>

        <!-- QR Body Container -->
        <div class="p-6 w-full flex flex-col items-center bg-white space-y-4">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-widest text-emerald-900">
                    Pindai untuk Memesan
                </p>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    Scan QR Code ini menggunakan kamera ponsel pintar Anda
                </p>
            </div>

            <!-- SVG QR Code Frame -->
            <div class="p-4 bg-emerald-50/60 rounded-2xl border-2 border-emerald-200/80 shadow-inner flex items-center justify-center">
                <div class="w-48 h-48 bg-white p-2 rounded-xl shadow-sm overflow-hidden flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:block">
                    {!! $qrSvg !!}
                </div>
            </div>

            <!-- Petunjuk Alur 3 Langkah Singkat -->
            <div class="w-full grid grid-cols-3 gap-2 text-[10px] text-gray-600 pt-2 border-t border-gray-150">
                <div class="text-center">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold inline-flex items-center justify-center mb-1">1</span>
                    <p class="font-semibold text-gray-800">Scan QR</p>
                    <p class="text-[9px] text-gray-400">Pilih menu</p>
                </div>
                <div class="text-center">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold inline-flex items-center justify-center mb-1">2</span>
                    <p class="font-semibold text-gray-800">Kirim Order</p>
                    <p class="text-[9px] text-gray-400">Antrean FIFO</p>
                </div>
                <div class="text-center">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 font-bold inline-flex items-center justify-center mb-1">3</span>
                    <p class="font-semibold text-gray-800">Ambil Sajian</p>
                    <p class="text-[9px] text-gray-400">Saat siap</p>
                </div>
            </div>
        </div>

        <!-- Footer Card -->
        <div class="w-full bg-emerald-950 text-white py-3 px-4 text-[10px] text-emerald-200/80 flex items-center justify-between">
            <span>&bull; Kode QR Permanen</span>
            <span>FIFO System &bull;</span>
        </div>

    </div>

</body>
</html>

