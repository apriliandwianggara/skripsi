@extends('layouts.app')

@section('title', 'Masuk Petugas - Sekarsa Coffee')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sekarsa-950 via-sekarsa-900 to-emerald-900 flex flex-col justify-center items-center px-4 py-8 relative">
    <!-- Background Coffee Art Elements -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#86efac_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-100 z-10">
        <!-- Header Brand -->
        <div class="bg-gradient-to-r from-sekarsa-900 to-emerald-800 p-8 text-center text-white relative">
            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 shadow-inner">
                <i class="fa-solid fa-mug-hot text-3xl text-emerald-300"></i>
            </div>
            <h1 class="text-2xl font-bold font-serif tracking-wider">SEKARSA COFFEE</h1>
            <p class="text-xs text-emerald-200 mt-1">Portal Masuk Petugas Kasir & Administrator</p>
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
                            placeholder="admin atau kasir"
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
                        &larr; Halaman Menu
                    </a>
                </div>

                <button type="submit" class="w-full py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl shadow-lg shadow-emerald-700/30 transition duration-200 flex items-center justify-center space-x-2 text-sm">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Demo Account Helper (Sangat membantu dosen penguji saat sidang skripsi) -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <p class="text-xs text-center font-bold text-gray-500 uppercase tracking-wider mb-3">Akun Akses Cepat Demo (Skripsi):</p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" onclick="fillDemo('admin', 'password123')"
                        class="p-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-900 rounded-xl text-xs text-left transition">
                        <p class="font-bold flex items-center"><i class="fa-solid fa-crown text-amber-500 mr-1.5"></i> Admin/Owner</p>
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">admin</p>
                    </button>
                    <button type="button" onclick="fillDemo('kasir', 'password123')"
                        class="p-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-900 rounded-xl text-xs text-left transition">
                        <p class="font-bold flex items-center"><i class="fa-solid fa-cash-register text-blue-600 mr-1.5"></i> Kasir Utama</p>
                        <p class="text-[11px] text-gray-500 font-mono mt-0.5">kasir</p>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <p class="mt-6 text-xs text-emerald-200/80 text-center">
        Sistem Pemesanan Minuman Menggunakan Algoritma FIFO &bull; Sekarsa Coffee
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
