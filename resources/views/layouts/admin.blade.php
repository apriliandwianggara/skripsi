<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Sekarsa Coffee')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sekarsa: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        serif: ['"Playfair Display"', 'Georgia', 'serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        .nav-active {
            background-color: #16a34a;
            color: white;
            font-weight: 600;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }
        .nav-item {
            color: rgba(209,250,229,0.8);
        }
        .nav-item:hover {
            background-color: #14532d;
            color: white;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 antialiased min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <header class="md:hidden bg-sekarsa-900 text-white flex items-center justify-between p-4 sticky top-0 z-40 shadow">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-white shadow">
                <i class="fa-solid fa-mug-hot text-sm"></i>
            </div>
            <span class="font-bold text-lg tracking-wide">SEKARSA COFFEE</span>
        </div>
        <button id="mobileMenuBtn" class="text-white hover:text-emerald-300 focus:outline-none p-1">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
    </header>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="hidden md:flex flex-col w-64 bg-sekarsa-950 text-emerald-100 shrink-0 min-h-screen sticky top-0 shadow-2xl z-30">
        <!-- Logo Brand -->
        <div class="p-6 border-b border-sekarsa-800/80 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white shadow-lg">
                <i class="fa-solid fa-mug-saucer text-lg"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg text-white tracking-wider font-serif">SEKARSA</h1>
                <p class="text-xs text-emerald-400 font-medium">Coffee & Eatery</p>
            </div>
        </div>

        <!-- User Role Profile Pill -->
        <div class="px-6 py-4 border-b border-sekarsa-800/60 bg-sekarsa-900/60 flex items-center space-x-3">
            <div class="w-9 h-9 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                <div class="flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs text-emerald-300 capitalize font-medium">
                        {{ auth()->user()->role === 'admin' ? 'Admin / Owner' : 'Pegawai / Barista' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto sidebar-scroll text-sm">

            @if(auth()->user()->role === 'admin')
            {{-- ===== ADMIN MENU ===== --}}
            <div class="px-3 pt-2 pb-1 text-xs font-semibold uppercase tracking-wider text-emerald-500">Admin Dashboard</div>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                <span>Ringkasan Bisnis</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-emerald-500">Katalog</div>

            <a href="{{ route('admin.menus.index') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.menus.*') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-utensils w-5 text-center"></i>
                <span>Manajemen Menu</span>
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-layer-group w-5 text-center"></i>
                <span>Kategori Menu</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-emerald-500">Pesanan</div>

            <a href="{{ route('admin.orders.index') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-clipboard-list w-5 text-center"></i>
                <span>Manajemen Pesanan</span>
            </a>

            <a href="{{ route('admin.orders.history') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.history') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                <span>Riwayat Pesanan</span>
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-emerald-500">Manajemen</div>

            <a href="{{ route('admin.users.index') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-users-gear w-5 text-center"></i>
                <span>Manajemen Pengguna</span>
            </a>

            <a href="{{ route('admin.reports') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports*') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-file-invoice-dollar w-5 text-center"></i>
                <span>Laporan Penjualan</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.settings.*') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-sliders w-5 text-center"></i>
                <span>Pengaturan Sistem</span>
            </a>

            @else
            {{-- ===== PEGAWAI / BARISTA MENU ===== --}}
            <div class="px-3 pt-2 pb-1 text-xs font-semibold uppercase tracking-wider text-emerald-500">Operasional</div>

            <a href="{{ route('pegawai.dashboard') }}"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('pegawai.dashboard') ? 'nav-active' : 'nav-item' }}">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-list-ol w-5 text-center"></i>
                    <span>Antrean FIFO</span>
                </div>
                <span class="bg-amber-500 text-white text-[11px] font-bold px-2 py-0.5 rounded-full" id="sidebarQueueBadge">
                    {{ \App\Models\Order::whereIn('status', ['menunggu', 'diproses'])->count() }}
                </span>
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-emerald-500">Riwayat</div>

            <a href="{{ route('admin.orders.history') }}"
               class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.orders.history') ? 'nav-active' : 'nav-item' }}">
                <i class="fa-solid fa-clock-rotate-left w-5 text-center"></i>
                <span>Riwayat Selesai</span>
            </a>
            @endif

            <!-- Lihat Tampilan Pelanggan -->
            <div class="pt-4 border-t border-sekarsa-800/50 mt-2">
                <a href="{{ route('customer.order') }}" target="_blank"
                   class="flex items-center space-x-3 px-3.5 py-2 rounded-xl text-emerald-300 hover:bg-sekarsa-900 hover:text-white transition text-xs">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i>
                    <span>Tampilan Pelanggan</span>
                </a>
            </div>
        </nav>

        <!-- Logout Button -->
        <div class="p-4 border-t border-sekarsa-800/80">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 bg-red-600/20 text-red-300 hover:bg-red-600 hover:text-white rounded-xl transition text-sm font-semibold">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-x-hidden">
        <!-- Topbar Desktop -->
        <header class="bg-white border-b border-gray-200 px-6 py-3.5 hidden md:flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <span class="text-sm font-medium text-gray-500">
                    <i class="fa-regular fa-calendar-days text-emerald-600 mr-1.5"></i>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-semibold text-emerald-800" id="liveClock">
                    {{ now()->format('H:i:s') }} WIB
                </span>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('customer.order') }}" target="_blank"
                   class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 px-3 py-1.5 rounded-lg transition">
                    <i class="fa-solid fa-mobile-screen mr-1.5"></i> Mode QR Pelanggan
                </a>

                <div class="h-6 w-px bg-gray-200"></div>

                <div class="text-right">
                    <p class="text-xs font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] font-semibold text-emerald-600 uppercase">{{ auth()->user()->role }}</p>
                </div>
            </div>
        </header>

        <!-- Content View -->
        <main class="flex-1 p-4 md:p-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-3 px-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} Sistem Pemesanan QR Code Sekarsa Coffee &bull; Tugas Akhir Skripsi
        </footer>
    </div>

    <!-- Scripts -->
    <script>
        // Live Clock
        setInterval(() => {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.innerText = timeStr;
        }, 1000);

        // Mobile Menu Toggle
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        if (mobileBtn && sidebar) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
            });
        }

        // Global Alert handler
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#16a34a',
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#dc2626'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    confirmButtonColor: '#dc2626'
                });
            @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
