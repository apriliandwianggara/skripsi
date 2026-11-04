@extends('layouts.admin')

@section('title', 'Kelola Kode Promo - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Kode Promo & Diskon</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola kupon potongan harga untuk menarik pelanggan dan promosi kafe.</p>
        </div>
        <button onclick="openCreatePromoModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5 self-start">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Promo Baru</span>
        </button>
    </div>

    <!-- Promo Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($promos as $promo)
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-mono font-bold text-xs">
                            {{ $promo->kode_promo }}
                        </span>
                        <h3 class="font-bold text-gray-900 text-base mt-2">{{ $promo->judul }}</h3>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button type="button" onclick="openEditPromoModal({{ json_encode($promo) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Ubah">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        <form action="{{ route('admin.promos.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Hapus promo {{ $promo->kode_promo }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <p class="text-xs text-gray-600 leading-relaxed">{{ $promo->deskripsi ?: '-' }}</p>

                <div class="p-3 bg-gray-50 rounded-2xl border border-gray-100 space-y-1 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Nilai Potongan</span>
                        <strong class="text-emerald-800">
                            @if($promo->tipe === 'persen')
                                {{ (int)$promo->nilai_potongan }}%
                                @if($promo->maksimal_potongan)
                                    (Maks Rp {{ number_format($promo->maksimal_potongan, 0, ',', '.') }})
                                @endif
                            @else
                                Rp {{ number_format($promo->nilai_potongan, 0, ',', '.') }}
                            @endif
                        </strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Min. Pembelian</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format($promo->min_pembelian, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Masa Berlaku</span>
                        <span class="text-gray-600">
                            {{ $promo->tgl_selesai ? $promo->tgl_selesai->format('d M Y') : 'Tanpa Batas' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Digunakan: <strong>{{ $promo->pesanans_count }} kali</strong></span>
                <span class="inline-flex items-center space-x-1 text-emerald-700 font-semibold">
                    <span class="w-2 h-2 rounded-full {{ $promo->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    <span>{{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </span>
            </div>
        </div>
        @endforeach
    </div>

</div>

<!-- Modal Promo Form -->
<div id="promoModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-gray-900 text-base" id="promoModalTitle">Tambah Kode Promo</h3>
        <p class="text-xs text-gray-500 mt-0.5">Kupon diskon yang dapat dimasukkan pelanggan saat memesan.</p>

        <form id="promoForm" action="{{ route('admin.promos.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div id="promoMethodField"></div>

            <div>
                <label for="promoKode" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Kupon <span class="text-red-500">*</span></label>
                <input type="text" name="kode_promo" id="promoKode" required placeholder="Contoh: NGOPIASIK"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs uppercase font-mono font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="promoJudul" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Judul Promo <span class="text-red-500">*</span></label>
                <input type="text" name="judul" id="promoJudul" required placeholder="Contoh: Diskon 20% Akhir Pekan"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="promoTipe" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tipe Diskon</label>
                    <select name="tipe" id="promoTipe" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="persen">Persentase (%)</option>
                        <option value="nominal">Nominal Tunai (Rp)</option>
                    </select>
                </div>
                <div>
                    <label for="promoNilai" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nilai Potongan <span class="text-red-500">*</span></label>
                    <input type="number" name="nilai_potongan" id="promoNilai" required min="1" placeholder="15 atau 5000"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="promoMin" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Min. Belanja (Rp)</label>
                    <input type="number" name="min_pembelian" id="promoMin" min="0" value="0"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="promoMax" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Maks. Potongan (Rp)</label>
                    <input type="number" name="maksimal_potongan" id="promoMax" min="0" placeholder="Opsional"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label for="promoDeskripsi" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Keterangan Syarat & Ketentuan</label>
                <textarea name="deskripsi" id="promoDeskripsi" rows="2" placeholder="Ketentuan pemakaian..."
                    class="w-full px-3.5 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closePromoModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan Promo
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreatePromoModal() {
        document.getElementById('promoModalTitle').innerText = 'Tambah Kode Promo Baru';
        document.getElementById('promoForm').action = "{{ route('admin.promos.store') }}";
        document.getElementById('promoMethodField').innerHTML = '';
        document.getElementById('promoKode').value = '';
        document.getElementById('promoJudul').value = '';
        document.getElementById('promoTipe').value = 'persen';
        document.getElementById('promoNilai').value = '';
        document.getElementById('promoMin').value = '0';
        document.getElementById('promoMax').value = '';
        document.getElementById('promoDeskripsi').value = '';
        document.getElementById('promoModal').classList.remove('hidden');
    }

    function openEditPromoModal(promo) {
        document.getElementById('promoModalTitle').innerText = 'Ubah Promo: ' + promo.kode_promo;
        document.getElementById('promoForm').action = "/kelola/promos/" + promo.id;
        document.getElementById('promoMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('promoKode').value = promo.kode_promo;
        document.getElementById('promoJudul').value = promo.judul;
        document.getElementById('promoTipe').value = promo.tipe;
        document.getElementById('promoNilai').value = Math.floor(promo.nilai_potongan);
        document.getElementById('promoMin').value = Math.floor(promo.min_pembelian);
        document.getElementById('promoMax').value = promo.maksimal_potongan ? Math.floor(promo.maksimal_potongan) : '';
        document.getElementById('promoDeskripsi').value = promo.deskripsi || '';
        document.getElementById('promoModal').classList.remove('hidden');
    }

    function closePromoModal() {
        document.getElementById('promoModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
