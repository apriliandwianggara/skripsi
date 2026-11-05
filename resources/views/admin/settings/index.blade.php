@extends('layouts.admin')

@section('title', 'Pengaturan Sistem & Tampilan - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-sliders text-emerald-600"></i>
                <span>Pengaturan Sistem & Tampilan Website</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Kelola identitas, logo, foto banner, teks pelanggan, radius GPS, alarm notifikasi, dan Kode QR Meja.
            </p>
        </div>

        <a href="{{ route('admin.settings.qr_print') }}" target="_blank"
           class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
            <i class="fa-solid fa-qrcode"></i>
            <span>Cetak Standee QR Meja</span>
        </a>
    </div>

    <!-- 2 Kolom: Form Pengaturan & QR Code Preview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Form Pengaturan (2 spans) -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- KARTU 1: IDENTITAS & LOGO/BANNER -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-800 pb-2 border-b border-gray-100 flex items-center">
                        <i class="fa-solid fa-palette mr-2 text-emerald-600"></i> Logo, Banner & Identitas Kafe
                    </h3>

                    <!-- Upload Logo & Banner -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                        <!-- Logo Kafe -->
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/80 space-y-2">
                            <label class="block text-[11px] font-bold uppercase text-gray-700">Logo Kafe (Resmi)</label>
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-14 rounded-xl bg-white p-1 border border-gray-300 shadow-sm overflow-hidden shrink-0 flex items-center justify-center">
                                    <img src="{{ $setting->logo_url }}" alt="Logo Kafe" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input type="file" name="logo_file" accept="image/*" class="w-full text-[11px] text-gray-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                                    <span class="text-[9px] text-gray-400 block mt-0.5">Format: JPG, PNG, WEBP (Maks 3MB)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Banner Suasana Kafe -->
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200/80 space-y-2">
                            <label class="block text-[11px] font-bold uppercase text-gray-700">Foto Banner / Suasana</label>
                            <div class="flex items-center space-x-3">
                                <div class="w-14 h-14 rounded-xl bg-white border border-gray-300 shadow-sm overflow-hidden shrink-0 flex items-center justify-center">
                                    <img src="{{ $setting->banner_url }}" alt="Banner Kafe" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input type="file" name="banner_file" accept="image/*" class="w-full text-[11px] text-gray-600 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
                                    <span class="text-[9px] text-gray-400 block mt-0.5">Tampil di header pelanggan & login</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Kafe <span class="text-red-500">*</span></label>
                            <input type="text" name="cafe_name" value="{{ old('cafe_name', $setting->cafe_name) }}" required class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Slogan / Tagline</label>
                            <input type="text" name="cafe_tagline" value="{{ old('cafe_tagline', $setting->cafe_tagline) }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">No. WhatsApp</label>
                            <input type="text" name="cafe_phone" value="{{ old('cafe_phone', $setting->cafe_phone) }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Instagram</label>
                            <input type="text" name="cafe_instagram" value="{{ old('cafe_instagram', $setting->cafe_instagram) }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Jam Buka Kafe</label>
                            <input type="text" name="cafe_opening_hours" value="{{ old('cafe_opening_hours', $setting->cafe_opening_hours ?? 'Setiap Hari: 08:00 - 23:00 WIB') }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Alamat Kafe</label>
                        <textarea name="cafe_address" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('cafe_address', $setting->cafe_address) }}</textarea>
                    </div>
                </div>

                <!-- KARTU 2: KONTEN TEKS PELANGGAN (CMS) -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-800 pb-2 border-b border-gray-100 flex items-center">
                        <i class="fa-solid fa-pen-to-square mr-2 text-emerald-600"></i> Konten Teks Halaman Pelanggan
                    </h3>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Judul Banner Pelanggan (Hero Title)</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title ?? 'Selamat Datang di Sekarsa Coffee') }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Subjudul / Deskripsi Sambutan</label>
                        <textarea name="hero_subtitle" rows="2" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Teks Pengumuman Berjalan (Running Text / Promo)</label>
                        <input type="text" name="announcement_text" value="{{ old('announcement_text', $setting->announcement_text) }}" placeholder="Contoh: Promo Spesial Hari Ini: Diskon 20% untuk Manual Brew!" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="text-[10px] text-gray-400 mt-1 block">Kosongkan jika tidak ada pengumuman berjalan.</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Teks Hak Cipta / Footer</label>
                        <input type="text" name="footer_text" value="{{ old('footer_text', $setting->footer_text ?? '© 2026 Sekarsa Coffee & Space. All rights reserved.') }}" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <!-- KARTU 3: NOTIFIKASI ALARM & SUARA KERAS -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-800 pb-2 border-b border-gray-100 flex items-center justify-between">
                        <span class="flex items-center">
                            <i class="fa-solid fa-bell mr-2 text-amber-500"></i> Alarm Notifikasi Pesanan Siap (Ponsel Pelanggan)
                        </span>
                        <button type="button" onclick="testAlarmSound()" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-slate-900 rounded-lg text-[11px] font-bold transition flex items-center space-x-1 shadow-sm">
                            <i class="fa-solid fa-volume-high"></i>
                            <span>Tes Suara Alarm</span>
                        </button>
                    </h3>

                    <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-2xl space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="pr-3">
                                <label for="alarm_sound_enabled" class="text-xs font-extrabold text-amber-950 flex items-center cursor-pointer">
                                    <i class="fa-solid fa-volume-high text-amber-600 mr-1.5"></i>
                                    Aktifkan Alarm Dering Berulang di Ponsel Pelanggan
                                </label>
                                <p class="text-[11px] text-amber-900/80 mt-1 leading-relaxed">
                                    Ketika kasir mengubah status pesanan ke <strong>"Siap Diambil"</strong>, HP pelanggan akan <strong>berdering kencang tiada henti + bergetar + memunculkan pop-up darurat</strong> sampai pelanggan menekan tombol <em>"Ambil Sekarang"</em>.
                                </p>
                            </div>
                            <div class="pt-0.5">
                                <input type="checkbox" id="alarm_sound_enabled" name="alarm_sound_enabled" value="1" {{ old('alarm_sound_enabled', $setting->alarm_sound_enabled ?? true) ? 'checked' : '' }}
                                       class="w-5 h-5 rounded text-amber-600 focus:ring-amber-500 border-gray-300 transition cursor-pointer">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            Template Notifikasi WhatsApp Pengambilan Pesanan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="whatsapp_notification_template" rows="4" required class="w-full text-xs p-3 border border-gray-300 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('whatsapp_notification_template', $setting->whatsapp_notification_template) }}</textarea>
                        <div class="mt-1 p-2 bg-emerald-50 rounded-lg text-[10px] text-emerald-800 space-y-0.5">
                            <p class="font-bold">Variabel otomatis:</p>
                            <p><code>{nama_pelanggan}</code>, <code>{nomor_antrean}</code>, <code>{nomor_pesanan}</code>, <code>{meja}</code>, <code>{daftar_menu}</code>, <code>{total_harga}</code>, <code>{nama_kafe}</code></p>
                        </div>
                    </div>
                </div>

                <!-- KARTU 4: PROTEKSI QR GATE & FIFO SETTINGS -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-800 pb-2 border-b border-gray-100 flex items-center">
                        <i class="fa-solid fa-shield-halved mr-2 text-emerald-600"></i> Keamanan Akses Pemesanan Meja (QR Gate)
                    </h3>

                    <!-- Fitur Proteksi Akses QR Meja (Selalu Aktif) -->
                    <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-3">
                        <div class="flex items-start justify-between">
                            <div class="pr-3">
                                <label for="require_qr_scan" class="text-xs font-extrabold text-emerald-950 flex items-center cursor-pointer">
                                    <i class="fa-solid fa-qrcode text-emerald-600 mr-1.5"></i>
                                    Proteksi Gerbang QR Meja (Wajib Scan Fisik di Meja)
                                </label>
                                <p class="text-[11px] text-emerald-800/80 mt-1 leading-relaxed">
                                    Pelanggan <strong>wajib memindai QR fisik di meja</strong> untuk membuka menu dan tidak dapat mengetik tautan secara manual di browser. Setelah pesanan berhasil dikirim, sesi ditutup seketika dan jika pelanggan ingin memesan lagi di lain waktu, pelanggan wajib memindai QR fisik di meja kembali.
                                </p>
                            </div>
                            <div class="pt-0.5">
                                <input type="checkbox" id="require_qr_scan" name="require_qr_scan" value="1" checked disabled
                                       class="w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300 opacity-80 cursor-not-allowed">
                                <input type="hidden" name="require_qr_scan" value="1">
                            </div>
                        </div>

                        <div class="pt-2 border-t border-emerald-100">
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">
                                Token Kunci Keamanan QR Meja
                            </label>
                            <input type="text" name="qr_access_key" value="{{ old('qr_access_key', $setting->qr_access_key ?? 'sekarsa_qr_cafe_2026') }}" 
                                   class="w-full text-xs p-2.5 bg-white border border-gray-300 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[10px] text-gray-400 mt-1 block">Token keamanan unik yang ditautkan ke QR fisik di meja kafe.</span>
                        </div>
                    </div>



                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                            URL Tujuan Kode QR Pemesanan (Permanen) <span class="text-red-500">*</span>
                        </label>
                        <input type="url" name="qr_permanent_url" value="{{ old('qr_permanent_url', $setting->qr_permanent_url) }}" required class="w-full text-xs p-3 border border-gray-300 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <span class="text-[10px] text-gray-400 mt-1 block">Contoh online: <code>http://sekarsacoffee.gt.tc/scan?</code></span>
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
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-lg flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Seluruh Perubahan</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Preview QR Code Permanen -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm text-center space-y-4 sticky top-6">
                <div class="flex items-center justify-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white p-0.5 border border-gray-200 shadow-sm flex items-center justify-center overflow-hidden">
                        <img src="{{ $setting->logo_url }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <span class="text-emerald-950 font-bold text-sm">Kode QR Meja</span>
                </div>

                <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 flex flex-col items-center">
                    <div class="w-48 h-48 bg-white p-2 rounded-xl shadow-inner flex items-center justify-center overflow-hidden [&>svg]:w-full [&>svg]:h-full [&>svg]:block">
                        {!! $qrSvg !!}
                    </div>
                    <span class="mt-2 text-[10px] font-bold uppercase tracking-wider text-emerald-800">
                        Kode QR Meja Aktif
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
                        <span>Uji Coba Buka Link QR</span>
                    </a>
                </div>

                <div class="text-[11px] text-gray-500 text-left bg-gray-50 p-3 rounded-xl border border-gray-150 space-y-1">
                    <p class="font-bold text-gray-700">Tips Penggunaan:</p>
                    <p>&bull; Kode QR ini permanen dan dapat dicetak di standee meja.</p>
                    <p>&bull; Anda bisa mengatur radius kafe dan suara alarm sesuai preferensi.</p>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
function testAlarmSound() {
    startSekarsaContinuousAlarm();
    Swal.fire({
        title: '🔔 Alarm Sedang Berdering!',
        text: 'Ini adalah simulasi suara kencang yang akan berbunyi di ponsel pelanggan saat pesanan siap diambil.',
        icon: 'info',
        confirmButtonColor: '#dc2626',
        confirmButtonText: 'Matikan Alarm Sekarang'
    }).then(() => {
        stopSekarsaContinuousAlarm();
    });
}
</script>
@endsection
