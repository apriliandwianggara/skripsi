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
                <!-- Cart Quick Indicator -->
                <button onclick="openCartModal()" class="relative p-2 rounded-xl bg-emerald-700/50 hover:bg-emerald-700 text-white transition focus:outline-none">
                    <i class="fa-solid fa-basket-shopping text-lg"></i>
                    <span id="headerCartBadge" class="hidden absolute -top-1 -right-1 bg-amber-400 text-slate-900 text-[10px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center shadow">0</span>
                </button>
            </div>
        </div>

        <!-- Banner Info Pelanggan -->
        <div class="bg-emerald-950/60 border-t border-emerald-800/50 px-4 py-1.5 text-center text-[11px] text-emerald-200/90 flex items-center justify-center space-x-2">
            <i class="fa-solid fa-mug-hot text-emerald-400 text-xs"></i>
            <span>Setiap sajian disiapkan segar dan berurutan demi cita rasa terbaik.</span>
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

        <!-- Widget Cek Membership Pelanggan Sekarsa Coffee -->
        <div class="mb-4 bg-gradient-to-r from-emerald-800 via-emerald-900 to-emerald-950 text-white p-3.5 rounded-2xl shadow-sm border border-emerald-700/50">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-amber-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-gift text-amber-400"></i>
                    <span>Cek Jumlah Pesanan & Hadiah Member</span>
                </span>
                <span class="text-[10px] text-emerald-200 bg-white/10 px-2 py-0.5 rounded-full font-semibold">
                    Pesanan ke-10 GRATIS
                </span>
            </div>
            <div class="flex gap-2">
                <input type="tel" id="topCheckPhone" placeholder="Ketik No. WhatsApp (contoh: 0812...)"
                       class="flex-1 bg-white/10 text-white placeholder-emerald-200/60 px-3 py-2 rounded-xl text-xs border border-emerald-600/60 focus:outline-none focus:ring-1 focus:ring-amber-400 font-mono">
                <button type="button" onclick="checkMembershipTop()" class="px-4 py-2 bg-amber-400 hover:bg-amber-300 active:scale-95 text-emerald-950 font-bold text-xs rounded-xl transition shrink-0 cursor-pointer shadow">
                    <span>Cek Poin</span>
                </button>
            </div>
            <!-- Alert Hasil Cek di Menu Utama -->
            <div id="topCheckResult" class="hidden mt-2.5 p-3 bg-white text-gray-900 rounded-xl text-xs shadow">
                <!-- Populated dynamically by JS -->
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

                    <!-- Banner Info Membership Sekarsa Coffee -->
                    <div class="bg-gradient-to-r from-emerald-800 to-emerald-950 text-white rounded-2xl p-3.5 shadow-md flex items-start space-x-3 border border-emerald-700/50">
                        <div class="w-9 h-9 rounded-xl bg-amber-400 text-emerald-950 flex items-center justify-center text-lg shrink-0 shadow">
                            <i class="fa-solid fa-gift"></i>
                        </div>
                        <div class="flex-1 text-xs">
                            <h4 class="font-bold text-amber-300 text-xs">Program Membership Sekarsa Coffee</h4>
                            <p class="text-[11px] text-emerald-100/90 mt-0.5 leading-relaxed">
                                Isi <strong>No. WhatsApp</strong> Anda! Kumpulkan 9 pemesanan, dapatkan <strong>1 Menu Pilihan GRATIS</strong> di pesanan ke-10 Anda.
                            </p>
                        </div>
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
                            <div class="flex gap-1.5">
                                <input type="tel"
                                       id="inputCustomerPhone"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full text-sm px-3.5 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono">
                                <button type="button" onclick="triggerManualCheckPhone()" class="px-3.5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shrink-0 cursor-pointer flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <span>Cek</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Kotak Status Membership Interaktif -->
                    <div id="memberStatusBox" class="hidden transition-all duration-300">
                        <!-- Loading State -->
                        <div id="memberLoading" class="hidden text-xs text-emerald-700 py-1.5 flex items-center gap-2">
                            <i class="fa-solid fa-spinner fa-spin text-sm"></i>
                            <span>Memeriksa status membership...</span>
                        </div>

                        <!-- New Member State -->
                        <div id="memberNewCard" class="hidden p-3 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs space-y-1">
                            <p class="font-bold text-emerald-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-sparkles text-amber-500"></i>
                                <span>Nomor Baru Siap Terdaftar!</span>
                            </p>
                            <p class="text-[11px] text-emerald-700 leading-relaxed">
                                Nomor ini belum memiliki riwayat pesanan. Pesanan ini akan dihitung sebagai <strong>pesanan ke-1</strong> Anda. Selesaikan 9 pesanan untuk mendapatkan 1 menu gratis di pesanan ke-10!
                            </p>
                        </div>

                        <!-- Existing Member Progress State (1 s/d 8) -->
                        <div id="memberProgressCard" class="hidden p-3.5 bg-emerald-50/90 border-2 border-emerald-500 rounded-2xl text-xs space-y-2.5 shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm" id="memberGreetName">Halo Member!</p>
                                    <p class="text-xs text-emerald-800 font-medium">Nomor WhatsApp Anda terdaftar.</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block px-3 py-1 bg-emerald-700 text-white font-mono font-black text-sm rounded-xl shadow-sm" id="memberTotalOrdersBadge">
                                        0x Pesan
                                    </span>
                                </div>
                            </div>
                            <div class="p-2.5 bg-white rounded-xl border border-emerald-200">
                                <div class="flex justify-between items-center text-xs font-bold">
                                    <span class="text-emerald-900">Sudah Memesan: <strong class="text-emerald-700 text-sm font-mono" id="memberTotalOrdersCount">0</strong> kali</span>
                                    <span class="text-amber-700" id="memberCycleBadge">Siklus: 0 / 10</span>
                                </div>
                                <div class="w-full bg-emerald-100 h-2 rounded-full overflow-hidden my-1.5">
                                    <div id="memberProgressBar" class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                                </div>
                                <p class="text-[11px] text-gray-600 font-medium" id="memberRemainingText">
                                    Kurang X kali pesanan lagi untuk mendapatkan 1 Menu Pilihan GRATIS!
                                </p>
                            </div>
                        </div>

                        <!-- Eligible for Reward State (Pesanan ke-10) -->
                        <div id="memberRewardCard" class="hidden p-3.5 bg-gradient-to-br from-amber-50 via-amber-100/60 to-amber-50 border-2 border-amber-400 rounded-2xl text-xs space-y-3 shadow-md">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base font-black shrink-0 shadow animate-bounce">
                                    <i class="fa-solid fa-gift"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-amber-950 text-xs flex items-center gap-1.5">
                                        <span>🎉 SELAMAT! INI PESANAN KE-10 ANDA!</span>
                                    </h4>
                                    <p class="text-[11px] text-amber-800 leading-tight mt-0.5">
                                        Anda berhak mendapatkan <strong>1 Menu Pilihan GRATIS</strong>. Silakan pilih menu di bawah ini:
                                    </p>
                                </div>
                            </div>

                            <!-- Pilihan Menu Hadiah Gratis -->
                            <div class="space-y-2 pt-1" id="rewardMenuOptionsContainer">
                                <!-- Populated dynamically by JS -->
                            </div>
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

                    <!-- Pilihan Metode Pembayaran Sesuai Alur Baru -->
                    <div class="space-y-2 pt-1">
                        <label class="block text-xs font-bold text-gray-800">
                            Pilih Metode Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 gap-2.5">
                            <!-- Opsi 1: QRIS / DANA Otomatis Sesuai Nominal -->
                            <label id="paymentOptionQris" class="payment-method-card flex items-start p-3 rounded-2xl border-2 border-emerald-500 bg-emerald-50/70 transition cursor-pointer shadow-sm">
                                <input type="radio" name="payment_method_radio" value="qris" checked onchange="onSelectPaymentMethod('qris')" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 mt-0.5 mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-xs text-gray-900 flex items-center space-x-1.5">
                                            <i class="fa-solid fa-qrcode text-emerald-700"></i>
                                            <span>QRIS & E-Wallet (BCA, Mandiri, BRI, DANA, GoPay)</span>
                                        </span>
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-600 text-white tracking-wider">Non-Tunai</span>
                                    </div>
                                    <p class="text-[11px] text-emerald-800/90 mt-1 leading-relaxed">
                                        Bayar praktis dengan scan kode QRIS melalui m-Banking atau E-Wallet.
                                    </p>
                                </div>
                            </label>

                            <!-- Opsi 2: Bayar Tunai di Kasir (Cash) -->
                            <label id="paymentOptionCash" class="payment-method-card flex items-start p-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition cursor-pointer">
                                <input type="radio" name="payment_method_radio" value="cash" onchange="onSelectPaymentMethod('cash')" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 mt-0.5 mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-xs text-gray-900 flex items-center space-x-1.5">
                                            <i class="fa-solid fa-money-bill-wave text-amber-600"></i>
                                            <span>Bayar Tunai di Kasir (Cash)</span>
                                        </span>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">Kasir</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
                                        Datang ke meja kasir dan sebutkan nama Anda untuk menyelesaikan pembayaran tunai.
                                    </p>
                                </div>
                            </label>
                        </div>
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
                    <i id="btnSubmitIcon" class="fa-solid fa-qrcode"></i>
                    <span id="btnSubmitText">Bayar Sekarang (QRIS)</span>
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

    // Toggle Tampilan Pilihan Metode Pembayaran
    function onSelectPaymentMethod(method) {
        const qrisCard = document.getElementById('paymentOptionQris');
        const cashCard = document.getElementById('paymentOptionCash');
        const submitIcon = document.getElementById('btnSubmitIcon');
        const submitText = document.getElementById('btnSubmitText');

        if (method === 'qris') {
            qrisCard.className = 'payment-method-card flex items-start p-3 rounded-2xl border-2 border-emerald-500 bg-emerald-50/70 transition cursor-pointer shadow-sm';
            cashCard.className = 'payment-method-card flex items-start p-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition cursor-pointer';
            if (submitIcon) submitIcon.className = 'fa-solid fa-qrcode';
            if (submitText) submitText.innerText = 'Bayar Sekarang (QRIS)';
        } else {
            cashCard.className = 'payment-method-card flex items-start p-3 rounded-2xl border-2 border-emerald-500 bg-emerald-50/70 transition cursor-pointer shadow-sm';
            qrisCard.className = 'payment-method-card flex items-start p-3 rounded-2xl border border-gray-200 bg-white hover:bg-gray-50 transition cursor-pointer';
            if (submitIcon) submitIcon.className = 'fa-solid fa-money-bill-wave';
            if (submitText) submitText.innerText = 'Pesan & Bayar di Kasir';
        }
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
        const paymentMethod = document.querySelector('input[name="payment_method_radio"]:checked')?.value || 'qris';

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
            payment_method: paymentMethod,
            reward_menu_id: (isMemberEligibleReward && selectedRewardMenuId) ? selectedRewardMenuId : null,
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

                // Jika memilih QRIS: langsung ke halaman pembayaran QRIS dengan nominal otomatis
                // Jika memilih Cash: langsung ke pelacakan status
                const destinationUrl = data.redirect_url || (paymentMethod === 'qris' ? data.payment_url : data.tracking_url);
                window.location.replace(destinationUrl);
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

    // ==========================================
    // LOGIKA MEMBERSHIP & HADIAH PESANAN KE-10
    // ==========================================
    let memberCheckTimer = null;
    let selectedRewardMenuId = null;
    let isMemberEligibleReward = false;

    function initMemberCheck() {
        const phoneInput = document.getElementById('inputCustomerPhone');
        if (!phoneInput) return;

        phoneInput.addEventListener('input', (e) => {
            clearTimeout(memberCheckTimer);
            const val = e.target.value.trim();
            if (val.length >= 10) {
                memberCheckTimer = setTimeout(() => {
                    checkMemberStatus(val);
                }, 400);
            } else {
                hideMemberStatus();
            }
        });

        phoneInput.addEventListener('blur', (e) => {
            const val = e.target.value.trim();
            if (val.length >= 10) {
                checkMemberStatus(val);
            }
        });
    }

    function triggerManualCheckPhone() {
        const phone = document.getElementById('inputCustomerPhone')?.value.trim();
        if (!phone || phone.length < 8) {
            Swal.fire({
                icon: 'info',
                title: 'Nomor WhatsApp',
                text: 'Silakan masukkan nomor WhatsApp Anda minimal 10 digit untuk memeriksa riwayat pesanan.',
                confirmButtonColor: '#16a34a'
            });
            return;
        }
        checkMemberStatus(phone);
    }

    async function checkMembershipTop() {
        const phoneInput = document.getElementById('topCheckPhone');
        const resultBox = document.getElementById('topCheckResult');
        const phone = phoneInput ? phoneInput.value.trim() : '';

        if (!phone || phone.length < 8) {
            Swal.fire({
                icon: 'info',
                title: 'Nomor WhatsApp',
                text: 'Ketik nomor WhatsApp Anda (contoh: 081234567890) untuk melihat jumlah pesanan & status member.',
                confirmButtonColor: '#16a34a'
            });
            return;
        }

        if (resultBox) {
            resultBox.classList.remove('hidden');
            resultBox.innerHTML = '<p class="text-gray-500 flex items-center gap-1.5"><i class="fa-solid fa-spinner fa-spin"></i><span>Memeriksa riwayat pesanan...</span></p>';
        }

        try {
            const res = await fetch(`/api/member/check?phone=${encodeURIComponent(phone)}`);
            const data = await res.json();

            if (!data.success) {
                if (resultBox) {
                    resultBox.innerHTML = `<p class="text-red-600 font-semibold">${data.message || 'Nomor HP tidak valid'}</p>`;
                }
                return;
            }

            // Sinkronkan ke form checkout jika dibuka
            const nameInput = document.getElementById('inputCustomerName');
            const phoneCheckout = document.getElementById('inputCustomerPhone');
            if (phoneCheckout) phoneCheckout.value = data.phone;
            if (nameInput && data.name && !nameInput.value) nameInput.value = data.name;

            if (data.found && data.total_orders > 0) {
                resultBox.innerHTML = `
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 text-xs">Halo, <strong class="text-emerald-800">${data.name || 'Member'}</strong>!</span>
                            <span class="px-2.5 py-0.5 bg-emerald-700 text-white rounded-lg text-xs font-black font-mono shadow-sm">
                                ${data.total_orders}x Pesan
                            </span>
                        </div>
                        <p class="text-xs text-emerald-900 font-medium leading-relaxed">
                            ${data.is_eligible_reward 
                                ? '🎉 <strong class="text-amber-700">SELAMAT!</strong> Ini adalah pemesanan ke-10 Anda. Anda berhak memilih <strong class="text-amber-700">1 Menu GRATIS</strong> saat checkout!' 
                                : 'Nomor WhatsApp Anda telah memesan sebanyak <strong class="font-bold text-emerald-800">' + data.total_orders + ' kali</strong>. Tersisa <strong class="font-bold text-amber-700">' + data.orders_remaining + ' kali pesanan lagi</strong> untuk mendapatkan 1 Menu Pilihan GRATIS!'}
                        </p>
                    </div>
                `;
            } else {
                resultBox.innerHTML = `
                    <div class="space-y-1">
                        <p class="font-bold text-emerald-900"><i class="fa-solid fa-sparkles text-amber-500 mr-1"></i> Nomor Baru Siap Terdaftar!</p>
                        <p class="text-gray-600 text-[11px]">Nomor ini belum memiliki riwayat pesanan. Lakukan pemesanan pertama Anda hari ini untuk mulai mengumpulkan poin menuju pesanan ke-10 GRATIS!</p>
                    </div>
                `;
            }

            // Trigger juga status box di form
            checkMemberStatus(data.phone);
        } catch (e) {
            if (resultBox) {
                resultBox.innerHTML = '<p class="text-red-500">Gagal memeriksa status. Silakan coba lagi.</p>';
            }
        }
    }

    async function checkMemberStatus(phone) {
        const statusBox = document.getElementById('memberStatusBox');
        const loading = document.getElementById('memberLoading');
        const newCard = document.getElementById('memberNewCard');
        const progressCard = document.getElementById('memberProgressCard');
        const rewardCard = document.getElementById('memberRewardCard');
        const nameInput = document.getElementById('inputCustomerName');

        if (!statusBox) return;

        statusBox.classList.remove('hidden');
        loading.classList.remove('hidden');
        newCard.classList.add('hidden');
        progressCard.classList.add('hidden');
        rewardCard.classList.add('hidden');

        try {
            const res = await fetch(`/api/member/check?phone=${encodeURIComponent(phone)}`);
            const data = await res.json();
            loading.classList.add('hidden');

            if (!data.success) {
                statusBox.classList.add('hidden');
                return;
            }

            // Jika member sudah terdaftar sebelumnya:
            if (data.found && data.name) {
                // Kunci/isi otomatis nama pelanggan sesuai pengisian pertama kali agar tidak reset
                if (!nameInput.value || nameInput.value.trim() === '') {
                    nameInput.value = data.name;
                }
            }

            if (data.is_eligible_reward) {
                // PESANAN KE-10: Berhak Menu Gratis!
                isMemberEligibleReward = true;
                rewardCard.classList.remove('hidden');
                renderRewardOptions(data.reward_menus || []);
            } else if (data.found && data.total_orders > 0) {
                // Member dalam progres 1 s/d 8
                isMemberEligibleReward = false;
                progressCard.classList.remove('hidden');

                const greet = document.getElementById('memberGreetName');
                const totalOrdersBadge = document.getElementById('memberTotalOrdersBadge');
                const totalOrdersCount = document.getElementById('memberTotalOrdersCount');
                const cycleBadge = document.getElementById('memberCycleBadge');
                const progressBar = document.getElementById('memberProgressBar');
                const remainingText = document.getElementById('memberRemainingText');

                if (greet) greet.innerText = `Halo, ${data.name || 'Member'}!`;
                if (totalOrdersBadge) totalOrdersBadge.innerText = `${data.total_orders}x Pesan`;
                if (totalOrdersCount) totalOrdersCount.innerText = data.total_orders;
                if (cycleBadge) cycleBadge.innerText = `Siklus: ${data.cycle_order} / 10`;
                if (progressBar) progressBar.style.width = `${data.cycle_order * 10}%`;
                if (remainingText) {
                    remainingText.innerText = `Kurang ${data.orders_remaining} kali pesanan lagi untuk mendapatkan 1 Menu Pilihan GRATIS!`;
                }
            } else {
                // Nomor baru
                isMemberEligibleReward = false;
                newCard.classList.remove('hidden');
            }
        } catch (err) {
            loading.classList.add('hidden');
            statusBox.classList.add('hidden');
        }
    }

    function hideMemberStatus() {
        const statusBox = document.getElementById('memberStatusBox');
        if (statusBox) statusBox.classList.add('hidden');
        isMemberEligibleReward = false;
        selectedRewardMenuId = null;
    }

    function renderRewardOptions(menus) {
        const container = document.getElementById('rewardMenuOptionsContainer');
        if (!container) return;

        if (!menus || menus.length === 0) {
            container.innerHTML = `
                <div class="p-3 bg-white/80 rounded-xl border border-amber-200 text-amber-900 text-center text-xs">
                    <p class="font-bold">Hadiah Spesial Member ke-10</p>
                    <p class="text-[11px] text-amber-700 mt-0.5">Sebutkan nama dan nomor WhatsApp Anda ke barista untuk memilih sajian gratis Anda langsung di tempat!</p>
                </div>
            `;
            return;
        }

        let html = '<p class="text-[11px] font-bold text-amber-900 mb-1.5">Pilih 1 Menu Hadiah Anda:</p>';
        menus.forEach((m, idx) => {
            const isChecked = idx === 0 ? 'checked' : '';
            if (idx === 0) {
                selectedRewardMenuId = m.id;
            }
            html += `
                <label class="flex items-center justify-between p-2.5 bg-white border-2 border-amber-300 rounded-xl cursor-pointer hover:bg-amber-50/50 transition shadow-sm">
                    <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                        <input type="radio" name="reward_menu_radio" value="${m.id}" ${isChecked} onchange="onSelectRewardMenu(${m.id})" class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                        <img src="${m.image_url}" class="w-10 h-10 rounded-lg object-cover bg-gray-100 shrink-0" onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-gray-900 truncate">${m.name}</p>
                            <p class="text-[10px] text-gray-400 line-through">Rp ${Number(m.price).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-sm shrink-0 uppercase tracking-wider">
                        GRATIS Rp 0
                    </span>
                </label>
            `;
        });

        container.innerHTML = html;
    }

    function onSelectRewardMenu(menuId) {
        selectedRewardMenuId = menuId;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadCart();
        initMemberCheck();
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

