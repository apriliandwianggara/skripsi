@extends('layouts.admin')

@section('title', 'Admin Dashboard - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-chart-pie text-emerald-600"></i>
                <span>Dashboard Administrator</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Ringkasan jumlah pesanan, status operasional FIFO, dan pendapatan harian Sekarsa Coffee.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="text-xs text-gray-500 bg-white border border-gray-200 px-3 py-1.5 rounded-xl shadow-sm">
                <i class="fa-regular fa-calendar mr-1 text-emerald-600"></i>
                Hari ini: <strong>{{ now()->translatedFormat('d F Y') }}</strong>
            </span>
        </div>
    </div>

    <!-- 6 KPI Status Cards Hari Ini -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total Pesanan -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Hari Ini</span>
                <span class="w-7 h-7 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-receipt"></i>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-gray-900 font-mono" id="kpiTotalOrders">{{ $totalOrdersToday }}</p>
            <p class="text-[10px] text-gray-400 mt-1">pesanan masuk</p>
        </div>

        <!-- Menunggu -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Menunggu</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 font-mono" id="kpiWaiting">{{ $waitingCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">dalam antrean</p>
        </div>

        <!-- Sedang Diproses -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">Diproses</span>
                <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-fire-burner"></i>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-blue-600 font-mono" id="kpiProcessing">{{ $processingCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">oleh barista</p>
        </div>

        <!-- Siap Diambil -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Siap Diambil</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-bell"></i>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 font-mono" id="kpiReady">{{ $readyCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">di counter</p>
        </div>

        <!-- Selesai -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600">Selesai</span>
                <span class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-purple-600 font-mono" id="kpiCompleted">{{ $completedCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">sukses diambil</p>
        </div>

        <!-- Dibatalkan -->
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-600">Dibatalkan</span>
                <span class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
            </div>
            <p class="text-2xl font-extrabold text-red-600 font-mono" id="kpiCancelled">{{ $cancelledCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">dibatalkan</p>
        </div>
    </div>

    <!-- Revenue Banner & Master Counters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 bg-gradient-to-r from-sekarsa-950 to-emerald-900 text-white rounded-2xl p-5 shadow-lg flex items-center justify-between">
            <div>
                <p class="text-xs text-emerald-300 font-semibold uppercase tracking-wider">Pendapatan Hari Ini</p>
                <h3 class="text-3xl font-black font-mono mt-1 text-white">Rp {{ number_format($totalRevenueToday, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-emerald-200/80 mt-1">Berdasarkan pesanan siap diambil dan selesai</p>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-3xl text-emerald-300">
                <i class="fa-solid fa-wallet"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Katalog Sajian</p>
                <h3 class="text-2xl font-extrabold text-gray-800 font-mono mt-1">{{ $totalMenu }} <span class="text-xs text-gray-400 font-normal">Menu</span></h3>
                <p class="text-[11px] text-gray-500 mt-0.5">{{ $totalCategory }} Kategori aktif</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-utensils"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Pengguna Sistem</p>
                <h3 class="text-2xl font-extrabold text-gray-800 font-mono mt-1">{{ $totalUser }} <span class="text-xs text-gray-400 font-normal">Akun</span></h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Admin & Staf Barista</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>
    </div>

    <!-- Chart & Top Menus Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Line Chart 7 Hari Terakhir -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wide">Tren Pendapatan 7 Hari Terakhir</h3>
                    <p class="text-xs text-gray-400">Total omzet pesanan berhasil per hari</p>
                </div>
                <span class="text-xs text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg font-semibold">
                    Grafik Penjualan
                </span>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top 5 Menu Terlaris -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wide mb-3 flex items-center">
                    <i class="fa-solid fa-trophy text-amber-500 mr-2"></i> Menu Terlaris
                </h3>

                @if($topMenus->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-8">Belum ada data penjualan tercatat.</p>
                @else
                    <div class="space-y-3">
                        @foreach($topMenus as $index => $item)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                                    <span class="w-6 h-6 rounded-lg {{ $index === 0 ? 'bg-amber-400 text-amber-950 font-black' : 'bg-gray-100 text-gray-600 font-bold' }} flex items-center justify-center text-[11px] shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="font-semibold text-gray-800 truncate">{{ $item->menu_name }}</span>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-emerald-800 font-mono">{{ $item->total_qty }}x</span>
                                    <span class="text-[10px] text-gray-400 block">Rp {{ number_format($item->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100 mt-4">
                <a href="{{ route('admin.menus.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 flex items-center justify-center space-x-1">
                    <span>Kelola Semua Menu</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Tabel Pesanan Terbaru -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-bold text-sm text-gray-800 uppercase tracking-wide">
                <i class="fa-solid fa-clock-rotate-left mr-1 text-emerald-600"></i> Pesanan Terbaru Masuk
            </h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-emerald-700 hover:underline">
                Lihat Seluruh Pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="p-3.5">Antrean</th>
                        <th class="p-3.5">No. Pesanan</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Waktu Server</th>
                        <th class="p-3.5">Item</th>
                        <th class="p-3.5">Total</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="recentOrdersBody" class="divide-y divide-gray-100 font-normal">
                    @forelse($recentOrders as $ord)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5 font-bold font-mono text-emerald-800 text-sm">#{{ $ord->queue_number }}</td>
                            <td class="p-3.5 font-mono text-gray-700">{{ $ord->order_number }}</td>
                            <td class="p-3.5 font-semibold text-gray-900">
                                {{ $ord->customer_name }}
                                @if($ord->table_number)
                                    <span class="ml-1 text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">M-{{ $ord->table_number }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-gray-500 font-mono">{{ $ord->received_at->format('H:i:s') }}</td>
                            <td class="p-3.5">{{ $ord->details->sum('quantity') }} porsi</td>
                            <td class="p-3.5 font-bold text-gray-800 font-mono">Rp {{ number_format($ord->total_price, 0, ',', '.') }}</td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $ord->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $ord->status === 'diproses' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $ord->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $ord->status === 'selesai' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $ord->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $ord) }}" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-6 text-center text-gray-400">Belum ada transaksi pesanan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($chartData) !!},
                backgroundColor: 'rgba(22, 163, 74, 0.1)',
                borderColor: '#16a34a',
                borderWidth: 2.5,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#15803d',
                pointRadius: 4,
                pointHoverRadius: 6
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
                            return 'Rp ' + (value / 1000) + 'k';
                        }
                    }
                }
            }
        }
    });

    // Real-Time Background Sync untuk Dashboard Owner / Admin
    async function syncAdminDashboard() {
        try {
            const res = await fetch("{{ route('admin.live_stats') }}");
            if (!res.ok) return;

            const data = await res.json();
            if (!data.success) return;

            // Update KPI cards
            document.getElementById('kpiTotalOrders').innerText = data.kpi.total_orders;
            document.getElementById('kpiWaiting').innerText = data.kpi.waiting;
            document.getElementById('kpiProcessing').innerText = data.kpi.processing;
            document.getElementById('kpiReady').innerText = data.kpi.ready;
            document.getElementById('kpiCompleted').innerText = data.kpi.completed;
            document.getElementById('kpiCancelled').innerText = data.kpi.cancelled;

            // Render Recent Orders
            const tbody = document.getElementById('recentOrdersBody');
            if (tbody && data.recent_orders.length > 0) {
                let html = '';
                data.recent_orders.forEach(ord => {
                    let badgeClass = 'bg-gray-100 text-gray-800';
                    if (ord.status === 'menunggu') badgeClass = 'bg-amber-100 text-amber-800';
                    else if (ord.status === 'diproses') badgeClass = 'bg-blue-100 text-blue-800';
                    else if (ord.status === 'siap_diambil') badgeClass = 'bg-emerald-100 text-emerald-800';
                    else if (ord.status === 'selesai') badgeClass = 'bg-purple-100 text-purple-800';
                    else if (ord.status === 'dibatalkan') badgeClass = 'bg-red-100 text-red-800';

                    html += `
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5 font-bold font-mono text-emerald-800 text-sm">#${ord.queue_number}</td>
                            <td class="p-3.5 font-mono text-gray-700">${ord.order_number}</td>
                            <td class="p-3.5 font-semibold text-gray-900">
                                ${ord.customer_name}
                                ${ord.table_number ? `<span class="ml-1 text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">M-${ord.table_number}</span>` : ''}
                            </td>
                            <td class="p-3.5 text-gray-500 font-mono">${ord.received_at}</td>
                            <td class="p-3.5">${ord.total_items} porsi</td>
                            <td class="p-3.5 font-bold text-gray-800 font-mono">${ord.total_price}</td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">
                                    ${ord.status_label}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="${ord.detail_url}" class="px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-xs font-semibold transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }
        } catch (e) {
            console.warn('Gagal sync live admin stats:', e);
        }
    }

    // Jalankan sync setiap 3.5 detik tanpa reload halaman
    setInterval(syncAdminDashboard, 3500);
</script>
@endpush
