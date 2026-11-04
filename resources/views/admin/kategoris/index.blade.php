@extends('layouts.admin')

@section('title', 'Kategori Menu - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Kategori Menu</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelompokkan produk kopi dan hidangan untuk navigasi pelanggan yang rapi.</p>
        </div>
        <button onclick="openCreateKategoriModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5 self-start">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Category Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($kategoris as $kategori)
        <div class="bg-white p-5 rounded-3xl border border-gray-200/80 shadow-sm flex flex-col justify-between space-y-4">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-{{ $kategori->icon ?: 'coffee' }}"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base leading-tight">{{ $kategori->nama }}</h3>
                        <span class="text-[11px] font-semibold text-emerald-700">{{ $kategori->menus_count }} Menu Aktif</span>
                    </div>
                </div>

                <div class="flex items-center space-x-1">
                    <button type="button" onclick="openEditKategoriModal({{ json_encode($kategori) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Ubah">
                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                    </button>
                    @if($kategori->menus_count === 0)
                    <form action="{{ route('admin.kategoris.destroy', $kategori->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $kategori->nama }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <p class="text-xs text-gray-500 leading-relaxed">{{ $kategori->deskripsi ?: 'Tidak ada deskripsi.' }}</p>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                <span>Slug: <code class="text-emerald-700 font-mono">{{ $kategori->slug }}</code></span>
                <span class="w-2 h-2 rounded-full {{ $kategori->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Modal Create / Edit Kategori -->
<div id="kategoriModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
        <h3 class="font-bold text-gray-900 text-base" id="katModalTitle">Tambah Kategori Baru</h3>
        <p class="text-xs text-gray-500 mt-0.5">Kelompok menu untuk kemudahan pemesanan pelanggan.</p>

        <form id="katForm" action="{{ route('admin.kategoris.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div id="katMethodField"></div>

            <div>
                <label for="katNama" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama" id="katNama" required placeholder="Contoh: Signature Coffee"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="katIcon" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Icon FontAwesome (Opsional)</label>
                <input type="text" name="icon" id="katIcon" placeholder="coffee, mug-hot, cookie, utensils, droplet..."
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="katDeskripsi" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Deskripsi Singkat</label>
                <textarea name="deskripsi" id="katDeskripsi" rows="2" placeholder="Keterangan singkat kategori..."
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closeKategoriModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateKategoriModal() {
        document.getElementById('katModalTitle').innerText = 'Tambah Kategori Baru';
        document.getElementById('katForm').action = "{{ route('admin.kategoris.store') }}";
        document.getElementById('katMethodField').innerHTML = '';
        document.getElementById('katNama').value = '';
        document.getElementById('katIcon').value = 'coffee';
        document.getElementById('katDeskripsi').value = '';
        document.getElementById('kategoriModal').classList.remove('hidden');
    }

    function openEditKategoriModal(kat) {
        document.getElementById('katModalTitle').innerText = 'Ubah Kategori: ' + kat.nama;
        document.getElementById('katForm').action = "/kelola/kategoris/" + kat.id;
        document.getElementById('katMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('katNama').value = kat.nama;
        document.getElementById('katIcon').value = kat.icon || 'coffee';
        document.getElementById('katDeskripsi').value = kat.deskripsi || '';
        document.getElementById('kategoriModal').classList.remove('hidden');
    }

    function closeKategoriModal() {
        document.getElementById('kategoriModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
