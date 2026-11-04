@extends('layouts.admin')

@section('title', 'Tambah Menu Baru - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-plus-circle text-emerald-600"></i>
                <span>Tambah Sajian Baru</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Masukkan informasi menu minuman atau makanan Sekarsa Coffee.</p>
        </div>

        <a href="{{ route('admin.menus.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Menu <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Kopi Susu Sekarsa" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price') }}" required min="0" placeholder="Contoh: 18000" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Deskripsi Sajian</label>
                <textarea name="description" rows="3" placeholder="Jelaskan cita rasa sajian, komposisi, atau catatan penyajian..." class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Status Ketersediaan</label>
                <select name="is_available" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="1" {{ old('is_available', '1') == '1' ? 'selected' : '' }}>Tersedia (Bisa dipesan)</option>
                    <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Habis (Kosong)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Foto Menu (Maks. 2MB)</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md">
                    Simpan Menu Baru
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
