@extends('layouts.app')

@section('title', 'Masuk Petugas - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen relative flex flex-col justify-center items-center px-4 py-8">
    <!-- Background Foto Kafe Sekarsa & Gradien Gelap -->
    <img src="{{ $setting->banner_url ?? asset('images/sekarsa-cafe.jpg') }}" alt="Sekarsa Cafe" class="absolute inset-0 w-full h-full object-cover object-center filter brightness-40">
    <div class="absolute inset-0 bg-gradient-to-br from-sekarsa-950/90 via-sekarsa-900/85 to-emerald-950/95 backdrop-blur-[2px]"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-100 z-10">
        <!-- Header Brand with Sekarsa Logo -->
        <div class="bg-gradient-to-r from-sekarsa-900 to-emerald-800 p-8 text-center text-white relative">
            <div class="w-20 h-20 mx-auto mb-3 rounded-2xl bg-white p-1.5 flex items-center justify-center shadow-xl border border-white/40 overflow-hidden">
                <img src="{{ $setting->logo_url ?? asset('images/sekarsa-logo.jpg') }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-bold font-serif tracking-wider text-white">{{ strtoupper($setting->cafe_name ?? 'SEKARSA COFFEE') }}</h1>
            <p class="text-xs text-emerald-200 mt-1">Portal Masuk Petugas & Administrator</p>
        </div>

        <!-- Form Body -->
        <div class="p-8">
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Username atau Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <input type="text" name="login" id="login" required autofocus
                            value="{{ old('login') }}"
                            placeholder="admin atau pegawai"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                        <span>Ingat Sesi Masuk</span>
                    </label>
                    <a href="{{ route('customer.order') }}" class="text-emerald-700 font-semibold hover:underline">
                        &larr; Halaman Pelanggan
                    </a>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl shadow-lg shadow-emerald-700/30 transition duration-200 flex items-center justify-center space-x-2 text-sm">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Demo Account Helper (Multi-Kasir untuk Sidang Skripsi) -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-xs text-center font-bold text-gray-500 uppercase tracking-wider mb-3">Akun Akses Multi-Kasir (Sidang Skripsi):</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillDemo('admin', 'password123')"
                        class="p-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-900 rounded-xl text-xs text-left transition">
                        <p class="font-bold flex items-center"><i class="fa-solid fa-crown text-amber-500 mr-1.5"></i> Owner/Admin</p>
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">admin</p>
                    </button>
                    <button type="button" onclick="fillDemo('kasir1', 'password123')"
                        class="p-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-900 rounded-xl text-xs text-left transition">
                        <p class="font-bold flex items-center"><i class="fa-solid fa-mug-hot text-blue-600 mr-1.5"></i> Kasir 1 (Bar)</p>
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">kasir1</p>
                    </button>
                    <button type="button" onclick="fillDemo('kasir2', 'password123')"
                        class="p-2.5 bg-teal-50 hover:bg-teal-100 border border-teal-200 text-teal-900 rounded-xl text-xs text-left transition">
                        <p class="font-bold flex items-center"><i class="fa-solid fa-mug-hot text-teal-600 mr-1.5"></i> Kasir 2 (Espresso)</p>
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">kasir2</p>
                    </button>
                    <button type="button" onclick="fillDemo('kasir3', 'password123')"
                        class="p-2.5 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-900 rounded-xl text-xs text-left transition">
                        <p class="font-bold flex items-center"><i class="fa-solid fa-mug-hot text-indigo-600 mr-1.5"></i> Kasir 3 (Brew)</p>
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">kasir3</p>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-6 text-xs text-emerald-200/80 text-center">
        Perancangan Sistem Pemesanan Digital Berbasis FIFO &bull; Sekarsa Coffee
    </p>
</div>

@push('scripts')
<script>
    function fillDemo(username, password) {
        document.getElementById('login').value = username;
        document.getElementById('password').value = password;
    }
</script>
@endpush
@endsection
