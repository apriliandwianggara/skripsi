@extends('layouts.admin')

@section('title', 'Ubah Menu: ' . $menu->name . ' - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-pen-to-square text-emerald-600"></i>
                <span>Ubah Sajian Menu</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Perbarui detail sajian {{ $menu->name }}.</p>
        </div>

        <a href="{{ route('admin.menus.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Menu <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $menu->name) }}" required class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $menu->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Harga Dasar (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" id="basePriceInput" name="price" value="{{ old('price', $menu->price) }}" min="0" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                    <p class="text-[10px] text-gray-400 mt-1">Jika memakai variasi, harga dasar otomatis mengikuti variasi terendah.</p>
                </div>
            </div>

            <!-- Bagian Variasi Menu (Opsional) -->
            @php
                $hasVars = old('has_variations', $menu->variations->isNotEmpty());
                $currVars = old('variations', $menu->variations->toArray());
            @endphp
            <div class="border border-emerald-100 bg-emerald-50/40 rounded-2xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" id="hasVariationsToggle" name="has_variations" value="1" {{ $hasVars ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <span class="text-xs font-bold text-gray-800">Menu ini memiliki variasi (ukuran/tipe)</span>
                    </label>
                    <span class="text-[11px] text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded font-medium">Opsional</span>
                </div>
                <p class="text-[11px] text-gray-500">
                    Contoh: Americano dengan variasi <strong>S (20.000)</strong>, <strong>M (23.000)</strong>, <strong>L (25.000)</strong>, atau variasi Panas / Dingin.
                </p>

                <div id="variationsWrapper" class="{{ $hasVars ? '' : 'hidden' }} space-y-3 pt-2 border-t border-emerald-100">
                    <div id="variationRows" class="space-y-2">
                        @if(!empty($currVars))
                            @foreach($currVars as $idx => $v)
                                <div class="variation-row flex items-center space-x-2 bg-white p-2.5 rounded-xl border border-gray-200 shadow-sm">
                                    <div class="flex-1">
                                        <input type="text" name="variations[{{ $idx }}][name]" value="{{ $v['name'] ?? '' }}" placeholder="Nama Variasi (contoh: S / M / L)" class="w-full text-xs p-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                            <span class="absolute left-2.5 top-2 text-[11px] text-gray-400">Rp</span>
                                            <input type="number" name="variations[{{ $idx }}][price]" value="{{ isset($v['price']) ? (int)$v['price'] : '' }}" min="0" placeholder="Harga" class="w-full text-xs pl-8 pr-2 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono">
                                        </div>
                                    </div>
                                    <button type="button" onclick="removeVariationRow(this)" class="p-2 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition" title="Hapus Variasi">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="variation-row flex items-center space-x-2 bg-white p-2.5 rounded-xl border border-gray-200 shadow-sm">
                                <div class="flex-1">
                                    <input type="text" name="variations[0][name]" value="S" placeholder="Nama Variasi" class="w-full text-xs p-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                </div>
                                <div class="flex-1">
                                    <div class="relative">
                                        <span class="absolute left-2.5 top-2 text-[11px] text-gray-400">Rp</span>
                                        <input type="number" name="variations[0][price]" value="{{ (int)$menu->price }}" min="0" placeholder="Harga" class="w-full text-xs pl-8 pr-2 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono">
                                    </div>
                                </div>
                                <button type="button" onclick="removeVariationRow(this)" class="p-2 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition" title="Hapus Variasi">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        @endif
                    </div>

                    <button type="button" onclick="addVariationRow()" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Tambah Variasi Lain</span>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Deskripsi Sajian</label>
                <textarea name="description" rows="3" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('description', $menu->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Status Ketersediaan</label>
                <select name="is_available" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="1" {{ old('is_available', $menu->is_available) ? 'selected' : '' }}>Tersedia (Bisa dipesan)</option>
                    <option value="0" {{ !old('is_available', $menu->is_available) ? 'selected' : '' }}>Habis (Kosong)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Ganti Foto Menu (Opsional)</label>
                @if($menu->image)
                    <div class="mb-2 flex items-center space-x-3 p-2 bg-gray-50 rounded-xl border border-gray-200">
                        <img src="{{ $menu->image_url }}" class="w-12 h-12 rounded-lg object-cover" onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                        <span class="text-xs text-gray-500">Foto saat ini tersimpan</span>
                    </div>
                @endif
                <input type="file" name="image" accept="image/*" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.menus.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md">
                    Perbarui Menu
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    const toggle = document.getElementById('hasVariationsToggle');
    const wrapper = document.getElementById('variationsWrapper');
    const rowsContainer = document.getElementById('variationRows');

    toggle.addEventListener('change', function() {
        if (this.checked) {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    });

    let varIndex = {{ !empty($currVars) ? count($currVars) : 2 }};

    function addVariationRow() {
        const row = document.createElement('div');
        row.className = 'variation-row flex items-center space-x-2 bg-white p-2.5 rounded-xl border border-gray-200 shadow-sm animate-fade-in';
        row.innerHTML = `
            <div class="flex-1">
                <input type="text" name="variations[${varIndex}][name]" placeholder="Nama Variasi (contoh: XL)" class="w-full text-xs p-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>
            <div class="flex-1">
                <div class="relative">
                    <span class="absolute left-2.5 top-2 text-[11px] text-gray-400">Rp</span>
                    <input type="number" name="variations[${varIndex}][price]" min="0" placeholder="Harga" class="w-full text-xs pl-8 pr-2 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono">
                </div>
            </div>
            <button type="button" onclick="removeVariationRow(this)" class="p-2 text-red-500 hover:text-red-700 rounded-lg hover:bg-red-50 transition" title="Hapus Variasi">
                <i class="fa-solid fa-trash-can text-xs"></i>
            </button>
        `;
        rowsContainer.appendChild(row);
        varIndex++;
    }

    function removeVariationRow(btn) {
        const row = btn.closest('.variation-row');
        if (rowsContainer.querySelectorAll('.variation-row').length > 1) {
            row.remove();
        } else {
            row.querySelectorAll('input').forEach(i => i.value = '');
        }
    }
</script>
@endsection
