@extends('layouts.admin')

@section('title', 'Manajemen Meja & QR Code - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Meja Pelanggan & QR Code</h1>
            <p class="text-xs text-gray-500 mt-0.5">Generate QR Code untuk ditempel di meja pelanggan guna sistem self-order Sekarsa Coffee.</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.mejas.print_all_qr') }}" target="_blank" class="px-3.5 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Semua QR Meja</span>
            </a>
            <button onclick="openCreateMejaModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                <i class="fa-solid fa-plus"></i>
                <span>Tambah Meja</span>
            </button>
        </div>
    </div>

    <!-- Table Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($mejas as $meja)
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-5 flex flex-col justify-between space-y-4 relative">
            
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Nomor Meja</span>
                    <h3 class="text-3xl font-black font-mono text-emerald-900 mt-0.5">{{ $meja->nomor_meja }}</h3>
                    <p class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-location-dot text-emerald-600 mr-1"></i> {{ $meja->lokasi }}</p>
                    <p class="text-[11px] text-gray-400 mt-0.5"><i class="fa-solid fa-users text-gray-400 mr-1"></i> Kapasitas: {{ $meja->kapasitas }} Orang</p>
                </div>

                <div class="flex flex-col items-end space-y-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                        {{ $meja->status === 'tersedia' ? 'bg-emerald-100 text-emerald-800' : ($meja->status === 'terisi' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ ucfirst($meja->status) }}
                    </span>

                    <div class="flex items-center space-x-1">
                        <button type="button" onclick="openEditMejaModal({{ json_encode($meja) }})" class="p-1 text-gray-400 hover:text-blue-600 transition" title="Ubah">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        <form action="{{ route('admin.mejas.destroy', $meja->id) }}" method="POST" onsubmit="return confirm('Hapus Meja {{ $meja->nomor_meja }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition" title="Hapus">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Active Order on Table if Any -->
            @if($meja->activePesanan)
            <div class="p-2.5 bg-amber-50 rounded-2xl border border-amber-200 text-xs">
                <span class="font-bold text-amber-900">Ada Pesanan Aktif:</span>
                <p class="text-[11px] text-amber-800 font-mono mt-0.5">
                    {{ $meja->activePesanan->nomor_pesanan }} ({{ $meja->activePesanan->status_pesanan }})
                </p>
            </div>
            @endif

            <!-- QR Code Button -->
            <div class="pt-3 border-t border-gray-100 flex items-center space-x-2">
                <a href="{{ route('admin.mejas.qr', $meja->id) }}" target="_blank"
                    class="flex-1 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                    <i class="fa-solid fa-qrcode text-xs"></i>
                    <span>Cetak Standee QR</span>
                </a>

                @if($meja->status !== 'tersedia')
                <form action="{{ route('admin.mejas.reset', $meja->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl transition" title="Reset Menjadi Tersedia">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </button>
                </form>
                @endif
            </div>

        </div>
        @endforeach
    </div>

</div>

<!-- Modal Meja Form -->
<div id="mejaAdminModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-gray-100">
        <h3 class="font-bold text-gray-900 text-base" id="mejaAdminModalTitle">Tambah Meja</h3>
        <p class="text-xs text-gray-500 mt-0.5">Informasi nomor dan kapasitas meja.</p>

        <form id="mejaAdminForm" action="{{ route('admin.mejas.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div id="mejaMethodField"></div>

            <div>
                <label for="admNomorMeja" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nomor Meja <span class="text-red-500">*</span></label>
                <input type="text" name="nomor_meja" id="admNomorMeja" required placeholder="Contoh: 01, VIP-1"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="admKapasitas" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                <input type="number" name="kapasitas" id="admKapasitas" required min="1" max="50" value="4"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="admLokasi" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Lokasi / Area <span class="text-red-500">*</span></label>
                <input type="text" name="lokasi" id="admLokasi" required placeholder="Contoh: Indoor (AC), Outdoor Garden"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div id="mejaStatusContainer" class="hidden">
                <label for="admStatus" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Status Meja</label>
                <select name="status" id="admStatus" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="tersedia">Tersedia</option>
                    <option value="terisi">Terisi (Dine-in)</option>
                    <option value="reservasi">Reservasi</option>
                </select>
            </div>

            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closeMejaAdminModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan Meja
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateMejaModal() {
        document.getElementById('mejaAdminModalTitle').innerText = 'Tambah Meja Baru';
        document.getElementById('mejaAdminForm').action = "{{ route('admin.mejas.store') }}";
        document.getElementById('mejaMethodField').innerHTML = '';
        document.getElementById('admNomorMeja').value = '';
        document.getElementById('admKapasitas').value = '4';
        document.getElementById('admLokasi').value = 'Indoor (AC)';
        document.getElementById('mejaStatusContainer').classList.add('hidden');
        document.getElementById('mejaAdminModal').classList.remove('hidden');
    }

    function openEditMejaModal(meja) {
        document.getElementById('mejaAdminModalTitle').innerText = 'Ubah Meja: ' + meja.nomor_meja;
        document.getElementById('mejaAdminForm').action = "/admin/mejas/" + meja.id;
        document.getElementById('mejaMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('admNomorMeja').value = meja.nomor_meja;
        document.getElementById('admKapasitas').value = meja.kapasitas;
        document.getElementById('admLokasi').value = meja.lokasi;
        document.getElementById('admStatus').value = meja.status;
        document.getElementById('mejaStatusContainer').classList.remove('hidden');
        document.getElementById('mejaAdminModal').classList.remove('hidden');
    }

    function closeMejaAdminModal() {
        document.getElementById('mejaAdminModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
