@extends('layouts.admin')

@section('title', 'Manajemen Pesanan - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-clipboard-list text-emerald-600"></i>
                <span>Manajemen Pesanan Masuk</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Pantau seluruh pesanan aktif dan urutan antrean sistem FIFO di Sekarsa Coffee.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('pegawai.dashboard') }}" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                <i class="fa-solid fa-list-ol"></i>
                <span>Buka Layar Barista FIFO</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. antrean, pesanan, nama..."
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status Pesanan --</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu Antrean</option>
                    <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="siap_diambil" {{ request('status') === 'siap_diambil' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="p-3.5">Antrean</th>
                        <th class="p-3.5">No. Pesanan</th>
                        <th class="p-3.5">Pelanggan</th>
                        <th class="p-3.5">Meja</th>
                        <th class="p-3.5">Waktu Masuk</th>
                        <th class="p-3.5">Item</th>
                        <th class="p-3.5">Total Harga</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5 font-bold font-mono text-emerald-800 text-base">#{{ $ord->queue_number }}</td>
                            <td class="p-3.5 font-mono text-gray-700">{{ $ord->order_number }}</td>
                            <td class="p-3.5">
                                <span class="font-bold text-gray-900 block">{{ $ord->customer_name }}</span>
                                @if($ord->customer_phone)
                                    <span class="text-[10px] text-gray-400 font-mono"><i class="fa-brands fa-whatsapp text-emerald-600"></i> {{ $ord->customer_phone }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($ord->table_number)
                                    <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-semibold">Meja {{ $ord->table_number }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono text-gray-500">
                                {{ $ord->received_at->format('H:i:s') }}
                                <span class="text-[10px] text-gray-400 block">{{ $ord->received_at->translatedFormat('d M') }}</span>
                            </td>
                            <td class="p-3.5">{{ $ord->details->sum('quantity') }} porsi</td>
                            <td class="p-3.5 font-bold text-emerald-900 font-mono text-sm">
                                Rp {{ number_format($ord->total_price, 0, ',', '.') }}
                                <span class="text-[10px] block font-sans font-normal {{ $ord->isPaid() ? 'text-emerald-700 font-semibold' : 'text-amber-600' }}">
                                    {{ $ord->payment_method_label }} &bull; {{ $ord->payment_status_label }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold
                                    {{ $ord->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $ord->status === 'diproses' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $ord->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $ord->status === 'selesai' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $ord->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : '' }}">
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
                            <td colspan="9" class="p-8 text-center text-gray-400">
                                <i class="fa-solid fa-clipboard-list text-4xl mb-2 text-gray-300"></i>
                                <p class="font-semibold text-gray-600 text-xs">Tidak ada data pesanan</p>
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

