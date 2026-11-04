@extends('layouts.app')

@section('title', 'Menu & Pemesanan - ' . ($pengaturan->nama_cafe ?? 'Sekarsa Coffee'))

@section('content')
<div class="pb-28 min-h-screen bg-[#fcfdfc]">

    <!-- Top Sticky Branding & Table Header -->
    <header class="sticky top-0 z-30 glass-header text-white px-4 py-3.5 shadow-md">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-emerald-300 shadow">
                    <i class="fa-solid fa-mug-hot text-base"></i>
                </div>
                <div>
                    <h1 class="font-serif font-bold text-base tracking-wide leading-tight">{{ $pengaturan->nama_cafe ?? 'SEKARSA COFFEE' }}</h1>
                    <p class="text-[11px] text-emerald-200">Pemesanan Mandiri Meja &bull; FIFO Queue</p>
                </div>
            </div>

            <!-- Table Indicator Pill -->
            <div>
                @if($activeMeja)
                    <button onclick="openMejaModal()" class="inline-flex items-center space-x-1.5 bg-emerald-800/90 hover:bg-emerald-700/90 text-white text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-500/50 transition shadow-sm">
                        <i class="fa-solid fa-chair text-emerald-300"></i>
                        <span>Meja <strong>{{ $activeMeja->nomor_meja }}</strong></span>
                        <i class="fa-solid fa-pen text-[10px] text-emerald-300 ml-1"></i>
                    </button>
                @else
                    <button onclick="openMejaModal()" class="inline-flex items-center space-x-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm animate-bounce">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>Pilih Nomor Meja</span>
                    </button>
                @endif
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 pt-4 space-y-5">

        <!-- Banner Alert jika Meja Belum Dipilih -->
        @if(!$activeMeja)
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-white p-4 rounded-2xl shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i class="fa-solid fa-qrcode text-2xl"></i>
                <div>
                    <p class="font-bold text-sm">Anda belum memilih nomor meja!</p>
                    <p class="text-xs text-amber-100">Silakan scan QR code di meja Anda atau pilih nomor meja secara manual.</p>
                </div>
            </div>
            <button onclick="openMejaModal()" class="px-3.5 py-1.5 bg-white text-amber-800 font-bold text-xs rounded-xl hover:bg-amber-50 transition shrink-0">
                Pilih Meja
            </button>
        </div>
        @endif

        <!-- Active Promos Carousel / Cards -->
        @if($promos->count() > 0)
        <div>
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-900 flex items-center">
                    <i class="fa-solid fa-fire text-amber-500 mr-1.5"></i> Promo Spesial Hari Ini
                </h2>
                <span class="text-[11px] text-gray-500">Gunakan saat checkout</span>
            </div>
            <div class="flex space-x-3 overflow-x-auto scrollbar-hide py-1">
                @foreach($promos as $promo)
                <div class="shrink-0 w-72 bg-gradient-to-r from-emerald-800 to-sekarsa-900 text-white p-3.5 rounded-2xl shadow-sm relative overflow-hidden border border-emerald-700/60 flex flex-col justify-between">
                    <div class="absolute -right-4 -bottom-4 w-20 h-20 rounded-full bg-emerald-600/20 pointer-events-none"></div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="bg-amber-400 text-gray-900 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md">
                                @if($promo->tipe === 'persen')
                                    Diskon {{ (int)$promo->nilai_potongan }}%
                                @else
                                    Potongan Rp {{ number_format($promo->nilai_potongan, 0, ',', '.') }}
                                @endif
                            </span>
                            <span class="text-[11px] text-emerald-300 font-mono font-semibold">{{ $promo->kode_promo }}</span>
                        </div>
                        <h3 class="font-bold text-sm leading-tight text-white line-clamp-1">{{ $promo->judul }}</h3>
                        <p class="text-[11px] text-emerald-100/90 mt-1 line-clamp-2">{{ $promo->deskripsi }}</p>
                    </div>
                    <div class="mt-3 pt-2 border-t border-emerald-700/50 flex items-center justify-between text-[11px]">
                        <span class="text-emerald-200">Min. Rp {{ number_format($promo->min_pembelian, 0, ',', '.') }}</span>
                        <button type="button" onclick="copyPromoCode('{{ $promo->kode_promo }}')" class="text-amber-300 hover:text-white font-bold flex items-center">
                            <i class="fa-regular fa-copy mr-1"></i> Salin Kode
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Search & Filter Controls -->
        <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('customer.order') }}" method="GET" class="flex gap-2">
                @if(request('meja'))
                    <input type="hidden" name="meja" value="{{ request('meja') }}">
                @endif
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </div>
                    <input type="text" name="q" value="{{ $search }}" placeholder="Cari kopi, makanan, snack favorit..."
                        class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-semibold transition">
                    Cari
                </button>
                @if($search)
                    <a href="{{ route('customer.order', array_filter(['meja' => request('meja'), 'kategori' => request('kategori')])) }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold flex items-center">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </form>
        </div>

        <!-- Category Navigation Tabs (Sticky Pills) -->
        <div class="sticky top-14 z-20 bg-[#fcfdfc]/95 backdrop-blur-md py-2 -mx-4 px-4 border-b border-gray-100">
            <div class="flex space-x-2 overflow-x-auto scrollbar-hide">
                <a href="{{ route('customer.order', array_filter(['meja' => request('meja'), 'q' => request('q')])) }}"
                    class="shrink-0 px-4 py-2 rounded-full text-xs font-bold transition flex items-center space-x-1.5 {{ empty($selectedKategori) ? 'bg-emerald-800 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                    <i class="fa-solid fa-border-all"></i>
                    <span>Semua Menu</span>
                </a>
                @foreach($kategoris as $kat)
                <a href="{{ route('customer.order', array_filter(['meja' => request('meja'), 'kategori' => $kat->slug, 'q' => request('q')])) }}"
                    class="shrink-0 px-4 py-2 rounded-full text-xs font-bold transition flex items-center space-x-1.5 {{ $selectedKategori === $kat->slug ? 'bg-emerald-800 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                    <i class="fa-solid fa-{{ $kat->icon ?? 'mug-hot' }}"></i>
                    <span>{{ $kat->nama }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <!-- Menu Grid -->
        @if($menus->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-mug-saucer"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800">Menu Tidak Ditemukan</h3>
            <p class="text-xs text-gray-500 mt-1">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
            @foreach($menus as $menu)
            <div class="bg-white rounded-2xl p-3.5 shadow-sm border border-gray-100/80 hover:border-emerald-200 hover:shadow-md transition flex flex-col justify-between relative group">
                
                @if($menu->is_rekomendasi)
                <span class="absolute top-3 left-3 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm z-10 flex items-center">
                    <i class="fa-solid fa-star text-[9px] mr-1"></i> Favorit
                </span>
                @endif

                <div class="flex space-x-3">
                    <!-- Photo -->
                    <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 shrink-0 relative">
                        <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <span class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">{{ $menu->kategori->nama ?? 'Menu' }}</span>
                        <h3 class="font-bold text-gray-900 text-sm leading-snug line-clamp-1 mt-0.5">{{ $menu->nama }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-1">{{ $menu->deskripsi }}</p>
                    </div>
                </div>

                <!-- Price and Action -->
                <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        @if($menu->harga_promo && $menu->harga_promo < $menu->harga)
                            <div class="flex items-baseline space-x-1.5">
                                <span class="text-sm font-bold text-emerald-800">Rp {{ number_format($menu->harga_promo, 0, ',', '.') }}</span>
                                <span class="text-[11px] text-gray-400 line-through">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <span class="text-sm font-bold text-emerald-800">Rp {{ number_format($menu->harga, 0, ',', '.') }}</span>
                        @endif
                    </div>

                    <button type="button" onclick="openItemModal({{ json_encode($menu) }})"
                        class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white rounded-xl text-xs font-bold flex items-center space-x-1 shadow-sm transition">
                        <i class="fa-solid fa-plus text-[10px]"></i>
                        <span>Pesan</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </main>

    <!-- Floating Sticky Cart Bottom Bar -->
    <div id="floatingCart" class="fixed bottom-4 inset-x-4 max-w-xl mx-auto z-40 hidden">
        <div class="bg-gradient-to-r from-sekarsa-900 to-emerald-800 text-white rounded-2xl p-3.5 shadow-2xl flex items-center justify-between border border-emerald-600/50 backdrop-blur-md">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center relative font-bold text-emerald-200">
                    <i class="fa-solid fa-bag-shopping text-lg"></i>
                    <span id="cartCountBadge" class="absolute -top-1.5 -right-1.5 bg-amber-500 text-gray-900 text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center shadow">0</span>
                </div>
                <div>
                    <p class="text-[11px] text-emerald-200 uppercase font-semibold">Total Pesanan</p>
                    <p class="text-base font-black text-white" id="cartTotalSummary">Rp 0</p>
                </div>
            </div>

            <button type="button" onclick="openCheckoutModal()" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-300 text-gray-900 font-extrabold text-xs rounded-xl shadow-lg transition flex items-center space-x-1.5">
                <span>Lihat Keranjang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </div>

</div>

<!-- Modal: Pilih Nomor Meja -->
<div id="mejaModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-chair text-emerald-700 text-lg"></i>
                <h3 class="font-bold text-gray-800 text-base">Pilih Nomor Meja Anda</h3>
            </div>
            <button onclick="closeMejaModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <p class="text-xs text-gray-500 my-3">Pilih meja tempat Anda duduk untuk pesanan ini:</p>

        <form action="{{ route('customer.set_meja') }}" method="POST">
            @csrf
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-64 overflow-y-auto pr-1 py-1">
                @foreach($allMejas as $m)
                <label class="cursor-pointer">
                    <input type="radio" name="meja_id" value="{{ $m->id }}" class="peer sr-only" {{ ($activeMeja && $activeMeja->id === $m->id) ? 'checked' : '' }}>
                    <div class="p-3 rounded-2xl border-2 border-gray-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/70 hover:border-gray-300 transition text-center">
                        <span class="text-xs font-bold text-gray-500 block uppercase">Meja</span>
                        <span class="text-xl font-black text-gray-800 peer-checked:text-emerald-800 block">{{ $m->nomor_meja }}</span>
                        <span class="text-[10px] text-gray-400 block truncate">{{ $m->lokasi }}</span>
                    </div>
                </label>
                @endforeach
            </div>

            <div class="mt-5 flex space-x-2">
                <button type="button" onclick="closeMejaModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow transition">
                    Konfirmasi Meja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Custom Item & Catatan Pesanan -->
<div id="itemModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl border border-gray-100 flex flex-col max-h-[90vh]">
        <div class="relative h-44 bg-gray-100">
            <img id="modalItemImg" src="" alt="Menu" class="w-full h-full object-cover">
            <button onclick="closeItemModal()" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center hover:bg-black/70">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-4">
            <div>
                <span id="modalItemKat" class="text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Kategori</span>
                <h3 id="modalItemName" class="text-lg font-bold text-gray-900 leading-tight">Nama Menu</h3>
                <p id="modalItemDesc" class="text-xs text-gray-500 mt-1">Deskripsi menu</p>
                <p id="modalItemPrice" class="text-base font-extrabold text-emerald-800 mt-2">Rp 0</p>
            </div>

            <!-- Notes -->
            <div>
                <label for="itemNotes" class="block text-xs font-bold text-gray-700 mb-1">Catatan Tambahan (Opsional)</label>
                <input type="text" id="itemNotes" placeholder="Contoh: Less ice, gula sedikit, pisah saus..."
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Quantity Stepper -->
            <div class="flex items-center justify-between pt-2">
                <span class="text-xs font-bold text-gray-700">Jumlah Pesan</span>
                <div class="flex items-center space-x-3 bg-gray-100 px-3 py-1.5 rounded-xl border border-gray-200">
                    <button type="button" onclick="decrementQty()" class="w-7 h-7 rounded-lg bg-white shadow-sm flex items-center justify-center text-gray-700 hover:bg-gray-50 active:scale-95 font-bold text-xs">
                        <i class="fa-solid fa-minus"></i>
                    </button>
                    <span id="itemQty" class="text-sm font-extrabold text-gray-800 w-5 text-center">1</span>
                    <button type="button" onclick="incrementQty()" class="w-7 h-7 rounded-lg bg-emerald-700 text-white shadow-sm flex items-center justify-center hover:bg-emerald-800 active:scale-95 font-bold text-xs">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <button type="button" onclick="confirmAddToCart()" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-cart-plus"></i>
                <span id="btnAddItemText">Tambahkan ke Pesanan</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal: Checkout & Konfirmasi Pesanan -->
<div id="checkoutModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl border border-gray-100 flex flex-col max-h-[92vh]">
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-sekarsa-900 to-emerald-800 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-receipt text-amber-300"></i>
                <div>
                    <h3 class="font-bold text-sm">Konfirmasi Pesanan Anda</h3>
                    <p class="text-[11px] text-emerald-200">Meja: <strong>{{ $activeMeja ? $activeMeja->nomor_meja : 'Belum Dipilih' }}</strong></p>
                </div>
            </div>
            <button onclick="closeCheckoutModal()" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="p-5 overflow-y-auto space-y-4 flex-1">
            
            <!-- Customer Name Input -->
            <div>
                <label for="customerName" class="block text-xs font-bold text-gray-700 mb-1">
                    Nama Pemesan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="customerName" placeholder="Masukkan nama panggilan Anda"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 font-medium">
            </div>

            <!-- Cart Items List -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Item yang Dipesan</h4>
                <div id="checkoutItemsContainer" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                    <!-- Populated by JS -->
                </div>
            </div>

            <!-- Promo Code Application -->
            <div class="bg-emerald-50/70 p-3 rounded-2xl border border-emerald-100">
                <label for="promoInput" class="block text-xs font-bold text-emerald-950 mb-1">Kode Promo Diskon</label>
                <div class="flex space-x-2">
                    <input type="text" id="promoInput" placeholder="Contoh: SEKARSA15"
                        class="flex-1 px-3 py-1.5 bg-white border border-emerald-200 rounded-xl text-xs uppercase font-mono font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button type="button" onclick="applyPromoCode()" class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition">
                        Gunakan
                    </button>
                </div>
                <p id="promoMessage" class="text-[11px] mt-1.5 hidden"></p>
            </div>

            <!-- General Notes -->
            <div>
                <label for="generalNotes" class="block text-xs font-bold text-gray-700 mb-1">Catatan Pesanan Keseluruhan</label>
                <textarea id="generalNotes" rows="2" placeholder="Catatan untuk barista / kasir..."
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <!-- Summary Calculations -->
            <div class="bg-gray-50 p-3.5 rounded-2xl border border-gray-100 space-y-1.5 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal Menu</span>
                    <span id="summarySubtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-semibold" id="summaryPromoRow" style="display: none;">
                    <span>Potongan Promo (<span id="summaryPromoCode"></span>)</span>
                    <span id="summaryDiscount">- Rp 0</span>
                </div>
                <div class="pt-2 border-t border-gray-200 flex justify-between text-sm font-extrabold text-gray-900">
                    <span>Total Pembayaran</span>
                    <span class="text-emerald-800 text-base" id="summaryGrandTotal">Rp 0</span>
                </div>
            </div>

            <!-- Notice Alur Pembayaran -->
            <div class="bg-amber-50 p-3 rounded-xl border border-amber-200 flex items-start space-x-2 text-[11px] text-amber-900">
                <i class="fa-solid fa-circle-info text-amber-600 mt-0.5"></i>
                <div>
                    <p class="font-bold">Info Pembayaran:</p>
                    <p>Pembayaran (QRIS atau Tunai) diselesaikan di kasir saat mengambil pesanan yang sudah selesai diracik.</p>
                </div>
            </div>

        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-100">
            <button type="button" id="btnSubmitOrder" onclick="processSubmitOrder()"
                class="w-full py-3.5 bg-emerald-700 hover:bg-emerald-800 active:scale-98 text-white font-bold rounded-xl text-xs shadow-lg shadow-emerald-700/30 transition flex items-center justify-center space-x-2">
                <i class="fa-solid fa-paper-plane"></i>
                <span>Kirim Pesanan Sekarang (Masuk FIFO)</span>
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // State Keranjang di LocalStorage
    let cart = JSON.parse(localStorage.getItem('sekarsa_cart') || '[]');
    let currentModalItem = null;
    let currentQty = 1;
    let appliedPromo = null;

    const currentMejaId = {{ $activeMeja ? $activeMeja->id : 'null' }};
    const currentMejaNomor = "{{ $activeMeja ? $activeMeja->nomor_meja : '' }}";

    document.addEventListener('DOMContentLoaded', function() {
        renderCartUI();
    });

    function copyPromoCode(code) {
        navigator.clipboard.writeText(code).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Kode ' + code + ' disalin!',
                showConfirmButton: false,
                timer: 1500
            });
            document.getElementById('promoInput').value = code;
        });
    }

    // Modal Item
    function openItemModal(menu) {
        currentModalItem = menu;
        currentQty = 1;
        document.getElementById('modalItemImg').src = menu.foto_url;
        document.getElementById('modalItemKat').innerText = menu.kategori ? menu.kategori.nama : 'Menu';
        document.getElementById('modalItemName').innerText = menu.nama;
        document.getElementById('modalItemDesc').innerText = menu.deskripsi || '-';
        
        const price = menu.harga_promo && menu.harga_promo < menu.harga ? menu.harga_promo : menu.harga;
        document.getElementById('modalItemPrice').innerText = 'Rp ' + Number(price).toLocaleString('id-ID');
        document.getElementById('itemQty').innerText = currentQty;
        document.getElementById('itemNotes').value = '';

        document.getElementById('itemModal').classList.remove('hidden');
    }

    function closeItemModal() {
        document.getElementById('itemModal').classList.add('hidden');
    }

    function incrementQty() {
        currentQty++;
        document.getElementById('itemQty').innerText = currentQty;
    }

    function decrementQty() {
        if (currentQty > 1) {
            currentQty--;
            document.getElementById('itemQty').innerText = currentQty;
        }
    }

    function confirmAddToCart() {
        if (!currentModalItem) return;

        const price = currentModalItem.harga_promo && currentModalItem.harga_promo < currentModalItem.harga 
            ? currentModalItem.harga_promo 
            : currentModalItem.harga;

        const notes = document.getElementById('itemNotes').value.trim();

        // Check if item with same ID and same notes exists
        const existingIndex = cart.findIndex(item => item.id === currentModalItem.id && item.notes === notes);

        if (existingIndex > -1) {
            cart[existingIndex].qty += currentQty;
        } else {
            cart.push({
                id: currentModalItem.id,
                name: currentModalItem.nama,
                price: parseFloat(price),
                qty: currentQty,
                notes: notes,
                foto_url: currentModalItem.foto_url
            });
        }

        saveCart();
        closeItemModal();

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: currentModalItem.nama + ' ditambahkan!',
            showConfirmButton: false,
            timer: 1500
        });
    }

    function saveCart() {
        localStorage.setItem('sekarsa_cart', JSON.stringify(cart));
        renderCartUI();
    }

    function renderCartUI() {
        const floatingCart = document.getElementById('floatingCart');
        const badge = document.getElementById('cartCountBadge');
        const totalSummary = document.getElementById('cartTotalSummary');

        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

        if (totalItems > 0) {
            floatingCart.classList.remove('hidden');
            badge.innerText = totalItems;
            totalSummary.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        } else {
            floatingCart.classList.add('hidden');
        }
    }

    // Modal Checkout
    function openCheckoutModal() {
        if (!currentMejaId) {
            openMejaModal();
            return;
        }

        if (cart.length === 0) {
            Swal.fire('Keranjang Kosong', 'Silakan pilih menu terlebih dahulu.', 'info');
            return;
        }

        renderCheckoutItems();
        updateCheckoutCalculations();
        document.getElementById('checkoutModal').classList.remove('hidden');
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.add('hidden');
    }

    function renderCheckoutItems() {
        const container = document.getElementById('checkoutItemsContainer');
        container.innerHTML = '';

        cart.forEach((item, index) => {
            const itemTotal = item.price * item.qty;
            const el = document.createElement('div');
            el.className = 'flex items-center justify-between p-2.5 bg-gray-50 rounded-xl border border-gray-100 text-xs';
            el.innerHTML = `
                <div class="flex-1 min-w-0 pr-2">
                    <p class="font-bold text-gray-800 truncate">${item.name}</p>
                    <p class="text-gray-500 text-[11px]">Rp ${item.price.toLocaleString('id-ID')} &times; ${item.qty} = <strong class="text-emerald-800">Rp ${itemTotal.toLocaleString('id-ID')}</strong></p>
                    ${item.notes ? `<p class="text-[10px] text-amber-700 italic"><i class="fa-solid fa-pen-to-square"></i> ${item.notes}</p>` : ''}
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="modifyItemQty(${index}, -1)" class="w-6 h-6 rounded bg-white border border-gray-300 text-gray-700 flex items-center justify-center font-bold">
                        -
                    </button>
                    <span class="font-bold text-xs w-4 text-center">${item.qty}</span>
                    <button type="button" onclick="modifyItemQty(${index}, 1)" class="w-6 h-6 rounded bg-emerald-700 text-white flex items-center justify-center font-bold">
                        +
                    </button>
                    <button type="button" onclick="removeItem(${index})" class="w-6 h-6 text-red-500 hover:text-red-700 flex items-center justify-center ml-1">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            `;
            container.appendChild(el);
        });
    }

    function modifyItemQty(index, delta) {
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            cart.splice(index, 1);
        }
        saveCart();
        renderCheckoutItems();
        updateCheckoutCalculations();

        if (cart.length === 0) {
            closeCheckoutModal();
        }
    }

    function removeItem(index) {
        cart.splice(index, 1);
        saveCart();
        renderCheckoutItems();
        updateCheckoutCalculations();

        if (cart.length === 0) {
            closeCheckoutModal();
        }
    }

    function updateCheckoutCalculations() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        document.getElementById('summarySubtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');

        let discount = 0;
        const promoRow = document.getElementById('summaryPromoRow');

        if (appliedPromo && appliedPromo.valid) {
            discount = appliedPromo.diskon;
            promoRow.style.display = 'flex';
            document.getElementById('summaryPromoCode').innerText = appliedPromo.code;
            document.getElementById('summaryDiscount').innerText = '- Rp ' + discount.toLocaleString('id-ID');
        } else {
            promoRow.style.display = 'none';
        }

        const grandTotal = Math.max(0, subtotal - discount);
        document.getElementById('summaryGrandTotal').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
    }

    // Apply Promo Code AJAX
    function applyPromoCode() {
        const code = document.getElementById('promoInput').value.trim();
        const msgEl = document.getElementById('promoMessage');
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

        if (!code) {
            msgEl.className = 'text-[11px] mt-1.5 text-red-600';
            msgEl.innerText = 'Masukkan kode promo terlebih dahulu.';
            msgEl.classList.remove('hidden');
            return;
        }

        fetch("{{ route('customer.check_promo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ kode_promo: code, subtotal: subtotal })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                appliedPromo = {
                    valid: true,
                    code: data.kode_promo,
                    diskon: data.diskon
                };
                msgEl.className = 'text-[11px] mt-1.5 text-emerald-700 font-bold';
                msgEl.innerText = data.message;
                msgEl.classList.remove('hidden');
                updateCheckoutCalculations();
            } else {
                appliedPromo = null;
                msgEl.className = 'text-[11px] mt-1.5 text-red-600';
                msgEl.innerText = data.message || 'Kode promo tidak dapat digunakan.';
                msgEl.classList.remove('hidden');
                updateCheckoutCalculations();
            }
        })
        .catch(() => {
            msgEl.className = 'text-[11px] mt-1.5 text-red-600';
            msgEl.innerText = 'Gagal memeriksa promo.';
            msgEl.classList.remove('hidden');
        });
    }

    // Submit Order (Alur 2 & 3: Kirim pesanan ke sistem antrean FIFO)
    function processSubmitOrder() {
        if (!currentMejaId) {
            Swal.fire('Nomor Meja Diperlukan', 'Silakan pilih nomor meja Anda.', 'warning');
            return;
        }

        const customerName = document.getElementById('customerName').value.trim();
        if (!customerName) {
            Swal.fire('Nama Diperlukan', 'Silakan isi nama pemesan.', 'warning');
            document.getElementById('customerName').focus();
            return;
        }

        if (cart.length === 0) {
            Swal.fire('Keranjang Kosong', 'Pilih minimal satu menu untuk dipesan.', 'warning');
            return;
        }

        const btn = document.getElementById('btnSubmitOrder');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Mengirim ke Antrean FIFO...</span>';

        const payload = {
            meja_id: currentMejaId,
            nama_pelanggan: customerName,
            catatan_umum: document.getElementById('generalNotes').value.trim(),
            kode_promo: appliedPromo ? appliedPromo.code : null,
            items: cart.map(item => ({
                menu_id: item.id,
                jumlah: item.qty,
                catatan: item.notes
            }))
        };

        fetch("{{ route('customer.submit_order') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Bersihkan keranjang
                localStorage.removeItem('sekarsa_cart');
                cart = [];
                renderCartUI();

                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Berhasil Masuk!',
                    html: `Nomor Pesanan Anda: <strong class="text-xl text-emerald-800">${data.nomor_pesanan}</strong><br>Pesanan Anda telah masuk dalam antrean pengerjaan barista.`,
                    confirmButtonText: 'Lihat Status Antrean',
                    confirmButtonColor: '#16a34a',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>Kirim Pesanan Sekarang (Masuk FIFO)</span>';
                Swal.fire('Gagal Mengirim Pesanan', data.message || 'Terjadi kesalahan.', 'error');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>Kirim Pesanan Sekarang (Masuk FIFO)</span>';
            Swal.fire('Kesalahan Jaringan', 'Gagal menghubungi server.', 'error');
        });
    }

    // Modal Meja
    function openMejaModal() {
        document.getElementById('mejaModal').classList.remove('hidden');
    }
    function closeMejaModal() {
        document.getElementById('mejaModal').classList.add('hidden');
    }
</script>
@endpush
