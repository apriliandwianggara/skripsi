@extends('layouts.app')

@section('title', 'Akses Pemesanan Terkunci - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-gradient-to-b from-sekarsa-950 via-sekarsa-900 to-slate-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl overflow-hidden border border-emerald-900/20 text-center relative animate-fade-in">
        
        <!-- Top Ornamental Header -->
        <div class="bg-gradient-to-b from-sekarsa-900 to-emerald-800 text-white p-8 relative overflow-hidden">
            <!-- Glow background effect -->
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-emerald-500/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-amber-500/20 rounded-full blur-xl"></div>

            <div class="relative z-10 flex flex-col items-center">
                <div class="w-20 h-20 bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl flex items-center justify-center mb-4 shadow-xl ring-8 ring-white/5">
                    <i class="fa-solid fa-qrcode text-4xl text-amber-300 animate-pulse"></i>
                </div>
                
                <span class="px-3 py-1 bg-amber-400 text-slate-950 text-[10px] font-extrabold uppercase tracking-widest rounded-full shadow-sm mb-2">
                    <i class="fa-solid fa-lock mr-1"></i> Akses Meja Diperlukan
                </span>

                <h1 class="text-2xl font-bold font-serif tracking-tight text-white">
                    {{ $setting->cafe_name ?? 'Sekarsa Coffee' }}
                </h1>
                <p class="text-xs text-emerald-200 mt-1">
                    {{ $setting->cafe_tagline ?? 'Warmth, Taste & Harmony in Every Sip' }}
                </p>
            </div>
        </div>

        <!-- Body Message -->
        <div class="p-6 sm:p-8 space-y-6">
            <div class="space-y-2">
                <h2 class="text-lg font-extrabold text-gray-900">
                    Pemesanan Wajib Melalui Scan QR
                </h2>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Untuk menjaga ketertiban antrean <strong class="text-emerald-800 font-semibold">First-In First-Out (FIFO)</strong> dan memastikan pesanan dibuat langsung di tempat, menu digital hanya dapat diakses dengan memindai <strong>Kode QR resmi</strong> yang terpasang di meja kafe.
                </p>
            </div>

            <!-- Petunjuk 3 Langkah Mudah -->
            <div class="bg-emerald-50/60 rounded-2xl p-4 border border-emerald-100 text-left space-y-3">
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-900 flex items-center">
                    <i class="fa-solid fa-circle-info mr-1.5 text-emerald-600"></i> Cara Membuka Menu Pemesanan:
                </p>
                <div class="space-y-2.5 text-xs text-gray-700">
                    <div class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">1</span>
                        <div>
                            <p class="font-semibold text-gray-900">Buka Kamera Ponsel</p>
                            <p class="text-[11px] text-gray-500">Gunakan kamera bawaan HP Anda atau aplikasi pemindai QR.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">2</span>
                        <div>
                            <p class="font-semibold text-gray-900">Arahkan ke Standee Meja</p>
                            <p class="text-[11px] text-gray-500">Arahkan kamera ke Kode QR akrilik yang tersedia di meja Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <span class="w-6 h-6 rounded-full bg-emerald-700 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">3</span>
                        <div>
                            <p class="font-semibold text-gray-900">Ketuk Tautan Pemesanan</p>
                            <p class="text-[11px] text-gray-500">Menu akan langsung terbuka secara otomatis di layar HP Anda.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Petugas / Staf Bantuan -->
            <div class="pt-2 border-t border-gray-150 flex flex-col items-center space-y-3">
                <p class="text-[11px] text-gray-500">
                    Butuh bantuan atau standee QR di meja Anda tidak terbaca?
                    <br>
                    <span class="text-emerald-700 font-semibold">Silakan hubungi kasir atau barista kami.</span>
                </p>

                @if(auth()->check())
                    <div class="w-full pt-2">
                        <a href="{{ route('customer.order', ['token' => $setting->qr_access_key]) }}" 
                           class="w-full py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow">
                            <i class="fa-solid fa-user-shield"></i>
                            <span>Mode Petugas: Masuk ke Menu Pelanggan</span>
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <!-- Footer Bar -->
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-150 text-[10px] text-gray-400">
            Sistem Pemesanan Digital Berbasis FIFO &copy; {{ date('Y') }} {{ $setting->cafe_name ?? 'Sekarsa Coffee' }}
        </div>

    </div>
</div>
@endsection

