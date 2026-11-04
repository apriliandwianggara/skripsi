@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Transaksi - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Laporan Penjualan & Transaksi</h1>
            <p class="text-xs text-gray-500 mt-0.5">Rekapitulasi transaksi, omzet, dan metode pembayaran pelanggan Sekarsa Coffee.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reports.print', request()->all()) }}" target="_blank"
                class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan Penjualan</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm">
        <form action="{{ route('admin.reports') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                    class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                    class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Status Pesanan</label>
                <select name="status_pesanan" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="diambil" {{ request('status_pesanan') === 'diambil' ? 'selected' : '' }}>Sudah Diambil</option>
                    <option value="selesai" {{ request('status_pesanan') === 'selesai' ? 'selected' : '' }}>Selesai (Siap)</option>
                    <option value="diproses" {{ request('status_pesanan') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="menunggu" {{ request('status_pesanan') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="dibatalkan" {{ request('status_pesanan') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Pembayaran</option>
                    <option value="lunas" {{ request('status_pembayaran') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="belum_dibayar" {{ request('status_pembayaran') === 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar</option>
                    <option value="menunggu_konfirmasi" {{ request('status_pembayaran') === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.reports') }}" class="p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omzet Terverifikasi</p>
                <h3 class="text-2xl font-black text-emerald-800 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Hanya pesanan berstatus Lunas</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Item Terjual</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalItemTerjual, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">porsi</span></h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Minuman dan makanan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-box-open"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah Transaksi</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalTransaksi, 0, ',', '.') }} <span class="text-sm font-normal text-gray-500">pesanan</span></h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Dalam rentang tanggal terpilih</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold">
                        <th class="py-3.5 px-4">No. Pesanan</th>
                        <th class="py-3.5 px-4">Waktu Masuk</th>
                        <th class="py-3.5 px-4">Meja & Pemesan</th>
                        <th class="py-3.5 px-4">Item Pesanan</th>
                        <th class="py-3.5 px-4">Status Pesanan</th>
                        <th class="py-3.5 px-4">Pembayaran</th>
                        <th class="py-3.5 px-4 text-right">Total Akhir</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pesanans as $p)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3 px-4 font-mono font-bold text-gray-900">{{ $p->nomor_pesanan }}</td>
                        <td class="py-3 px-4 text-gray-500 font-mono">{{ $p->waktu_masuk->format('d/m/Y H:i') }}</td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-gray-800">{{ $p->nama_pelanggan }}</span>
                            <span class="text-gray-400 block text-[11px]">Meja {{ $p->meja->nomor_meja }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="space-y-0.5 max-w-xs">
                                @foreach($p->items as $item)
                                    <p class="text-[11px] text-gray-700 truncate">
                                        <strong class="text-emerald-800">{{ $item->jumlah }}x</strong> {{ $item->nama_menu }}
                                    </p>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold
                                @if($p->status_pesanan === 'selesai' || $p->status_pesanan === 'diambil') bg-emerald-100 text-emerald-800
                                @elseif($p->status_pesanan === 'diproses') bg-blue-100 text-blue-800
                                @elseif($p->status_pesanan === 'menunggu') bg-amber-100 text-amber-800
                                @else bg-red-100 text-red-700 @endif">
                                {{ $p->status_pesanan_label }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold block w-fit
                                {{ $p->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">
                                {{ $p->status_pembayaran_label }}
                            </span>
                            @if($p->metode_pembayaran)
                                <span class="text-[10px] text-gray-400 uppercase mt-0.5 block font-semibold">
                                    {{ $p->metode_pembayaran }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right font-bold text-gray-900">
                            Rp {{ number_format($p->total_akhir, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('kasir.order.receipt', $p->id) }}" target="_blank"
                                class="p-1.5 text-emerald-700 hover:bg-emerald-50 rounded-lg transition" title="Cetak Nota">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-400">Tidak ada riwayat transaksi pada filter tanggal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $pesanans->links() }}
        </div>
    </div>

</div>
@endsection
