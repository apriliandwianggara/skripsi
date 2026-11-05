@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan #' . $order->queue_number . ' - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col py-6 px-4">
    <div class="max-w-md mx-auto w-full">

        <!-- Banner Pop-up Siap Diambil (Otomatis Muncul di HP Pelanggan Tanpa Perlu Klik Pantau) -->
        <div id="readyAlertBanner" class="{{ $order->status === 'siap_diambil' ? '' : 'hidden' }} mb-4 bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800 text-white rounded-3xl p-5 shadow-2xl text-center space-y-3 border-2 border-amber-300">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto text-amber-300 text-3xl shadow-inner">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <h2 class="font-black text-xl text-white font-serif tracking-wide">PESANAN ANDA SIAP DIAMBIL!</h2>
                <p class="text-xs text-emerald-100 mt-1">
                    Silakan menuju ke meja kasir/counter dan tunjukkan nomor antrean Anda.
                </p>
            </div>
            <div class="inline-block bg-white text-emerald-950 font-black text-3xl px-6 py-2 rounded-2xl shadow font-mono">
                {{ $order->queue_number }}
            </div>
        </div>

        <!-- Card Konfirmasi Utama -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header Status Sukses -->
            <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white p-6 text-center relative overflow-hidden">
                <div class="w-14 h-14 bg-white p-1 rounded-2xl mx-auto mb-3 shadow-lg border border-white/40 overflow-hidden flex items-center justify-center">
                    <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>
                <h1 class="font-bold text-xl font-serif">Pesanan Berhasil Diterima!</h1>
                <p class="text-xs text-emerald-200/90 mt-0.5">
                    Pesanan Anda telah masuk ke dalam antrean sistem FIFO
                </p>

                <!-- Box Nomor Antrean Besar -->
                <div class="mt-4 bg-emerald-900/80 border border-emerald-700/80 rounded-2xl p-4 shadow-inner">
                    <span class="text-[11px] uppercase tracking-wider text-emerald-300 font-semibold block mb-0.5">Nomor Antrean Anda</span>
                    <span class="text-5xl font-black text-amber-300 tracking-wider font-mono">{{ $order->queue_number }}</span>
                    <div class="mt-1 flex items-center justify-center space-x-2 text-xs text-emerald-200">
                        <span>{{ $order->order_number }}</span>
                        @if($order->table_number)
                            <span>&bull;</span>
                            <span class="bg-emerald-800 px-2 py-0.5 rounded font-medium">Meja {{ $order->table_number }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Detail Pesanan -->
            <div class="p-6 space-y-4">

                <!-- Info Algoritma FIFO -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3.5 text-xs text-emerald-950 space-y-2">
                    <div class="flex items-center space-x-2 font-bold text-emerald-900">
                        <i class="fa-solid fa-list-ol text-emerald-700"></i>
                        <span>Posisi Antrean FIFO</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-center pt-1">
                        <div class="bg-white rounded-xl p-2 border border-emerald-100">
                            <span class="text-[10px] text-gray-500 block">Pesanan di Depan</span>
                            <span id="confDepanVal" class="font-bold text-emerald-800 text-base font-mono">{{ $queueInfo['pesanan_di_depan'] }}</span>
                        </div>
                        <div class="bg-white rounded-xl p-2 border border-emerald-100">
                            <span class="text-[10px] text-gray-500 block">Estimasi Menunggu</span>
                            <span id="confEstimasiVal" class="font-bold text-emerald-800 text-base font-mono">&plusmn; {{ $queueInfo['estimasi_waktu_menit'] }} mnt</span>
                        </div>
                    </div>
                </div>

                <!-- Info Pemesan -->
                <div class="text-xs space-y-1.5 border-b border-gray-100 pb-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Nama Pemesan:</span>
                        <strong class="text-gray-900 font-semibold">{{ $order->customer_name }}</strong>
                    </div>
                    @if($order->customer_phone)
                        <div class="flex justify-between text-gray-600">
                            <span>No. WhatsApp:</span>
                            <span class="text-gray-800 font-mono">{{ $order->customer_phone }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Waktu Diterima:</span>
                        <span class="text-gray-800">{{ $order->received_at->format('H:i:s') }} WIB ({{ $order->received_at->translatedFormat('d M Y') }})</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Status Saat Ini:</span>
                        <span id="confStatusBadge" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1 animate-pulse"></span>
                            <span id="confStatusText">{{ $order->status_label }}</span>
                        </span>
                    </div>
                </div>

                <!-- Daftar Menu yang Dipesan -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Rincian Menu</h3>
                    <div class="space-y-2.5">
                        @foreach($order->details as $item)
                            <div class="flex items-start justify-between text-xs">
                                <div class="pr-2">
                                    <p class="font-semibold text-gray-800">
                                        <span class="text-emerald-700 font-bold mr-1">{{ $item->quantity }}x</span>
                                        {{ $item->menu_name }}
                                    </p>
                                    @if($item->notes)
                                        <p class="text-[11px] text-gray-400 italic">"{{ $item->notes }}"</p>
                                    @endif
                                </div>
                                <span class="font-semibold text-gray-700 shrink-0">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($order->customer_notes)
                        <div class="mt-3 p-2.5 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-600">
                            <span class="font-semibold text-gray-700">Catatan Khusus:</span>
                            <p class="italic text-[11px] mt-0.5">"{{ $order->customer_notes }}"</p>
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-sm text-gray-900">Total Harga:</span>
                        <span class="font-extrabold text-base text-emerald-800">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Action Button ke Pelacakan Live -->
                <div class="pt-2 space-y-2">
                    <a href="{{ route('customer.track', $order->tracking_token) }}"
                       class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold py-3.5 rounded-2xl text-sm transition shadow-lg shadow-emerald-600/30 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-satellite-dish animate-pulse"></i>
                        <span>Pantau Status Pesanan (Live)</span>
                    </a>

                    <a href="{{ route('customer.qr_required') }}"
                       class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2.5 rounded-2xl text-xs transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Pesan Tambahan? Pindai QR di Meja</span>
                    </a>
                </div>

            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-[11px] text-gray-400 mt-6">
            &copy; {{ date('Y') }} {{ $setting->cafe_name ?? 'Sekarsa Coffee' }} &bull; Sistem Pemesanan FIFO
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderToken = "{{ $order->tracking_token }}";
    let currentStatus = "{{ $order->status }}";
    let chimePlayed = (currentStatus === 'siap_diambil' || currentStatus === 'selesai');

    async function checkConfirmationStatus() {
        try {
            const res = await fetch(`/api/pesanan/${orderToken}/status`);
            if (!res.ok) return;

            const data = await res.json();
            if (!data.success) return;

            const newStatus = data.status;

            // Update info antrean
            const depanEl = document.getElementById('confDepanVal');
            const estimasiEl = document.getElementById('confEstimasiVal');
            if (depanEl) depanEl.innerText = data.pesanan_di_depan;
            if (estimasiEl) estimasiEl.innerText = `± ${data.estimasi_waktu_menit} mnt`;

            if (newStatus !== currentStatus) {
                currentStatus = newStatus;

                const badge = document.getElementById('confStatusBadge');
                const text = document.getElementById('confStatusText');
                if (text) text.innerText = data.status_label;

                // Jika status Siap Diambil
                if (newStatus === 'siap_diambil') {
                    const banner = document.getElementById('readyAlertBanner');
                    if (banner) banner.classList.remove('hidden');

                    if (badge) {
                        badge.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800';
                    }

                    if (!chimePlayed) {
                        chimePlayed = true;
                        // Putar nada dering pesanan selesai
                        playSekarsaChime();

                        Swal.fire({
                            title: 'PESANAN ANDA SIAP!',
                            html: `Nomor Antrean <b>#${data.queue_number}</b> siap diambil di meja kasir.`,
                            icon: 'success',
                            confirmButtonColor: '#16a34a',
                            confirmButtonText: 'Baik, Saya Menuju Kasir'
                        });
                    }
                } else if (newStatus === 'diproses') {
                    if (badge) {
                        badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800';
                    }
                } else if (newStatus === 'selesai') {
                    const banner = document.getElementById('readyAlertBanner');
                    if (banner) banner.classList.add('hidden');
                    if (badge) {
                        badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800';
                    }
                }
            }
        } catch (e) {
            console.warn('Sync status konfirmasi gagal:', e);
        }
    }

    // Auto Polling Real-time setiap 2.5 Detik
    setInterval(checkConfirmationStatus, 2500);
</script>
@endpush

