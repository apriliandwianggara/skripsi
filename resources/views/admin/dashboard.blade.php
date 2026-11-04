@extends('layouts.admin')

@section('title', 'Dashboard Owner & Eksekutif - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Ringkasan Operasional Sekarsa Coffee</h1>
            <p class="text-xs text-gray-500 mt-0.5">Pantau pendapatan, antrean FIFO, dan performa penjualan secara real-time.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('kasir.dashboard') }}" class="px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-cash-register"></i>
                <span>Ke Layar Kasir</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="px-3.5 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 rounded-xl text-xs font-bold transition flex items-center space-x-1.5">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Laporan Lengkap</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Pendapatan Hari Ini -->
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Omzet Hari Ini</p>
                <h3 class="text-2xl font-black text-emerald-800 mt-1">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">
                    <i class="fa-solid fa-arrow-trend-up"></i> Dari {{ $totalPesananHariIni }} transaksi
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Omzet Bulan Ini</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Periode {{ now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <!-- Antrean Aktif FIFO -->
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Antrean Aktif FIFO</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $pesananAktif }} <span class="text-sm font-normal text-gray-500">pesanan</span></h3>
                <p class="text-[11px] text-amber-600 font-semibold mt-0.5">Sedang menunggu & diracik</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>

        <!-- Rata-rata Waktu FIFO Layanan -->
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Avg. Waktu Tunggu FIFO</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">{{ $fifoMetrics['rata_rata_waktu_layanan_menit'] }} <span class="text-sm font-normal text-gray-500">menit</span></h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Waktu rata-rata pengerjaan pesanan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
        </div>
    </div>

    <!-- Chart Penjualan 7 Hari & Top Menu Terlaris -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sales Trend Graph (8 cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900 font-serif">Tren Penjualan 7 Hari Terakhir</h3>
                    <p class="text-xs text-gray-400">Total omzet yang berstatus lunas</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-lg">
                    Grafik Mingguan
                </span>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Selling Menus (4 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-gray-200/80 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-base text-gray-900 font-serif">Menu Terlaris (Top 5)</h3>
                <p class="text-xs text-gray-400">Produk paling sering dipesan pelanggan</p>

                <div class="mt-4 space-y-3">
                    @forelse($topMenus as $idx => $m)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2.5">
                            <span class="w-6 h-6 rounded-lg {{ $idx === 0 ? 'bg-amber-500 text-white font-black' : 'bg-gray-100 text-gray-600 font-bold' }} flex items-center justify-center text-[11px]">
                                #{{ $idx + 1 }}
                            </span>
                            <span class="font-semibold text-gray-800 truncate max-w-[140px]">{{ $m->nama_menu }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-emerald-800">{{ $m->total_terjual }} porsi</span>
                            <p class="text-[10px] text-gray-400">Rp {{ number_format($m->total_pendapatan, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-6 text-center">Belum ada data penjualan tercatat.</p>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <a href="{{ route('admin.menus.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center justify-between">
                    <span>Lihat Semua {{ $totalMenu }} Menu</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Staff Audit Activity Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Pesanan Terbaru (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-base text-gray-900 font-serif">Pesanan Terbaru</h3>
                <a href="{{ route('admin.reports') }}" class="text-xs font-bold text-emerald-700 hover:underline">Semua Transaksi &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-100 text-gray-400 uppercase font-semibold">
                            <th class="pb-2">No. Pesanan</th>
                            <th class="pb-2">Meja & Pemesan</th>
                            <th class="pb-2">Waktu</th>
                            <th class="pb-2">Status</th>
                            <th class="pb-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentOrders as $order)
                        <tr>
                            <td class="py-2.5 font-bold font-mono text-gray-800">{{ $order->nomor_pesanan }}</td>
                            <td class="py-2.5">
                                <span class="font-semibold text-gray-800">{{ $order->nama_pelanggan }}</span>
                                <span class="text-gray-400 block text-[10px]">Meja {{ $order->meja->nomor_meja }}</span>
                            </td>
                            <td class="py-2.5 text-gray-500 font-mono">{{ $order->waktu_masuk->format('H:i') }}</td>
                            <td class="py-2.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold
                                    @if($order->status_pesanan === 'selesai') bg-emerald-100 text-emerald-800
                                    @elseif($order->status_pesanan === 'diproses') bg-blue-100 text-blue-800
                                    @elseif($order->status_pesanan === 'menunggu') bg-amber-100 text-amber-800
                                    @elseif($order->status_pesanan === 'diambil') bg-gray-100 text-gray-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $order->status_pesanan_label }}
                                </span>
                            </td>
                            <td class="py-2.5 text-right font-bold text-gray-900">
                                Rp {{ number_format($order->total_akhir, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Log Aktivitas Petugas (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-gray-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-gray-900 font-serif">Audit Log Aktivitas</h3>
                    <p class="text-xs text-gray-400">Riwayat aksi kasir & admin</p>
                </div>
                <a href="{{ route('admin.logs.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">Semua Log &rarr;</a>
            </div>

            <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                @foreach($recentLogs as $log)
                <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100 text-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-bold text-emerald-800 text-[11px]">{{ $log->user_name }}</span>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-700 leading-snug">{{ $log->deskripsi }}</p>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Omzet Penjualan (Rp)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#15803d',
                    backgroundColor: 'rgba(21, 128, 61, 0.1)',
                    borderWidth: 2.5,
                    tension: 0.35,
                    fill: true,
                    pointBackgroundColor: '#15803d',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
