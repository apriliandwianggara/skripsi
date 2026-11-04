<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Standee Meja {{ $meja->nomor_meja }} - {{ $pengaturan->nama_cafe }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f3f4f6;
        }
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
        }
        .standee-card {
            width: 105mm;
            height: 148mm; /* A6 acrylic standard */
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">

    <div class="no-print mb-6 flex space-x-3">
        <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md">
            Cetak Standee Meja (A6)
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-xl text-xs">
            Tutup
        </button>
    </div>

    <!-- Standee Card Container -->
    <div class="standee-card bg-white rounded-3xl overflow-hidden border border-emerald-900/10 flex flex-col justify-between text-center relative p-6">
        
        <!-- Header -->
        <div>
            <div class="w-12 h-12 mx-auto mb-2 rounded-2xl bg-emerald-900 text-white flex items-center justify-center text-xl shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                    <line x1="6" y1="1" x2="6" y2="4"></line>
                    <line x1="10" y1="1" x2="10" y2="4"></line>
                    <line x1="14" y1="1" x2="14" y2="4"></line>
                </svg>
            </div>
            <h1 class="font-serif font-black text-xl text-emerald-950 tracking-wider uppercase">{{ $pengaturan->nama_cafe ?? 'SEKARSA COFFEE' }}</h1>
            <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-widest mt-0.5">Self-Order &bull; FIFO Queue System</p>
        </div>

        <!-- Table Number Callout -->
        <div class="my-2 py-2 px-4 bg-emerald-50 rounded-2xl border border-emerald-200/80 inline-block mx-auto">
            <span class="text-[10px] font-black uppercase text-emerald-800 tracking-widest">Nomor Meja</span>
            <h2 class="text-4xl font-black text-emerald-900 font-mono tracking-tight leading-none mt-0.5">{{ $meja->nomor_meja }}</h2>
            <span class="text-[9px] text-gray-500 font-medium">{{ $meja->lokasi }}</span>
        </div>

        <!-- QR Code SVG Image -->
        <div class="p-3 bg-white rounded-2xl border-2 border-emerald-800/20 shadow-inner max-w-[170px] mx-auto">
            <div class="w-full h-auto">
                {!! $qrSvg !!}
            </div>
        </div>

        <!-- Instructions -->
        <div>
            <h3 class="text-xs font-black text-gray-900 uppercase tracking-wide">Pindai QR Code Untuk Memesan</h3>
            <p class="text-[10px] text-gray-500 mt-0.5 px-2">Buka kamera ponsel Anda, arahkan ke QR di atas, pilih sajian kopi dan kirim pesanan.</p>
        </div>

        <!-- Footer -->
        <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[9px] text-gray-400 font-semibold px-2">
            <span>{{ $pengaturan->instagram ?? '@sekarsacoffee' }}</span>
            <span>Free Wi-Fi Available</span>
        </div>

    </div>

</body>
</html>
