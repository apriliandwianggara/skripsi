@extends('layouts.admin')

@section('title', 'Log Aktivitas Sistem - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Audit Log Aktivitas</h1>
            <p class="text-xs text-gray-500 mt-0.5">Rekam jejak setiap perubahan harga, promo, pemrosesan pesanan, dan transaksi.</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm">
        <form action="{{ route('admin.logs.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas atau nama staf..."
                    class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <select name="aksi" onchange="this.form.submit()" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Kategori Aksi --</option>
                    @foreach($aksiList as $a)
                        <option value="{{ $a }}" {{ request('aksi') === $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition">
                    Cari Log
                </button>
                @if(request('search') || request('aksi'))
                    <a href="{{ route('admin.logs.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold flex items-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold">
                        <th class="py-3.5 px-4">Waktu</th>
                        <th class="py-3.5 px-4">Petugas / Pelaku</th>
                        <th class="py-3.5 px-4">Jenis Aksi</th>
                        <th class="py-3.5 px-4">Keterangan / Rincian Perubahan</th>
                        <th class="py-3.5 px-4">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3 px-4 text-gray-500 font-mono whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                            <span class="block text-[10px] text-gray-400 font-sans">{{ $log->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-gray-900 whitespace-nowrap">
                            {{ $log->user_name }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold font-mono uppercase bg-emerald-100 text-emerald-800">
                                {{ $log->aksi }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-700 leading-snug">
                            {{ $log->deskripsi }}
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-gray-400 whitespace-nowrap">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">Belum ada catatan aktivitas pada sistem.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
