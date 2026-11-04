@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Pesanan - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i>
                <span>Laporan Rekapitulasi Pesanan</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Rekap pesanan, volume penjualan, dan total pendapatan berdasarkan rentang tanggal.
            </p>
        </div>

        <a href="{{ route('admin.reports.print', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'status' => request('status')]) }}" target="_blank"
           class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
            <i class="fa-solid fa-print"></i>
            <span>Cetak Rekapitulasi</span>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.reports') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Dari Tanggal:</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Filter Status:</label>
                <select name="status" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="siap_diambil" {{ request('status') === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-magnifying-glass mr-1"></i> Tampilkan Rekap
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Total Transaksi</span>
            <p class="text-2xl font-extrabold text-gray-900 font-mono mt-1">{{ number_format($totalOrders) }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">pesanan dalam periode</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Total Porsi Terjual</span>
            <p class="text-2xl font-extrabold text-blue-700 font-mono mt-1">{{ number_format($totalQuantity) }}</p>
            <p class="text-[11px] text-gray-400 mt-0.5">gelas / porsi menu</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-gray-400">Total Pendapatan Bersih</span>
            <p class="text-2xl font-extrabold text-emerald-800 font-mono mt-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
            <p class="text-[11px] text-emerald-600 mt-0.5">dari pesanan siap & selesai</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="p-3.5">Waktu Masuk</th>
                        <th class="p-3.5">Antrean</th>
                        <th class="p-3.5">No. Pesanan</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Daftar Menu</th>
                        <th class="p-3.5">Total Harga</th>
                        <th class="p-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5 font-mono text-gray-500 whitespace-nowrap">
                                {{ $ord->received_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-3.5 font-bold font-mono text-emerald-800">#{{ $ord->queue_number }}</td>
                            <td class="p-3.5 font-mono text-gray-700">{{ $ord->order_number }}</td>
                            <td class="p-3.5 font-semibold text-gray-900">{{ $ord->customer_name }}</td>
                            <td class="p-3.5">
                                <span class="text-gray-700 block max-w-xs truncate">{{ $ord->details->map(fn($d) => $d->quantity . 'x ' . $d->menu_name)->join(', ') }}</span>
                            </td>
                            <td class="p-3.5 font-bold font-mono text-emerald-900 whitespace-nowrap">
                                Rp {{ number_format($ord->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                    {{ $ord->status === 'selesai' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $ord->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $ord->status === 'diproses' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $ord->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $ord->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">Tidak ada data transaksi pada rentang tanggal ini</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
