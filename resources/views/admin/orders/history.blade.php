@extends('layouts.admin')

@section('title', 'Riwayat Pesanan - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
                <span>Riwayat Pesanan Selesai</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Catatan seluruh transaksi pesanan yang telah selesai diambil atau dibatalkan.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports') }}" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Lihat Laporan Penjualan</span>
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.orders.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. antrean, pesanan, nama..."
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status Riwayat --</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Pesanan Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Pesanan Dibatalkan</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.history') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="p-3.5">Antrean</th>
                        <th class="p-3.5">No. Pesanan</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Tanggal Masuk</th>
                        <th class="p-3.5">Item</th>
                        <th class="p-3.5">Total Harga</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5 font-bold font-mono text-emerald-800 text-sm">#{{ $ord->queue_number }}</td>
                            <td class="p-3.5 font-mono text-gray-700">{{ $ord->order_number }}</td>
                            <td class="p-3.5 font-bold text-gray-900">
                                {{ $ord->customer_name }}
                                @if($ord->table_number)
                                    <span class="ml-1 text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded font-normal">Meja {{ $ord->table_number }}</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-gray-500">
                                {{ $ord->received_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="p-3.5">{{ $ord->details->sum('quantity') }} porsi</td>
                            <td class="p-3.5 font-bold font-mono text-emerald-900 text-sm">
                                Rp {{ number_format($ord->total_price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                                    {{ $ord->status === 'selesai' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <a href="{{ route('admin.orders.show', $ord) }}" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg font-semibold transition inline-flex items-center space-x-1">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-400">
                                <i class="fa-solid fa-clock-rotate-left text-4xl mb-2 text-gray-300"></i>
                                <p class="font-semibold text-gray-600 text-xs">Belum ada riwayat pesanan</p>
                            </td>
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

