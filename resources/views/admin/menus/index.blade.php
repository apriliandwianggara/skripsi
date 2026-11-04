@extends('layouts.admin')

@section('title', 'Manajemen Menu - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-utensils text-emerald-600"></i>
                <span>Manajemen Menu Sajian</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Kelola sajian makanan, minuman kopi, non-kopi, harga, dan ketersediaannya.
            </p>
        </div>

        <a href="{{ route('admin.menus.create') }}" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Menu Baru</span>
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.menus.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama menu..."
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <select name="category_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Kategori --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="is_available" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('is_available') === '1' ? 'selected' : '' }}>Tersedia</option>
                    <option value="0" {{ request('is_available') === '0' ? 'selected' : '' }}>Habis (Kosong)</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-semibold transition">
                    <i class="fa-solid fa-filter mr-1"></i> Filter
                </button>
                @if(request('search') || request('category_id') || request('is_available') !== null)
                    <a href="{{ route('admin.menus.index') }}" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Menu Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="p-3.5 w-16">Foto</th>
                        <th class="p-3.5">Nama Menu</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Harga</th>
                        <th class="p-3.5">Ketersediaan</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5">
                                <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" class="w-12 h-12 rounded-xl object-cover bg-emerald-50" onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                            </td>
                            <td class="p-3.5">
                                <span class="font-bold text-gray-900 block text-sm">{{ $menu->name }}</span>
                                <span class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">{{ $menu->description ?: 'Tanpa deskripsi' }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 font-semibold text-[11px]">
                                    {{ $menu->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="p-3.5 font-bold font-mono text-emerald-900 text-sm">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </td>
                            <td class="p-3.5">
                                <form action="{{ route('admin.menus.toggle', $menu) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold transition {{ $menu->is_available ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $menu->is_available ? 'bg-emerald-600' : 'bg-red-600' }}"></span>
                                        <span>{{ $menu->is_available ? 'Tersedia' : 'Habis' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <a href="{{ route('admin.menus.edit', $menu) }}" class="p-2 text-emerald-700 hover:bg-emerald-50 rounded-lg inline-block font-semibold transition" title="Ubah">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus menu sajian ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg font-semibold transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-400">
                                <i class="fa-solid fa-utensils text-4xl mb-2 text-gray-300"></i>
                                <p class="font-semibold text-gray-600 text-xs">Belum ada data menu</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($menus->hasPages())
            <div class="p-4 border-t border-gray-200 bg-gray-50/50">
                {{ $menus->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
