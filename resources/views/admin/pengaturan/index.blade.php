@extends('layouts.admin')

@section('title', 'Profil & Pengaturan Toko - Sekarsa Coffee')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <h1 class="text-2xl font-black text-gray-900 font-serif">Profil & Pengaturan Toko</h1>
        <p class="text-xs text-gray-500 mt-0.5">Konfigurasi identitas Sekarsa Coffee, pesan struk, dan gambar barcode QRIS statis.</p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-200/80 shadow-sm">
        <form action="{{ route('admin.pengaturan.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nama_cafe" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Nama Cafe <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_cafe" id="nama_cafe" required value="{{ old('nama_cafe', $pengaturan->nama_cafe) }}"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 font-semibold">
                </div>

                <div>
                    <label for="tagline" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tagline / Slogan</label>
                    <input type="text" name="tagline" id="tagline" value="{{ old('tagline', $pengaturan->tagline) }}"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label for="alamat" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Alamat Lengkap Cafe</label>
                <textarea name="alamat" id="alamat" rows="2"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('alamat', $pengaturan->alamat) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="telepon" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon', $pengaturan->telepon) }}"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="instagram" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Akun Instagram</label>
                    <input type="text" name="instagram" id="instagram" value="{{ old('instagram', $pengaturan->instagram) }}" placeholder="@sekarsacoffee"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="estimasi_menit_per_antrean" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Estimasi Waktu FIFO per Antrean (Menit) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="estimasi_menit_per_antrean" id="estimasi_menit_per_antrean" required min="1" max="30"
                        value="{{ old('estimasi_menit_per_antrean', $pengaturan->estimasi_menit_per_antrean) }}"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <p class="text-[11px] text-gray-400 mt-1">Dikalikan posisi antrean untuk perkiraan waktu tunggu pelanggan.</p>
                </div>

                <div>
                    <label for="qris_image" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Unggah Foto / SVG QRIS Toko</label>
                    <input type="file" name="qris_image" id="qris_image" accept="image/*"
                        class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-gray-400 mt-1">Muncul saat pelanggan memilih pembayaran QRIS di HP.</p>
                </div>
            </div>

            <div>
                <label for="pesan_struk" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Pesan Ucapan di Kaki Struk</label>
                <textarea name="pesan_struk" id="pesan_struk" rows="2"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('pesan_struk', $pengaturan->pesan_struk) }}</textarea>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
