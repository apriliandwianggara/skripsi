@extends('layouts.admin')

@section('title', 'Tambah Menu Baru - Sekarsa Coffee')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center space-x-3">
        <a href="{{ route('admin.menus.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition shadow-sm">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 font-serif">Tambah Menu Baru</h1>
            <p class="text-xs text-gray-500">Tambahkan produk kopi, minuman, atau makanan ke buku menu Sekarsa.</p>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm">
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Nama Menu <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="nama" required value="{{ old('nama') }}" placeholder="Contoh: Kopi Susu Sekarsa"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kategori_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="kategori_id" id="kategori_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status_stok" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Ketersediaan Stok <span class="text-red-500">*</span></label>
                    <select name="status_stok" id="status_stok" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="tersedia" {{ old('status_stok') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="habis" {{ old('status_stok') === 'habis' ? 'selected' : '' }}>Habis (Sold Out)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="harga" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Harga Normal (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga" id="harga" required min="0" value="{{ old('harga') }}" placeholder="22000"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="harga_promo" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Harga Promo (Opsional)</label>
                    <input type="number" name="harga_promo" id="harga_promo" min="0" value="{{ old('harga_promo') }}" placeholder="Kosongkan jika tidak promo"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Deskripsi Singkat Sajian</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" placeholder="Jelaskan cita rasa atau komposisi bahan menu..."
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div>
                <label for="foto" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Foto Menu (Opsional)</label>
                <input type="file" name="foto" id="foto" accept="image/*"
                    class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maks 2MB.</p>
            </div>

            <div class="pt-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_rekomendasi" value="1" {{ old('is_rekomendasi') ? 'checked' : '' }}
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-gray-700">Tampilkan sebagai Menu Rekomendasi / Favorit</span>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                    Simpan Menu
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
