@extends('layouts.admin')

@section('title', 'Pengaturan Sistem - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-sliders text-emerald-600"></i>
                <span>Pengaturan Sistem & Kode QR</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Kelola identitas Sekarsa Coffee, template notifikasi WhatsApp, estimasi antrean FIFO, dan Kode QR Permanen.
            </p>
        </div>

        <a href="{{ route('admin.settings.qr_print') }}" target="_blank"
           class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
            <i class="fa-solid fa-qrcode"></i>
            <span>Cetak Standee QR Permanen</span>
        </a>
    </div>

    <!-- 2 Kolom: Form Pengaturan & QR Code Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Form Pengaturan (2 spans) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h3 class="font-bold text-sm uppercase tracking-wider text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
                    <i class="fa-solid fa-store mr-2 text-emerald-600"></i> Profil & Identitas Kafe
                </h3>

                <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Kafe <span class="text-red-500">*</span></label>
                        <input type="text" name="cafe_name" value="{{ old('cafe_name', $setting->cafe_name) }}" required class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Slogan / Tagline</label>
                        <input type="text" name="cafe_tagline" value="{{ old('cafe_tagline', $setting->cafe_tagline) }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">No. WhatsApp Kafe</label>
                            <input type="text" name="cafe_phone" value="{{ old('cafe_phone', $setting->cafe_phone) }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Akun Instagram</label>
                            <input type="text" name="cafe_instagram" value="{{ old('cafe_instagram', $setting->cafe_instagram) }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Alamat Kafe</label>
                        <textarea name="cafe_address" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('cafe_address', $setting->cafe_address) }}</textarea>
                    </div>

                    <!-- Pengaturan Parameter FIFO & QR -->
                    <div class="pt-4 border-t border-gray-100 space-y-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-emerald-800">
                            <i class="fa-solid fa-network-wired mr-1"></i> Parameter FIFO & Kode QR Permanen
                        </h4>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                                URL Tujuan Kode QR Pemesanan (Permanen) <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="qr_permanent_url" value="{{ old('qr_permanent_url', $setting->qr_permanent_url) }}" required class="w-full text-xs p-3 border border-gray-300 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[10px] text-gray-400 mt-1 block">URL ini yang akan di-encode ke dalam Kode QR Permanen yang dicetak dan ditempel di meja kafe selamanya.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                                Estimasi Menit per Antrean FIFO <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="estimated_minutes_per_queue" value="{{ old('estimated_minutes_per_queue', $setting->estimated_minutes_per_queue) }}" required min="1" max="30" class="w-28 text-xs p-3 border border-gray-300 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <span class="text-xs text-gray-500">Menit per pesanan antrean aktif</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                                Template Notifikasi WhatsApp Pengambilan Pesanan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="whatsapp_notification_template" rows="5" required class="w-full text-xs p-3 border border-gray-300 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('whatsapp_notification_template', $setting->whatsapp_notification_template) }}</textarea>
                            <div class="mt-1 p-2 bg-emerald-50 rounded-lg text-[10px] text-emerald-800 space-y-0.5">
                                <p class="font-bold">Variabel yang dapat digunakan:</p>
                                <p><code>{nama_pelanggan}</code>, <code>{nomor_antrean}</code>, <code>{nomor_pesanan}</code>, <code>{meja}</code>, <code>{daftar_menu}</code>, <code>{total_harga}</code>, <code>{nama_kafe}</code></p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md flex items-center space-x-1.5">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>Simpan Perubahan Pengaturan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Kolom Preview QR Code Permanen -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm text-center space-y-4">
                <div class="flex items-center justify-center space-x-2 text-emerald-900 font-bold text-sm">
                    <i class="fa-solid fa-qrcode text-lg text-emerald-600"></i>
                    <span>Kode QR Pemesanan</span>
                </div>

                <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 flex flex-col items-center">
                    <div class="w-48 h-48 bg-white p-2 rounded-xl shadow-inner flex items-center justify-center">
                        {!! $qrSvg !!}
                    </div>
                    <span class="mt-2 text-[10px] font-bold uppercase tracking-wider text-emerald-800">
                        Dipakai Selamanya
                    </span>
                    <span class="text-[9px] text-gray-400 break-all mt-0.5 max-w-xs font-mono">
                        {{ $permanentUrl }}
                    </span>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('admin.settings.qr_print') }}" target="_blank"
                       class="w-full py-2.5 bg-emerald-800 hover:bg-emerald-900 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Standee Akrilik Meja</span>
                    </a>

                    <a href="{{ $permanentUrl }}" target="_blank"
                       class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Uji Coba Buka Link</span>
                    </a>
                </div>

                <div class="text-[11px] text-gray-500 text-left bg-gray-50 p-3 rounded-xl border border-gray-150 space-y-1">
                    <p class="font-bold text-gray-700">Petunjuk Cetak QR:</p>
                    <p>&bull; Kode QR ini permanen dan tidak akan pernah kedaluwarsa.</p>
                    <p>&bull; Tempelkan pada akrilik meja pelanggan untuk dipindai.</p>
                    <p>&bull; Anda juga dapat menambahkan parameter meja dengan format: <code>?meja=01</code></p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

