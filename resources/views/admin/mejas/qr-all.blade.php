<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua QR Standee Meja - {{ $pengaturan->nama_cafe }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f9fafb;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="p-6">

    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
        <div>
            <h2 class="font-bold text-gray-900 text-sm">Lembar Cetak QR Code Meja (Siap Gunting / Acrylic Standee)</h2>
            <p class="text-xs text-gray-500">Total {{ count($qrData) }} Meja siap cetak ke ukuran kertas A4.</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md">
                Cetak Halaman (Print)
            </button>
            <button onclick="window.close()" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs">
                Tutup
            </button>
        </div>
    </div>

    <!-- Grid Layout 2x2 per section -->
    <div class="max-w-4xl mx-auto grid grid-cols-2 gap-6">
        @foreach($qrData as $item)
        <div class="bg-white rounded-3xl border-2 border-dashed border-gray-300 p-6 flex flex-col justify-between text-center relative break-inside-avoid shadow-sm">
            
            <!-- Header -->
            <div>
                <h1 class="font-serif font-black text-lg text-emerald-950 uppercase tracking-wider">{{ $pengaturan->nama_cafe ?? 'SEKARSA COFFEE' }}</h1>
                <p class="text-[9px] text-emerald-700 font-bold uppercase tracking-widest">Self-Order &bull; FIFO Queue System</p>
            </div>

            <!-- Table Number -->
            <div class="my-2 py-1.5 px-4 bg-emerald-50 rounded-2xl border border-emerald-200/80 inline-block mx-auto">
                <span class="text-[9px] font-black uppercase text-emerald-800 tracking-widest">Nomor Meja</span>
                <h2 class="text-3xl font-black text-emerald-900 font-mono leading-none mt-0.5">{{ $item['meja']->nomor_meja }}</h2>
                <span class="text-[8px] text-gray-500">{{ $item['meja']->lokasi }}</span>
            </div>

            <!-- QR Code -->
            <div class="p-2.5 bg-white rounded-2xl border border-gray-200 max-w-[150px] mx-auto">
                <div class="w-full h-auto">
                    {!! $item['qrSvg'] !!}
                </div>
            </div>

            <!-- Instruction -->
            <div class="mt-2">
                <h3 class="text-[11px] font-black text-gray-900 uppercase">Scan QR untuk Memesan Menu</h3>
                <p class="text-[9px] text-gray-500 mt-0.5">Buka kamera ponsel Anda & arahkan ke kode ini.</p>
            </div>

            <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[8px] text-gray-400 font-semibold">
                <span>{{ $pengaturan->instagram ?? '@sekarsacoffee' }}</span>
                <span>Free Wi-Fi</span>
            </div>

        </div>
        @endforeach
    </div>

</body>
</html>
