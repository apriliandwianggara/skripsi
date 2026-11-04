<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - {{ \App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11pt; color: #000; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 p-8">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow print:shadow-none print:p-0">

        <!-- Tombol Aksi Cetak -->
        <div class="no-print mb-6 flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-200">
            <span class="text-xs text-gray-600">Tekan tombol cetak atau gunakan shortcut <strong>Ctrl+P</strong> untuk mencetak dokumen ini.</span>
            <div class="space-x-2">
                <button onclick="window.print()" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition">
                    Cetak Laporan
                </button>
                <button onclick="window.close()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Kop Surat -->
        <div class="text-center border-b-2 border-emerald-900 pb-4 mb-6">
            <h1 class="text-2xl font-black uppercase tracking-wider text-emerald-950 font-serif">
                {{ \App\Models\Setting::get()->cafe_name ?? 'SEKARSA COFFEE' }}
            </h1>
            <p class="text-xs text-gray-600 mt-1">
                {{ \App\Models\Setting::get()->cafe_address ?? 'Jl. Sekarsa, Tempat Santai & Produktif' }}
                @if(\App\Models\Setting::get()->cafe_phone)
                    &bull; Telp/WA: {{ \App\Models\Setting::get()->cafe_phone }}
                @endif
            </p>
            <h2 class="text-base font-bold text-gray-800 mt-3 uppercase tracking-wide">
                Laporan Rekapitulasi Pesanan & Penjualan
            </h2>
            <p class="text-xs text-gray-500 font-mono">
                Periode: {{ $startDate->translatedFormat('d F Y') }} s/d {{ $endDate->translatedFormat('d F Y') }}
            </p>
        </div>

        <!-- Tabel Data -->
        <table class="w-full text-xs text-left border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100 text-gray-800 font-bold uppercase text-[10px]">
                    <th class="border border-gray-300 p-2 text-center w-10">No</th>
                    <th class="border border-gray-300 p-2">Waktu Masuk</th>
                    <th class="border border-gray-300 p-2">Antrean</th>
                    <th class="border border-gray-300 p-2">No. Pesanan</th>
                    <th class="border border-gray-300 p-2">Pelanggan</th>
                    <th class="border border-gray-300 p-2">Rincian Menu</th>
                    <th class="border border-gray-300 p-2 text-center">Status</th>
                    <th class="border border-gray-300 p-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $i => $ord)
                    <tr class="border-b border-gray-200">
                        <td class="border border-gray-300 p-2 text-center">{{ $i + 1 }}</td>
                        <td class="border border-gray-300 p-2 font-mono">{{ $ord->received_at->format('d/m/Y H:i') }}</td>
                        <td class="border border-gray-300 p-2 font-mono font-bold text-center">#{{ $ord->queue_number }}</td>
                        <td class="border border-gray-300 p-2 font-mono">{{ $ord->order_number }}</td>
                        <td class="border border-gray-300 p-2">{{ $ord->customer_name }}</td>
                        <td class="border border-gray-300 p-2">{{ $ord->details->map(fn($d) => $d->quantity . 'x ' . $d->menu_name)->join(', ') }}</td>
                        <td class="border border-gray-300 p-2 text-center capitalize">{{ $ord->status_label }}</td>
                        <td class="border border-gray-300 p-2 text-right font-mono font-semibold">
                            Rp {{ number_format($ord->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border border-gray-300 p-4 text-center text-gray-400">Tidak ada transaksi</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-gray-100 font-bold">
                    <td colspan="7" class="border border-gray-300 p-2 text-right uppercase">Total Pendapatan Bersih:</td>
                    <td class="border border-gray-300 p-2 text-right font-mono text-sm text-emerald-900">
                        Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan -->
        <div class="mt-12 flex justify-end">
            <div class="text-center text-xs space-y-12">
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB<br>Penanggung Jawab Sekarsa Coffee,</p>
                <div class="border-b border-gray-400 w-44 mx-auto"></div>
                <p class="font-bold text-gray-900">({{ auth()->user()->name ?? 'Administrator' }})</p>
            </div>
        </div>

    </div>

</body>
</html>
