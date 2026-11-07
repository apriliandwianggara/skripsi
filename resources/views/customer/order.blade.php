@extends('layouts.app')

@section('title', 'Menu & Pemesanan - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col pb-28">

    <!-- Header Sticky Mobile & Desktop -->
    <header class="glass-header text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-xl bg-white p-0.5 flex items-center justify-center shadow-md overflow-hidden shrink-0 border border-emerald-400/40">
                    <img src="{{ $setting->logo_url }}" alt="{{ $setting->cafe_name ?? 'Sekarsa Logo' }}" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-bold text-base tracking-wide font-serif leading-tight">
                        {{ $setting->cafe_name ?? 'SEKARSA COFFEE' }}
                    </h1>
                    <p class="text-[11px] text-emerald-200">
                        {{ $setting->cafe_tagline ?? 'Coffee & Space' }}
                        @if($tableParam)
                            <span class="ml-1 px-1.5 py-0.5 rounded bg-emerald-800 text-emerald-100 font-semibold font-mono text-[10px]">Meja {{ $tableParam }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Info FIFO Badge -->
                <div class="hidden sm:flex items-center space-x-1.5 bg-emerald-800/80 px-2.5 py-1 rounded-full text-[11px] text-emerald-200 border border-emerald-700/60">
                    <i class="fa-solid fa-clock-rotate-left text-emerald-400"></i>
                    <span>Antrean FIFO</span>
                </div>
                <!-- Cart Quick Indicator -->
                <button onclick="openCartModal()" class="relative p-2 rounded-xl bg-emerald-700/50 hover:bg-emerald-700 text-white transition focus:outline-none">
                    <i class="fa-solid fa-basket-shopping text-lg"></i>
                    <span id="headerCartBadge" class="hidden absolute -top-1 -right-1 bg-amber-400 text-slate-900 text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center shadow">0</span>
                </button>
            </div>
        </div>

        <!-- Banner Info Pelanggan -->
        <div class="bg-emerald-950/60 border-t border-emerald-800/50 px-4 py-1.5 text-center text-[11px] text-emerald-200/90 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-circle-info text-emerald-400 text-xs"></i>
            <span>Pesanan diproses berurutan sesuai waktu masuk (FIFO).</span>
        </div>
    </header>

    <!-- Main Container -->
    <main class="max-w-3xl mx-auto w-full px-4 pt-4 flex-1">

        @if(!empty($setting->announcement_text))
        <!-- Running Text / Pengumuman Kafe -->
        <div class="mb-3 bg-amber-500 text-slate-950 px-3 py-2 rounded-xl text-xs font-semibold flex items-center space-x-2 shadow-sm border border-amber-600/30">
            <i class="fa-solid fa-bullhorn text-amber-900 shrink-0"></i>
            <marquee behavior="scroll" direction="left" scrollamount="4" class="font-medium">
                {{ $setting->announcement_text }}
            </marquee>
        </div>
        @endif

        <!-- Hero Banner Suasana Kafe Sekarsa -->
        <div class="mb-4 relative rounded-2xl overflow-hidden shadow-md border border-emerald-900/10 h-36 sm:h-48 group">
            <img src="{{ $setting->banner_url }}" alt="{{ $setting->cafe_name ?? 'Sekarsa Coffee & Space' }}" class="w-full h-full object-cover object-center filter brightness-90 transition duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent flex flex-col justify-end p-3.5 sm:p-5 text-white">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white w-max mb-1 shadow-sm">
                    <i class="fa-solid fa-mug-hot mr-1 text-[9px]"></i> {{ $setting->cafe_name ?? 'Sekarsa Coffee & Space' }}
                </span>
                <h2 class="text-base sm:text-xl font-bold font-serif leading-tight">
                    {{ $setting->hero_title ?? 'Warmth, Taste & Harmony in Every Sip' }}
                </h2>
                <p class="text-[11px] text-emerald-200 flex items-center mt-0.5 line-clamp-1">
                    <i class="fa-solid fa-location-dot mr-1 text-amber-400 shrink-0"></i>
                    <span>{{ $setting->hero_subtitle ?? ($setting->cafe_address ?? 'Jl. Kaliurang KM 5.8, Manggung, Caturtunggal, Sleman') }}</span>
                </p>
                @if(!empty($setting->cafe_opening_hours))
                <p class="text-[10px] text-emerald-300/90 flex items-center mt-0.5">
                    <i class="fa-solid fa-clock mr-1 text-emerald-400 shrink-0"></i>
                    <span>{{ $setting->cafe_opening_hours }}</span>
                </p>
                @endif
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-4">
            <form action="{{ route('customer.order') }}" method="GET" class="relative">
                @if(!empty($tokenParam))
                    <input type="hidden" name="token" value="{{ $tokenParam }}">
                @endif
                @if($tableParam)
                    <input type="hidden" name="meja" value="{{ $tableParam }}">
                @endif
                @if($selectedCategory)
                    <input type="hidden" name="kategori" value="{{ $selectedCategory }}">
                @endif
                <div class="relative">
                    <input type="text"
                           name="q"
                           value="{{ $search }}"
                           placeholder="Cari kopi, mocktail, camilan favoritmu..."
                           class="w-full bg-white pl-10 pr-10 py-3 rounded-2xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent shadow-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                    @if($search)
                        <a href="{{ route('customer.order', array_filter(['token' => $tokenParam, 'meja' => $tableParam, 'kategori' => $selectedCategory])) }}"
                           class="absolute right-3.5 top-3 text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-circle-xmark text-base"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Category Horizontal Filter (Scrollable) -->
        <div class="mb-5 -mx-4 px-4 overflow-x-auto scrollbar-hide flex items-center space-x-2 pb-1">
            <a href="{{ route('customer.order', array_filter(['token' => $tokenParam, 'meja' => $tableParam, 'q' => $search])) }}"
               class="shrink-0 px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm {{ empty($selectedCategory) ? 'bg-emerald-700 text-white shadow-emerald-700/20' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                <i class="fa-solid fa-border-all mr-1.5"></i> Semua Menu
            </a>

            @foreach($categories as $cat)
                <a href="{{ route('customer.order', array_filter(['token' => $tokenParam, 'meja' => $tableParam, 'kategori' => $cat->slug, 'q' => $search])) }}"
                   class="shrink-0 px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm {{ $selectedCategory === $cat->slug ? 'bg-emerald-700 text-white shadow-emerald-700/20' : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-200' }}">
                    <i class="fa-solid fa-{{ $cat->icon ?: 'coffee' }} mr-1.5"></i> {{ $cat->name }}
                </a>
            @endforeach
        </div>

        @if($search)
            <div class="mb-4 flex items-center justify-between text-xs text-gray-600 bg-emerald-50 p-2.5 rounded-xl border border-emerald-100">
                <span>Hasil pencarian untuk: <strong class="text-emerald-900">"{{ $search }}"</strong> ({{ $menus->count() }} menu)</span>
                <a href="{{ route('customer.order', array_filter(['token' => $tokenParam, 'meja' => $tableParam, 'kategori' => $selectedCategory])) }}" class="text-emerald-700 font-semibold hover:underline">Reset</a>
            </div>
        @endif

        <!-- Menu Grid -->
        @if($menus->isEmpty())
            <div class="bg-white rounded-3xl p-8 text-center border border-gray-100 shadow-sm my-6">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-gray-400 mb-3 text-2xl">
                    <i class="fa-solid fa-mug-saucer"></i>
                </div>
                <h3 class="font-bold text-gray-700 text-base mb-1">Menu Tidak Ditemukan</h3>
                <p class="text-xs text-gray-500 mb-4">Sajian yang kamu cari belum tersedia atau sedang habis.</p>
                <a href="{{ route('customer.order', array_filter(['token' => $tokenParam, 'meja' => $tableParam])) }}" class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 px-4 py-2 rounded-xl hover:bg-emerald-100">
                    <i class="fa-solid fa-arrow-left mr-1.5"></i> Lihat Semua Menu
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                @foreach($menus as $menu)
                    <div class="bg-white rounded-2xl p-3 border border-gray-100 shadow-sm hover:shadow-md transition flex flex-row items-center space-x-3 relative" id="menu-card-{{ $menu->id }}">
                        <!-- Menu Image -->
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-emerald-50 shrink-0 relative {{ !$menu->is_available ? 'grayscale opacity-75' : '' }}">
                            <img src="{{ $menu->image_url }}"
                                 alt="{{ $menu->name }}"
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                            <span class="absolute bottom-1 left-1 bg-black/60 backdrop-blur-sm text-white text-[9px] font-semibold px-1.5 py-0.5 rounded">
                                {{ $menu->category->name ?? 'Menu' }}
                            </span>
                            @if(!$menu->is_available)
                                <div class="absolute inset-0 bg-red-950/60 backdrop-blur-[1px] flex items-center justify-center">
                                    <span class="bg-red-600 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded shadow">Habis</span>
                                </div>
                            @endif
                        </div>

                        <!-- Menu Details -->
                        <div class="flex-1 min-w-0 pr-1 flex flex-col justify-between self-stretch py-0.5">
                            <div>
                                <div class="flex items-center space-x-1.5">
                                    <h3 class="font-bold text-gray-900 text-sm leading-snug truncate {{ !$menu->is_available ? 'line-through text-gray-400' : '' }}">{{ $menu->name }}</h3>
                                    @if(!$menu->is_available)
                                        <span class="px-1.5 py-0.2 bg-red-100 text-red-700 text-[10px] font-extrabold rounded">Habis</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 line-clamp-2 mt-0.5 leading-relaxed">{{ $menu->description ?: 'Racikan spesial khas Sekarsa Coffee.' }}</p>
                            </div>

                            <div class="flex items-center justify-between mt-2 pt-1 border-t border-gray-50">
                                <div>
                                    @if($menu->variations->isNotEmpty())
                                        <span class="font-bold text-emerald-800 text-xs sm:text-sm block">
                                            {{ $menu->formatted_price_range }}
                                        </span>
                                        <span class="inline-flex items-center text-[9px] text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 font-medium mt-0.5">
                                            <i class="fa-solid fa-layer-group mr-1 text-[8px]"></i> {{ $menu->variations->count() }} Pilihan
                                        </span>
                                    @else
                                        <span class="font-bold text-emerald-800 text-sm {{ !$menu->is_available ? 'text-gray-400' : '' }}">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                @if($menu->is_available)
                                    @if($menu->variations->isNotEmpty())
                                        <button type="button"
                                                onclick="openVariationModal({{ $menu->id }})"
                                                class="inline-flex items-center space-x-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3 py-1.5 rounded-xl text-xs font-semibold transition shadow-sm">
                                            <span>Pilih</span>
                                            <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                                onclick="addToCart({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }}, '{{ $menu->image_url }}')"
                                                class="inline-flex items-center space-x-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3 py-1.5 rounded-xl text-xs font-semibold transition shadow-sm">
                                            <i class="fa-solid fa-plus text-[10px]"></i>
                                            <span>Pesan</span>
                                        </button>
                                    @endif
                                @else
                                    <button type="button" disabled
                                            class="inline-flex items-center space-x-1 bg-gray-100 text-gray-400 px-3 py-1.5 rounded-xl text-xs font-semibold cursor-not-allowed">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                        <span>Habis</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Footer Info Kafe -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center text-xs text-gray-500">
            <p class="font-serif font-bold text-gray-700 text-sm">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</p>
            <p class="mt-1 text-[11px]">{{ $setting->cafe_address ?? 'Jl. Sekarsa, Tempat Santai & Produktif' }}</p>
            @if($setting->cafe_phone)
                <p class="mt-0.5 text-[11px]">WhatsApp: {{ $setting->cafe_phone }}</p>
            @endif
        </div>
    </main>

    <!-- Bottom Floating Cart Bar -->
    <div id="floatingCartBar" class="hidden fixed bottom-4 inset-x-4 max-w-xl mx-auto z-40">
        <div class="bg-sekarsa-900 text-white rounded-2xl p-3.5 shadow-2xl flex items-center justify-between border border-emerald-700/60 backdrop-blur-md">
            <div class="flex items-center space-x-3 cursor-pointer" onclick="openCartModal()">
                <div class="relative bg-emerald-600 w-11 h-11 rounded-xl flex items-center justify-center text-white">
                    <i class="fa-solid fa-basket-shopping text-lg"></i>
                    <span id="floatingCartCount" class="absolute -top-1.5 -right-1.5 bg-amber-400 text-slate-900 text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center">0</span>
                </div>
                <div>
                    <p class="text-[11px] text-emerald-200">Total Pembayaran</p>
                    <p id="floatingCartTotal" class="font-bold text-base text-white">Rp 0</p>
                </div>
            </div>

            <button onclick="openCartModal()" class="bg-emerald-500 hover:bg-emerald-400 active:scale-95 text-slate-950 font-bold px-4 py-2.5 rounded-xl text-xs flex items-center space-x-1.5 shadow transition">
                <span>Lihat Keranjang</span>
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Modal Pilih Variasi Menu -->
    <div id="variationModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-3xl sm:rounded-3xl max-h-[90vh] flex flex-col shadow-2xl overflow-hidden animate-slide-up">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-emerald-950 text-white">
                <div class="flex items-center space-x-2.5">
                    <i class="fa-solid fa-mug-hot text-amber-400"></i>
                    <h3 class="font-bold text-sm">Pilih Variasi / Ukuran</h3>
                </div>
                <button type="button" onclick="closeVariationModal()" class="text-emerald-200 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 overflow-y-auto flex-1 space-y-4">
                <div class="flex items-center space-x-3 bg-gray-50 p-2.5 rounded-2xl border border-gray-100">
                    <img id="varModalImage" src="" class="w-14 h-14 rounded-xl object-cover bg-emerald-100 shrink-0" onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                    <div class="min-w-0 flex-1">
                        <h4 id="varModalMenuName" class="font-bold text-sm text-gray-900 truncate">Menu</h4>
                        <p id="varModalMenuDesc" class="text-[11px] text-gray-500 line-clamp-1 mt-0.5">Deskripsi</p>
                    </div>
                </div>

                <!-- Pilihan Variasi Radio Buttons -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">
                        Pilihan Variasi <span class="text-red-500">*</span>
                    </label>
                    <div id="varModalOptionsList" class="space-y-2">
                        <!-- Populated dynamically by JS -->
                    </div>
                </div>

                <!-- Jumlah / Stepper -->
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <div>
                        <label class="text-xs font-bold text-gray-700 block">Jumlah Pesanan</label>
                        <span class="text-[10px] text-gray-400">Atur porsi sajian</span>
                    </div>
                    <div class="flex items-center border border-gray-300 rounded-xl bg-white shadow-sm overflow-hidden">
                        <button type="button" onclick="changeVarModalQty(-1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 active:scale-95 text-xs">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <span id="varModalQtyDisplay" class="w-8 text-center font-bold text-xs text-gray-800">1</span>
                        <button type="button" onclick="changeVarModalQty(1)" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 active:scale-95 text-xs">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Catatan Khusus Item -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan Menu <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <input type="text" id="varModalNotes" placeholder="Contoh: Less ice, normal sugar" class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 text-gray-700 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
            </div>

            <!-- Footer / Action -->
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <div>
                    <span class="text-[10px] text-gray-500 block">Total</span>
                    <span id="varModalTotalPrice" class="font-bold text-sm sm:text-base text-emerald-800 font-mono">Rp 0</span>
                </div>
                <button type="button" onclick="confirmAddVariationToCart()" class="bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold px-4 py-2.5 rounded-xl text-xs flex items-center space-x-1.5 shadow transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah ke Keranjang</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Keranjang & Checkout -->
    <div id="cartModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-lg rounded-t-3xl sm:rounded-3xl max-h-[92vh] flex flex-col shadow-2xl overflow-hidden animate-slide-up">
            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-emerald-900 text-white">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-basket-shopping text-emerald-300"></i>
                    <h2 class="font-bold text-base">Keranjang Pesanan</h2>
                </div>
                <button onclick="closeCartModal()" class="text-emerald-200 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 overflow-y-auto flex-1 space-y-4">
                <!-- Cart Items List -->
                <div id="cartItemsList" class="space-y-3">
                    <!-- Populated by JavaScript -->
                </div>

                <!-- Empty Cart State in Modal -->
                <div id="cartEmptyState" class="hidden text-center py-8">
                    <i class="fa-solid fa-basket-shopping text-4xl text-gray-300 mb-2"></i>
                    <p class="text-sm font-semibold text-gray-600">Keranjang masih kosong</p>
                    <p class="text-xs text-gray-400 mt-1">Silakan pilih menu minuman atau makanan favoritmu.</p>
                </div>

                <!-- Form Identitas Pelanggan -->
                <div id="checkoutFormSection" class="pt-3 border-t border-gray-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">
                            <i class="fa-solid fa-user-pen mr-1"></i> Data Pemesan
                        </span>
                        <span class="text-[11px] text-gray-500">* Wajib diisi</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Nama Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="inputCustomerName"
                               placeholder="Contoh: Dimas Aditya"
                               required
                               class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                No. Meja <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="text"
                                   id="inputTableNumber"
                                   value="{{ $tableParam ?? '' }}"
                                   placeholder="Contoh: 04"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                No. WhatsApp <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <input type="tel"
                                   id="inputCustomerPhone"
                                   placeholder="08xxxxxxxxxx"
                                   class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Catatan Khusus <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <textarea id="inputCustomerNotes"
                                  rows="2"
                                  placeholder="Contoh: Kurangi gula, es batu sedikit saja"
                                  class="w-full text-sm px-3.5 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <!-- Ringkasan Algoritma FIFO -->
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900 space-y-1">
                        <div class="font-bold flex items-center space-x-1.5 text-emerald-800">
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                            <span>Sistem Antrean FIFO Sekarsa Coffee</span>
                        </div>
                        <p class="text-[11px] text-emerald-700">
                            Waktu server saat pesanan dikirim akan menjadi penentu urutan pengerjaan pesanan Anda.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Total & Button) -->
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-gray-600 font-medium">Total Tagihan:</span>
                    <span id="modalTotalAmount" class="font-bold text-lg text-emerald-900">Rp 0</span>
                </div>

                <button id="btnSubmitOrder"
                        onclick="submitOrder()"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold py-3.5 rounded-xl text-sm transition shadow-lg shadow-emerald-600/30 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Kirim Pesanan Sekarang</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Data Menu & Variasi untuk Akses Cepat di Klien
    const menuData = {
        @foreach($menus as $m)
        "{{ $m->id }}": {
            id: {{ $m->id }},
            name: "{{ addslashes($m->name) }}",
            price: {{ (float)$m->price }},
            imageUrl: "{{ $m->image_url }}",
            description: "{{ addslashes($m->description ?? '') }}",
            hasVariations: {{ $m->variations->isNotEmpty() ? 'true' : 'false' }},
            variations: [
                @foreach($m->variations as $v)
                {
                    id: {{ $v->id }},
                    name: "{{ addslashes($v->name) }}",
                    price: {{ (float)$v->price }}
                },
                @endforeach
            ]
        },
        @endforeach
    };

    // Cart storage in localStorage
    const CART_STORAGE_KEY = 'sekarsa_coffee_cart';
    let cart = [];

    // Load cart from localStorage
    function loadCart() {
        try {
            const saved = localStorage.getItem(CART_STORAGE_KEY);
            cart = saved ? JSON.parse(saved) : [];
            // Normalisasi cartKey untuk kompatibilitas data lama
            cart.forEach(item => {
                if (!item.cartKey) {
                    item.cartKey = item.variation_id ? `${item.id}_${item.variation_id}` : String(item.id);
                }
            });
        } catch (e) {
            cart = [];
        }
        updateCartUI();
    }

    // Save cart to localStorage
    function saveCart() {
        localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
        updateCartUI();
    }

    // State Modal Variasi
    let currentVariationMenu = null;
    let currentSelectedVariation = null;
    let varModalQty = 1;

    // Buka Modal Pilih Variasi
    function openVariationModal(menuId) {
        const menu = menuData[menuId];
        if (!menu) return;

        currentVariationMenu = menu;
        varModalQty = 1;

        document.getElementById('varModalMenuName').innerText = menu.name;
        document.getElementById('varModalMenuDesc').innerText = menu.description || 'Pilihan variasi nikmat dari Sekarsa Coffee.';
        document.getElementById('varModalImage').src = menu.imageUrl;
        document.getElementById('varModalNotes').value = '';
        document.getElementById('varModalQtyDisplay').innerText = '1';

        // Render radio button pilihan variasi
        const listContainer = document.getElementById('varModalOptionsList');
        listContainer.innerHTML = '';

        if (menu.variations && menu.variations.length > 0) {
            currentSelectedVariation = menu.variations[0]; // default variasi pertama

            menu.variations.forEach((v, idx) => {
                const isChecked = idx === 0 ? 'checked' : '';
                const optionDiv = document.createElement('label');
                optionDiv.className = `variation-option-card flex items-center justify-between p-3 rounded-xl border transition cursor-pointer ${idx === 0 ? 'border-emerald-500 bg-emerald-50/50 shadow-sm' : 'border-gray-200 bg-white hover:bg-gray-50'}`;
                optionDiv.innerHTML = `
                    <div class="flex items-center space-x-2.5">
                        <input type="radio" name="selected_variation_radio" value="${v.id}" ${isChecked} onchange="onSelectVariation(${v.id})" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <div>
                            <span class="font-bold text-xs text-gray-800 block">${v.name}</span>
                            <span class="text-[10px] text-gray-400">Pilihan sajian</span>
                        </div>
                    </div>
                    <span class="font-bold text-xs text-emerald-800 font-mono">${formatRupiah(v.price)}</span>
                `;
                listContainer.appendChild(optionDiv);
            });
        }

        updateVarModalTotal();

        const modal = document.getElementById('variationModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function onSelectVariation(varId) {
        if (!currentVariationMenu) return;
        currentSelectedVariation = currentVariationMenu.variations.find(v => v.id === varId);

        // Update border highlight visual
        document.querySelectorAll('.variation-option-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');
            if (radio && radio.checked) {
                card.classList.add('border-emerald-500', 'bg-emerald-50/50', 'shadow-sm');
                card.classList.remove('border-gray-200', 'bg-white');
            } else {
                card.classList.remove('border-emerald-500', 'bg-emerald-50/50', 'shadow-sm');
                card.classList.add('border-gray-200', 'bg-white');
            }
        });

        updateVarModalTotal();
    }

    function changeVarModalQty(delta) {
        varModalQty += delta;
        if (varModalQty < 1) varModalQty = 1;
        if (varModalQty > 50) varModalQty = 50;
        document.getElementById('varModalQtyDisplay').innerText = varModalQty;
        updateVarModalTotal();
    }

    function updateVarModalTotal() {
        if (!currentSelectedVariation) return;
        const total = currentSelectedVariation.price * varModalQty;
        document.getElementById('varModalTotalPrice').innerText = formatRupiah(total);
    }

    function closeVariationModal() {
        const modal = document.getElementById('variationModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function confirmAddVariationToCart() {
        if (!currentVariationMenu || !currentSelectedVariation) {
            Swal.fire('Peringatan', 'Silakan pilih variasi menu terlebih dahulu.', 'warning');
            return;
        }

        const notes = document.getElementById('varModalNotes').value.trim();
        const cartKey = `${currentVariationMenu.id}_${currentSelectedVariation.id}`;

        const existing = cart.find(item => item.cartKey === cartKey);
        if (existing) {
            existing.quantity += varModalQty;
            if (notes && !existing.notes) existing.notes = notes;
        } else {
            cart.push({
                cartKey: cartKey,
                id: currentVariationMenu.id,
                variation_id: currentSelectedVariation.id,
                variation_name: currentSelectedVariation.name,
                name: `${currentVariationMenu.name} (${currentSelectedVariation.name})`,
                price: currentSelectedVariation.price,
                image: currentVariationMenu.imageUrl,
                quantity: varModalQty,
                notes: notes
            });
        }

        saveCart();
        closeVariationModal();

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: `+${varModalQty} ${currentVariationMenu.name} (${currentSelectedVariation.name}) ditambahkan!`
        });
    }

    // Add item to cart (Menu reguler tanpa variasi)
    function addToCart(id, name, price, image) {
        const cartKey = String(id);
        const existing = cart.find(item => (item.cartKey || String(item.id)) === cartKey);
        if (existing) {
            existing.quantity += 1;
        } else {
            cart.push({
                cartKey: cartKey,
                id: id,
                variation_id: null,
                variation_name: null,
                name: name,
                price: price,
                image: image,
                quantity: 1,
                notes: ''
            });
        }
        saveCart();

        // Toast feedback
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
        Toast.fire({
            icon: 'success',
            title: `+1 ${name} ditambahkan!`
        });
    }

    // Change item quantity
    function changeQty(cartKey, delta) {
        const item = cart.find(i => (i.cartKey || String(i.id)) === String(cartKey));
        if (!item) return;

        item.quantity += delta;
        if (item.quantity <= 0) {
            cart = cart.filter(i => (i.cartKey || String(i.id)) !== String(cartKey));
        }
        saveCart();
    }

    // Remove item from cart
    function removeItem(cartKey) {
        cart = cart.filter(i => (i.cartKey || String(i.id)) !== String(cartKey));
        saveCart();
    }

    // Update item specific note
    function updateItemNote(cartKey, note) {
        const item = cart.find(i => (i.cartKey || String(i.id)) === String(cartKey));
        if (item) {
            item.notes = note;
            localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
        }
    }

    // Format currency IDR
    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    // Update Cart UI Elements
    function updateCartUI() {
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        // Header badge
        const headerBadge = document.getElementById('headerCartBadge');
        if (headerBadge) {
            headerBadge.innerText = totalItems;
            headerBadge.classList.toggle('hidden', totalItems === 0);
        }

        // Floating bottom bar
        const floatingBar = document.getElementById('floatingCartBar');
        const floatingCount = document.getElementById('floatingCartCount');
        const floatingTotal = document.getElementById('floatingCartTotal');
        if (floatingBar) {
            floatingBar.classList.toggle('hidden', totalItems === 0);
            if (floatingCount) floatingCount.innerText = totalItems;
            if (floatingTotal) floatingTotal.innerText = formatRupiah(totalPrice);
        }

        // Modal cart elements
        const modalTotal = document.getElementById('modalTotalAmount');
        if (modalTotal) modalTotal.innerText = formatRupiah(totalPrice);

        renderCartModalItems();
    }

    // Render items list inside modal
    function renderCartModalItems() {
        const container = document.getElementById('cartItemsList');
        const emptyState = document.getElementById('cartEmptyState');
        const checkoutForm = document.getElementById('checkoutFormSection');
        const btnSubmit = document.getElementById('btnSubmitOrder');

        if (!container) return;

        if (cart.length === 0) {
            container.innerHTML = '';
            emptyState.classList.remove('hidden');
            checkoutForm.classList.add('hidden');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');
            return;
        }

        emptyState.classList.add('hidden');
        checkoutForm.classList.remove('hidden');
        btnSubmit.disabled = false;
        btnSubmit.classList.remove('opacity-50', 'cursor-not-allowed');

        let html = '';
        cart.forEach(item => {
            const itemKey = item.cartKey || String(item.id);
            html += `
                <div class="bg-gray-50 border border-gray-200/80 rounded-2xl p-3 flex flex-col space-y-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                            <img src="${item.image}" class="w-10 h-10 rounded-lg object-cover bg-emerald-100 shrink-0" onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                            <div class="truncate">
                                <h4 class="font-bold text-xs text-gray-800 truncate">${item.name}</h4>
                                <span class="text-xs text-emerald-800 font-semibold">${formatRupiah(item.price)}</span>
                            </div>
                        </div>

                        <!-- Stepper & Delete -->
                        <div class="flex items-center space-x-2 shrink-0">
                            <div class="flex items-center border border-gray-300 rounded-xl bg-white shadow-sm overflow-hidden">
                                <button onclick="changeQty('${itemKey}', -1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-100 active:scale-95 text-xs">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <span class="w-8 text-center font-bold text-xs text-gray-800">${item.quantity}</span>
                                <button onclick="changeQty('${itemKey}', 1)" class="w-7 h-7 flex items-center justify-center text-gray-600 hover:bg-gray-100 active:scale-95 text-xs">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                            <button onclick="removeItem('${itemKey}')" class="text-red-400 hover:text-red-600 p-1 text-xs">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Note input per item -->
                    <div>
                        <input type="text"
                               placeholder="Catatan menu (opsional: less ice/sugar)"
                               value="${item.notes || ''}"
                               onchange="updateItemNote('${itemKey}', this.value)"
                               class="w-full text-[11px] bg-white px-2.5 py-1.5 rounded-lg border border-gray-200 text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Modal Cart Controls
    function openCartModal() {
        const modal = document.getElementById('cartModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCartModal() {
        const modal = document.getElementById('cartModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Submit Order to Server via AJAX POST
    async function submitOrder() {
        if (cart.length === 0) {
            Swal.fire('Keranjang Kosong', 'Silakan pilih menu terlebih dahulu.', 'warning');
            return;
        }

        const customerName = document.getElementById('inputCustomerName').value.trim();
        const customerPhone = document.getElementById('inputCustomerPhone').value.trim();
        const tableNumber = document.getElementById('inputTableNumber').value.trim();
        const customerNotes = document.getElementById('inputCustomerNotes').value.trim();

        if (!customerName) {
            Swal.fire({
                icon: 'warning',
                title: 'Nama Pelanggan Wajib Diisi',
                text: 'Mohon isi nama Anda agar barista dapat memanggil pesanan dengan tepat.',
                confirmButtonColor: '#16a34a'
            });
            document.getElementById('inputCustomerName').focus();
            return;
        }

        const btnSubmit = document.getElementById('btnSubmitOrder');
        const originalHtml = btnSubmit.innerHTML;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

        const payload = {
            _token: csrfToken,
            qr_token: "{{ $tokenParam }}",
            customer_name: customerName,
            customer_phone: customerPhone,
            table_number: tableNumber,
            customer_notes: customerNotes,
            items: cart.map(item => ({
                menu_id: item.id,
                variation_id: item.variation_id || null,
                quantity: item.quantity,
                notes: item.notes || null
            }))
        };

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i><span>Mencatat Pesanan ke Server...</span>`;

        try {
            const res = await fetch("{{ route('customer.submit_order') }}", {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok && data.success) {
                // Clear cart from storage
                localStorage.removeItem(CART_STORAGE_KEY);
                cart = [];

                // Redirect to confirmation page
                window.location.replace(data.confirmation_url);
            } else {
                if (data.redirect_url) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akses Ditolak',
                        text: data.message,
                        confirmButtonColor: '#16a34a',
                        confirmButtonText: 'Scan QR di Meja'
                    }).then(() => {
                        window.location.href = data.redirect_url;
                    });
                    return;
                }
                throw new Error(data.message || 'Gagal mengirim pesanan');
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Mengirim',
                text: error.message || 'Terjadi gangguan jaringan, silakan coba lagi.',
                confirmButtonColor: '#dc2626'
            });
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = originalHtml;
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadCart();
        // Polling Real-time Status Ketersediaan Menu (Jika Kasir Mengubah Menu Jadi Habis)
        setInterval(checkRealtimeMenuAvailability, 3000);
    });

    let lastMenuVersion = 0;
    async function checkRealtimeMenuAvailability() {
        try {
            const res = await fetch("{{ route('customer.menu_status_api') }}");
            if (!res.ok) return;

            const data = await res.json();
            if (!data.success) return;

            if (data.version && data.version !== lastMenuVersion && lastMenuVersion !== 0) {
                // Perubahan ketersediaan menu terjadi di kasir/admin
                // Update tampilan tombol dan status menu secara langsung tanpa reload
                data.menus.forEach(m => {
                    const card = document.getElementById(`menu-card-${m.id}`);
                    if (!card) return;

                    const btn = card.querySelector('button');
                    const imgBox = card.querySelector('.shrink-0');
                    const title = card.querySelector('h3');

                    if (!m.is_available) {
                        // Ubah menjadi habis
                        if (btn) {
                            btn.disabled = true;
                            btn.className = 'inline-flex items-center space-x-1 bg-gray-100 text-gray-400 px-3 py-1.5 rounded-xl text-xs font-semibold cursor-not-allowed';
                            btn.innerHTML = '<i class="fa-solid fa-ban text-[10px]"></i><span>Habis</span>';
                            btn.onclick = null;
                        }
                        if (title && !title.classList.contains('line-through')) {
                            title.classList.add('line-through', 'text-gray-400');
                        }
                    } else {
                        // Ubah menjadi tersedia
                        if (btn && btn.disabled) {
                            btn.disabled = false;
                            btn.className = 'inline-flex items-center space-x-1 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white px-3 py-1.5 rounded-xl text-xs font-semibold transition shadow-sm';
                            btn.innerHTML = '<i class="fa-solid fa-plus text-[10px]"></i><span>Pesan</span>';
                            btn.onclick = () => location.reload();
                        }
                        if (title) {
                            title.classList.remove('line-through', 'text-gray-400');
                        }
                    }
                });
            }
            lastMenuVersion = data.version;
        } catch (e) {
            console.warn('Gagal cek status ketersediaan menu:', e);
        }
    }
</script>
@endpush

