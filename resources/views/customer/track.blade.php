@extends('layouts.app')

@section('title', 'Pelacakan Pesanan #' . $order->queue_number . ' - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col py-5 px-4 pb-16">
    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Top Brand Header -->
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-white p-0.5 border border-emerald-600/30 flex items-center justify-center shadow overflow-hidden">
                    <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="font-bold text-sm text-gray-800 font-serif">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h2>
                    <p class="text-[10px] text-gray-500">Live Tracker Pesanan FIFO</p>
                </div>
            </div>

            <div class="flex items-center space-x-1.5 text-[11px] text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-full font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>Live Terhubung</span>
            </div>
        </div>

        <!-- Banner Siap Diambil (Tampil jika status = siap_diambil) -->
        <div id="readyPickupBanner" class="{{ $order->status === 'siap_diambil' ? '' : 'hidden' }} bg-gradient-to-r from-emerald-600 to-emerald-500 text-white rounded-3xl p-5 shadow-xl text-center space-y-3 animate-bounce-subtle border-2 border-amber-300">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto text-amber-300 text-3xl shadow-inner">
                <i class="fa-solid fa-bell animate-wiggle"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-lg text-white font-serif">PESANAN ANDA SIAP DIAMBIL!</h3>
                <p class="text-xs text-emerald-100 mt-1">
                    Silakan menuju ke meja kasir/counter dan tunjukkan nomor antrean Anda.
                </p>
            </div>
            <div class="inline-block bg-white text-emerald-900 font-extrabold text-2xl px-6 py-2 rounded-xl shadow font-mono">
                {{ $order->queue_number }}
            </div>
            <div class="pt-2">
                <button type="button" onclick="acknowledgeReadyOrder()" class="px-5 py-2.5 bg-amber-400 hover:bg-amber-300 text-emerald-950 font-black rounded-xl text-xs uppercase tracking-wider shadow-lg transition transform active:scale-95 inline-flex items-center space-x-1.5">
                    <i class="fa-solid fa-volume-xmark"></i>
                    <span>Matikan Alarm / Saya Ambil</span>
                </button>
            </div>
        </div>

        <!-- Pop-up Layar Penuh Darurat Pesanan Siap (Berdenging Keras Tiada Henti) -->
        <div id="orderReadyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-md transition-all duration-300 hidden">
            <div class="bg-gradient-to-b from-emerald-900 via-emerald-950 to-gray-950 text-white rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl border-4 border-amber-400 animate-bounce-subtle space-y-5">
                <div class="relative inline-block">
                    <div class="w-20 h-20 bg-amber-400 text-emerald-950 rounded-3xl flex items-center justify-center mx-auto text-4xl shadow-xl ring-8 ring-amber-400/30 animate-pulse">
                        <i class="fa-solid fa-bell animate-wiggle"></i>
                    </div>
                    <span class="absolute -top-1 -right-1 flex h-6 w-6">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-6 w-6 bg-red-500 text-white text-[10px] font-black items-center justify-center">!</span>
                    </span>
                </div>

                <div class="space-y-1">
                    <span class="text-xs font-black uppercase tracking-widest text-amber-300">PANGGILAN KASIR</span>
                    <h2 class="text-2xl font-black font-serif text-white">PESANAN ANDA SIAP!</h2>
                    <p class="text-xs text-emerald-200">Pesanan telah selesai diracik oleh barista Sekarsa Coffee.</p>
                </div>

                <div class="bg-white/10 rounded-2xl p-4 border border-white/20 space-y-1">
                    <span class="text-[10px] text-amber-300 font-bold uppercase tracking-wider block">Nomor Antrean Anda</span>
                    <span class="text-4xl font-black font-mono text-white tracking-widest block">{{ $order->queue_number }}</span>
                    <span class="text-xs text-emerald-300 font-medium block">Atas Nama: {{ $order->customer_name }}</span>
                </div>

                <button type="button" onclick="acknowledgeReadyOrder()" class="w-full py-4 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-emerald-950 rounded-2xl font-black text-sm uppercase tracking-wider shadow-2xl transition transform active:scale-95 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-check-double text-lg"></i>
                    <span>SAYA AMBIL SEKARANG (MATIKAN ALARM)</span>
                </button>
            </div>
        </div>

        <!-- Card Kartu Status Utama -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
            <!-- Header Kartu -->
            <div class="p-5 border-b border-gray-100 bg-emerald-950 text-white flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-semibold text-emerald-300 tracking-wider">Nomor Antrean</span>
                    <h3 class="text-3xl font-black text-amber-300 font-mono tracking-wider">{{ $order->queue_number }}</h3>
                    <p class="text-xs text-emerald-200/80 mt-0.5">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <span id="badgeCurrentStatus" class="inline-flex items-center px-3 py-1 rounded-xl text-xs font-bold
                        {{ $order->status === 'menunggu' ? 'bg-amber-500/20 text-amber-300 border border-amber-400/40' : '' }}
                        {{ $order->status === 'diproses' ? 'bg-blue-500/20 text-blue-300 border border-blue-400/40' : '' }}
                        {{ $order->status === 'siap_diambil' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/40' : '' }}
                        {{ $order->status === 'selesai' ? 'bg-gray-500/20 text-gray-300 border border-gray-400/40' : '' }}
                        {{ $order->status === 'dibatalkan' ? 'bg-red-500/20 text-red-300 border border-red-400/40' : '' }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                        <span id="textStatusLabel">{{ $order->status_label }}</span>
                    </span>
                    @if($order->table_number)
                        <p class="text-[11px] text-emerald-300 mt-1 font-semibold">Meja {{ $order->table_number }}</p>
                    @endif
                </div>
            </div>

            <!-- Stepper 4 Langkah Sesuai Skripsi -->
            <div class="p-5 border-b border-gray-100 bg-gray-50/70">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center justify-between">
                    <span>Tahapan Pengerjaan</span>
                    <span class="text-emerald-700 font-mono text-[10px]">FIFO Server Time</span>
                </div>

                <div class="relative flex items-center justify-between">
                    <!-- Garis Progres Latar -->
                    <div class="absolute left-4 right-4 top-4 -translate-y-1/2 h-1 bg-gray-200 -z-0"></div>
                    <!-- Garis Progres Aktif -->
                    <div id="stepperProgressBar" class="absolute left-4 top-4 -translate-y-1/2 h-1 bg-emerald-600 transition-all duration-500 -z-0"
                         style="width: {{ $order->status === 'menunggu' ? '15%' : ($order->status === 'diproses' ? '50%' : ($order->status === 'siap_diambil' ? '80%' : '100%')) }};"></div>

                    <!-- Step 1: Menunggu -->
                    <div class="flex flex-col items-center relative z-10">
                        <div id="stepDot1" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow {{ in_array($order->status, ['menunggu', 'diproses', 'siap_diambil', 'selesai']) ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-clock text-[11px]"></i>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 mt-1.5 text-center leading-tight">Menunggu</span>
                    </div>

                    <!-- Step 2: Diproses -->
                    <div class="flex flex-col items-center relative z-10">
                        <div id="stepDot2" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow {{ in_array($order->status, ['diproses', 'siap_diambil', 'selesai']) ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-fire-burner text-[11px]"></i>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 mt-1.5 text-center leading-tight">Diproses</span>
                    </div>

                    <!-- Step 3: Siap Diambil -->
                    <div class="flex flex-col items-center relative z-10">
                        <div id="stepDot3" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow {{ in_array($order->status, ['siap_diambil', 'selesai']) ? 'bg-emerald-600 text-white animate-pulse' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-bell text-[11px]"></i>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 mt-1.5 text-center leading-tight">Siap Diambil</span>
                    </div>

                    <!-- Step 4: Selesai -->
                    <div class="flex flex-col items-center relative z-10">
                        <div id="stepDot4" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow {{ $order->status === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-check text-[11px]"></i>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 mt-1.5 text-center leading-tight">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Posisi Antrean FIFO Live Panel -->
            <div id="fifoQueueInfoPanel" class="p-5 border-b border-gray-100 {{ in_array($order->status, ['siap_diambil', 'selesai', 'dibatalkan']) ? 'hidden' : '' }}">
                <div class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 text-emerald-950">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-emerald-900 flex items-center">
                            <i class="fa-solid fa-chart-simple mr-1.5 text-emerald-600"></i> Metrik Antrean FIFO
                        </span>
                        <span class="text-[10px] bg-emerald-200/80 text-emerald-900 px-2 py-0.5 rounded-full font-semibold">
                            Posisi ke-<span id="posisiAntreanVal">{{ $queueInfo['posisi_antrean'] }}</span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="bg-white rounded-xl p-2.5 border border-emerald-100/80 shadow-sm">
                            <p class="text-[10px] text-gray-500 font-medium">Pesanan di Depan</p>
                            <p id="pesananDiDepanVal" class="text-xl font-black text-emerald-900 font-mono">{{ $queueInfo['pesanan_di_depan'] }}</p>
                            <p class="text-[9px] text-gray-400">antrean aktif</p>
                        </div>
                        <div class="bg-white rounded-xl p-2.5 border border-emerald-100/80 shadow-sm">
                            <p class="text-[10px] text-gray-500 font-medium">Estimasi Waktu</p>
                            <p id="estimasiWaktuVal" class="text-xl font-black text-emerald-900 font-mono">&plusmn; {{ $queueInfo['estimasi_waktu_menit'] }} mnt</p>
                            <p class="text-[9px] text-gray-400">waktu tunggu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Pemesan & Item Pesanan -->
            <div class="p-5 space-y-4">
                <div class="text-xs space-y-1 text-gray-600">
                    <div class="flex justify-between">
                        <span>Nama Pelanggan:</span>
                        <strong class="text-gray-900">{{ $order->customer_name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span>Waktu Pesanan Masuk:</span>
                        <span class="text-gray-800">{{ $order->received_at->format('H:i') }} WIB</span>
                    </div>
                </div>

                <!-- Menu List -->
                <div class="border-t border-gray-100 pt-3">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Item Dipesan</h4>
                    <div class="space-y-2">
                        @foreach($order->details as $item)
                            <div class="flex justify-between items-start text-xs">
                                <div>
                                    <p class="font-semibold text-gray-800">
                                        <span class="text-emerald-700 font-bold mr-1">{{ $item->quantity }}x</span>
                                        {{ $item->menu_name }}
                                    </p>
                                    @if($item->notes)
                                        <p class="text-[11px] text-gray-400 italic">"{{ $item->notes }}"</p>
                                    @endif
                                </div>
                                <span class="font-semibold text-gray-700">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-3 pt-2 border-t border-gray-100 flex justify-between items-center text-xs">
                        <span class="font-bold text-gray-900">Total Pembayaran:</span>
                        <span class="font-extrabold text-sm text-emerald-800">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Timeline Perubahan Status -->
                <div class="border-t border-gray-100 pt-3">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2.5">Riwayat Status</h4>
                    <div id="timelineContainer" class="space-y-2">
                        @foreach($order->statusHistory as $hist)
                            <div class="flex items-start space-x-2 text-[11px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mt-1.5 shrink-0"></span>
                                <div class="flex-1">
                                    <p class="text-gray-800 font-semibold leading-tight">{{ $hist->notes }}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $hist->created_at->format('H:i:s') }} &bull; {{ $hist->changed_by }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <!-- Tombol Pesan Lagi -->
        <div class="text-center pt-2">
            <a href="{{ route('customer.order', $order->table_number ? ['meja' => $order->table_number] : []) }}"
               class="inline-flex items-center space-x-2 text-xs font-semibold text-emerald-700 hover:text-emerald-900 py-2 px-4 rounded-xl hover:bg-emerald-50 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Menu Pemesanan</span>
            </a>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes wiggle {
        0%, 100% { transform: rotate(-10deg); }
        50% { transform: rotate(10deg); }
    }
    .animate-wiggle {
        animation: wiggle 0.6s ease-in-out infinite;
    }
    @keyframes bounceSubtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-4px); }
    }
    .animate-bounce-subtle {
        animation: bounceSubtle 2s ease-in-out infinite;
    }
</style>
@endpush

@push('scripts')
<script>
    const orderToken = "{{ $order->tracking_token }}";
    let currentStatus = "{{ $order->status }}";
    let alarmAcknowledged = false;

    // Minta izin notifikasi browser agar saat tab di-minimize tetap ada notif
    document.addEventListener('DOMContentLoaded', () => {
        requestNotificationPermission();

        // Jika saat halaman pertama kali dibuka statusnya sudah siap_diambil
        if (currentStatus === 'siap_diambil') {
            triggerOrderReadyAlarm();
        }
    });

    function triggerOrderReadyAlarm() {
        if (alarmAcknowledged) return;

        const modal = document.getElementById('orderReadyModal');
        if (modal) modal.classList.remove('hidden');

        const banner = document.getElementById('readyPickupBanner');
        if (banner) banner.classList.remove('hidden');

        startSekarsaContinuousAlarm();
    }

    function acknowledgeReadyOrder() {
        alarmAcknowledged = true;
        stopSekarsaContinuousAlarm();

        const modal = document.getElementById('orderReadyModal');
        if (modal) modal.classList.add('hidden');

        Swal.fire({
            title: 'Alarm Dimatikan',
            text: 'Silakan menuju ke counter barista atau kasir untuk mengambil pesanan Anda. Terima kasih!',
            icon: 'success',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Baik, Mengerti'
        });
    }

    // Polling API endpoint setiap 3 detik
    async function checkOrderStatus() {
        try {
            const res = await fetch(`/api/pesanan/${orderToken}/status`);
            if (!res.ok) return;

            const data = await res.json();
            if (!data.success) return;

            // Update status UI
            const newStatus = data.status;

            if (newStatus !== currentStatus) {
                currentStatus = newStatus;

                // Update text label & badges
                const labelEl = document.getElementById('textStatusLabel');
                if (labelEl) labelEl.innerText = data.status_label;

                // Update Stepper
                updateStepper(newStatus);

                // Cek jika status berubah ke 'siap_diambil'
                if (newStatus === 'siap_diambil') {
                    const queuePanel = document.getElementById('fifoQueueInfoPanel');
                    if (queuePanel) queuePanel.classList.add('hidden');

                    triggerOrderReadyAlarm();
                } else if (newStatus === 'selesai') {
                    acknowledgeReadyOrder();
                    const banner = document.getElementById('readyPickupBanner');
                    if (banner) banner.classList.add('hidden');
                    const queuePanel = document.getElementById('fifoQueueInfoPanel');
                    if (queuePanel) queuePanel.classList.add('hidden');
                }
            }

            // Update FIFO metrics if still waiting or processing
            if (newStatus === 'menunggu' || newStatus === 'diproses') {
                const posEl = document.getElementById('posisiAntreanVal');
                const depanEl = document.getElementById('pesananDiDepanVal');
                const estEl = document.getElementById('estimasiWaktuVal');

                if (posEl) posEl.innerText = data.posisi_antrean;
                if (depanEl) depanEl.innerText = data.pesanan_di_depan;
                if (estEl) estEl.innerText = `± ${data.estimasi_waktu_menit} mnt`;
            }

        } catch (e) {
            console.warn('Gagal memuat status live:', e);
        }
    }

    function updateStepper(status) {
        const bar = document.getElementById('stepperProgressBar');
        const dot1 = document.getElementById('stepDot1');
        const dot2 = document.getElementById('stepDot2');
        const dot3 = document.getElementById('stepDot3');
        const dot4 = document.getElementById('stepDot4');

        if (status === 'menunggu') {
            if (bar) bar.style.width = '15%';
            setDotActive(dot1, true);
            setDotActive(dot2, false);
            setDotActive(dot3, false);
            setDotActive(dot4, false);
        } else if (status === 'diproses') {
            if (bar) bar.style.width = '50%';
            setDotActive(dot1, true);
            setDotActive(dot2, true);
            setDotActive(dot3, false);
            setDotActive(dot4, false);
        } else if (status === 'siap_diambil') {
            if (bar) bar.style.width = '80%';
            setDotActive(dot1, true);
            setDotActive(dot2, true);
            setDotActive(dot3, true);
            setDotActive(dot4, false);
        } else if (status === 'selesai') {
            if (bar) bar.style.width = '100%';
            setDotActive(dot1, true);
            setDotActive(dot2, true);
            setDotActive(dot3, true);
            setDotActive(dot4, true);
        }
    }

    function setDotActive(el, active) {
        if (!el) return;
        if (active) {
            el.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow bg-emerald-600 text-white';
        } else {
            el.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow bg-gray-200 text-gray-500';
        }
    }

    // Start Polling Interval 3 detik
    setInterval(checkOrderStatus, 3000);
</script>
@endpush
