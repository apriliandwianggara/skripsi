@extends('layouts.admin')

@section('title', 'Kelola Menu & Harga - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Katalog Menu & Harga</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola daftar sajian makanan & minuman, harga, stok, dan promo.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.menus.create') }}" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Menu Baru</span>
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..."
                    class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <select name="kategori_id" onchange="this.form.submit()" class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="flex space-x-2">
                <select name="status_stok" onchange="this.form.submit()" class="flex-1 px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status Stok --</option>
                    <option value="tersedia" {{ request('status_stok') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ request('status_stok') === 'habis' ? 'selected' : '' }}>Habis (Sold Out)</option>
                </select>
                @if(request('search') || request('kategori_id') || request('status_stok'))
                    <a href="{{ route('admin.menus.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold flex items-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Menu Table -->
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold">
                        <th class="py-3.5 px-4">Menu & Foto</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Harga Normal</th>
                        <th class="py-3.5 px-4">Harga Promo</th>
                        <th class="py-3.5 px-4">Status Stok</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($menus as $menu)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama }}" class="w-12 h-12 rounded-xl object-cover bg-gray-100 shrink-0">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $menu->nama }}</h4>
                                    <p class="text-[11px] text-gray-400 line-clamp-1 max-w-xs">{{ $menu->deskripsi }}</p>
                                    @if($menu->is_rekomendasi)
                                        <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold">Favorit Rekomendasi</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-semibold text-[11px]">
                                {{ $menu->kategori->nama ?? '-' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-bold text-gray-800">
                            Rp {{ number_format($menu->harga, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4">
                            @if($menu->harga_promo && $menu->harga_promo < $menu->harga)
                                <span class="font-bold text-emerald-800">Rp {{ number_format($menu->harga_promo, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <form action="{{ route('admin.menus.toggle_stock', $menu->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 rounded-full text-[11px] font-bold transition flex items-center space-x-1
                                    {{ $menu->status_stok === 'tersedia' ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $menu->status_stok === 'tersedia' ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                    <span>{{ ucfirst($menu->status_stok) }}</span>
                                </button>
                            </form>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                <a href="{{ route('admin.menus.edit', $menu->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Ubah Menu">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu {{ $menu->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus Menu">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400">Tidak ada menu yang sesuai dengan filter pencarian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{ $menus->links() }}
        </div>
    </div>

</div>
@endsection
