@extends('layouts.admin')

@section('title', 'Kategori Menu - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-layer-group text-emerald-600"></i>
                <span>Kategori Menu</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Kelola kelompok sajian minuman dan makanan Sekarsa Coffee.
            </p>
        </div>

        <button onclick="openCreateModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Kategori Baru</span>
        </button>
    </div>

    <!-- Category Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($categories as $cat)
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-{{ $cat->icon ?: 'coffee' }}"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            {{ $cat->menus_count }} Menu
                        </span>
                    </div>

                    <h3 class="font-bold text-base text-gray-900">{{ $cat->name }}</h3>
                    <p class="text-xs text-gray-400 mt-1 line-clamp-2">
                        {{ $cat->description ?: 'Belum ada deskripsi untuk kategori ini.' }}
                    </p>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-[11px] text-gray-400 font-mono">Urutan: #{{ $cat->order }}</span>

                    <div class="flex items-center space-x-1">
                        <button onclick="openEditModal({{ json_encode($cat) }})" class="p-2 text-emerald-700 hover:bg-emerald-50 rounded-lg text-xs font-semibold transition" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg text-xs font-semibold transition" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-4 bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400">
                <i class="fa-solid fa-layer-group text-4xl mb-2 text-gray-300"></i>
                <p class="font-semibold text-gray-600 text-sm">Belum ada kategori</p>
                <p class="text-xs text-gray-400 mt-0.5">Tambahkan kategori menu seperti Kopi, Non-Kopi, Makanan Ringan, dll.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- Modal Tambah / Edit Kategori -->
<div id="categoryModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-bold text-base text-gray-900" id="modalTitle">Tambah Kategori Menu</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="categoryForm" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div id="methodContainer"></div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="catName" required placeholder="Contoh: Signature Coffee" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Ikon FontAwesome (opsional)</label>
                    <input type="text" name="icon" id="catIcon" placeholder="Contoh: mug-hot, cup-togo, bowl-food, cookie" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[10px] text-gray-400 mt-1 block">Tulis nama ikon tanpa awalan 'fa-', contoh: <code>mug-hot</code></span>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Deskripsi (opsional)</label>
                    <textarea name="description" id="catDesc" rows="3" placeholder="Keterangan singkat kategori sajian" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>
            </div>

            <div class="flex items-center space-x-2 mt-6">
                <button type="button" onclick="closeModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Kategori Menu';
        document.getElementById('categoryForm').action = "{{ route('admin.categories.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('catName').value = '';
        document.getElementById('catIcon').value = 'mug-hot';
        document.getElementById('catDesc').value = '';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function openEditModal(cat) {
        document.getElementById('modalTitle').innerText = 'Ubah Kategori Menu';
        document.getElementById('categoryForm').action = `/admin/categories/${cat.id}`;
        document.getElementById('methodContainer').innerHTML = '@method("PUT")';
        document.getElementById('catName').value = cat.name;
        document.getElementById('catIcon').value = cat.icon || 'mug-hot';
        document.getElementById('catDesc').value = cat.description || '';
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }
</script>
@endpush
