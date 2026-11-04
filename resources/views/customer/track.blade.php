@extends('layouts.app')

@section('title', 'Status Pesanan ' . $pesanan->nomor_pesanan . ' - ' . ($pengaturan->nama_cafe ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-gray-50 pb-20">

    <!-- Top Header -->
    <header class="glass-header text-white px-4 py-3.5 shadow sticky top-0 z-30">
        <div class="max-w-2xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <a href="{{ route('customer.order', ['meja' => $pesanan->meja->nomor_meja]) }}" class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center text-white hover:bg-white/25 transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h1 class="font-serif font-bold text-sm tracking-wide">SEKARSA COFFEE</h1>
                    <p class="text-[10px] text-emerald-200">Pelacakan Antrean FIFO Real-time</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center space-x-1 bg-emerald-800/80 border border-emerald-500/50 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                    <i class="fa-solid fa-chair text-emerald-300 text-[10px]"></i>
                    <span>Meja {{ $pesanan->meja->nomor_meja }}</span>
                </span>
            </div>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 pt-5 space-y-4">

        <!-- Banner Status Pesanan Siap (Celebration Card) -->
        <div id="readyCelebrationBanner" class="{{ $pesanan->status_pesanan === 'selesai' ? '' : 'hidden' }} bg-gradient-to-r from-emerald-600 to-emerald-800 text-white p-5 rounded-3xl shadow-xl border border-emerald-400/50 animate-pulse">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 rounded-2xl bg-white text-emerald-800 flex items-center justify-center text-2xl shrink-0 shadow-lg">
                    <i class="fa-solid fa-bell text-amber-500"></i>
                </div>
                <div>
                    <span class="bg-amber-400 text-gray-950 text-[10px] font-black uppercase px-2 py-0.5 rounded-md">Pesanan Siap Diambil!</span>
                    <h2 class="text-lg font-black mt-1 leading-tight">Pesanan Anda Sudah Jadi!</h2>
                    <p class="text-xs text-emerald-100 mt-0.5">Silakan menuju ke <strong>Meja Kasir</strong> untuk mengambil pesanan dan melakukan pembayaran.</p>
                </div>
            </div>
        </div>

        <!-- Main Order Ticket Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Header Ticket -->
            <div class="bg-gradient-to-r from-sekarsa-900 to-emerald-800 text-white p-5 flex items-center justify-between">
                <div>
                    <p class="text-[11px] text-emerald-200 uppercase font-semibold">Nomor Pesanan</p>
                    <h2 class="text-3xl font-black tracking-wider font-mono text-amber-300" id="ticketOrderNumber">{{ $pesanan->nomor_pesanan }}</h2>
                    <p class="text-xs text-emerald-100 mt-0.5">Atas Nama: <strong>{{ $pesanan->nama_pelanggan }}</strong></p>
                </div>
                <div class="text-right">
                    <span id="ticketStatusBadge" class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                        @if($pesanan->status_pesanan === 'menunggu') bg-amber-500 text-white
                        @elseif($pesanan->status_pesanan === 'diproses') bg-blue-500 text-white
                        @elseif($pesanan->status_pesanan === 'selesai') bg-emerald-400 text-gray-950 font-black
                        @elseif($pesanan->status_pesanan === 'diambil') bg-gray-500 text-white
                        @else bg-red-500 text-white @endif">
                        {{ $pesanan->status_pesanan_label }}
                    </span>
                    <p class="text-[11px] text-emerald-200 mt-1">Masuk: {{ $pesanan->waktu_masuk->format('H:i') }} WIB</p>
                </div>
            </div>

            <!-- Stepper Progress Tracker -->
            <div class="p-5 border-b border-gray-100 bg-gray-50/50">
                <div class="relative flex items-center justify-between">
                    <!-- Progress Line -->
                    <div class="absolute left-4 right-4 top-1/2 -translate-y-1/2 h-1 bg-gray-200 z-0"></div>
                    <div id="stepperProgressBar" class="absolute left-4 top-1/2 -translate-y-1/2 h-1 bg-emerald-600 transition-all duration-500 z-0"
                        style="width: 
                        @if($pesanan->status_pesanan === 'menunggu') 15%
                        @elseif($pesanan->status_pesanan === 'diproses') 50%
                        @elseif($pesanan->status_pesanan === 'selesai') 85%
                        @elseif($pesanan->status_pesanan === 'diambil') 100%
                        @else 0% @endif;"></div>

                    <!-- Step 1: Menunggu -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div id="stepCircle1" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm
                            {{ in_array($pesanan->status_pesanan, ['menunggu', 'diproses', 'selesai', 'diambil']) ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <span class="text-[10px] font-bold text-gray-700 mt-1">Antrean</span>
                    </div>

                    <!-- Step 2: Diproses -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div id="stepCircle2" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm
                            {{ in_array($pesanan->status_pesanan, ['diproses', 'selesai', 'diambil']) ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-mug-hot"></i>
                        </div>
                        <span class="text-[10px] font-bold text-gray-700 mt-1">Diracik</span>
                    </div>

                    <!-- Step 3: Selesai -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div id="stepCircle3" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm
                            {{ in_array($pesanan->status_pesanan, ['selesai', 'diambil']) ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <span class="text-[10px] font-bold text-gray-700 mt-1">Siap Ambil</span>
                    </div>

                    <!-- Step 4: Diambil -->
                    <div class="relative z-10 flex flex-col items-center">
                        <div id="stepCircle4" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm
                            {{ $pesanan->status_pesanan === 'diambil' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <span class="text-[10px] font-bold text-gray-700 mt-1">Selesai</span>
                    </div>
                </div>
            </div>

            <!-- Dynamic FIFO Queue Status Details -->
            <div class="p-5 space-y-4">
                <div id="queueDetailBox" class="p-4 rounded-2xl border
                    @if($pesanan->status_pesanan === 'menunggu') bg-amber-50/70 border-amber-200 text-amber-950
                    @elseif($pesanan->status_pesanan === 'diproses') bg-blue-50/70 border-blue-200 text-blue-950
                    @elseif($pesanan->status_pesanan === 'selesai') bg-emerald-50/70 border-emerald-200 text-emerald-950
                    @else bg-gray-50 border-gray-200 text-gray-800 @endif">
                    
                    <div class="flex items-start space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0 text-lg">
                            <span id="queueBoxIcon">
                                @if($pesanan->status_pesanan === 'menunggu') <i class="fa-solid fa-list-ol text-amber-600"></i>
                                @elseif($pesanan->status_pesanan === 'diproses') <i class="fa-solid fa-blender text-blue-600 animate-spin"></i>
                                @elseif($pesanan->status_pesanan === 'selesai') <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                @else <i class="fa-solid fa-champagne-glasses text-gray-600"></i> @endif
                            </span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm" id="queueStatusTitle">
                                @if($pesanan->status_pesanan === 'menunggu')
                                    Pesanan Sedang dalam Antrean FIFO
                                @elseif($pesanan->status_pesanan === 'diproses')
                                    Sedang Diracik oleh Barista Sekarsa
                                @elseif($pesanan->status_pesanan === 'selesai')
                                    Minuman & Makanan Anda Sudah Siap!
                                @elseif($pesanan->status_pesanan === 'diambil')
                                    Pesanan Telah Diambil
                                @else
                                    Pesanan Dibatalkan
                                @endif
                            </h4>
                            <p class="text-xs mt-1 text-gray-600" id="queueStatusDesc">
                                @if($pesanan->status_pesanan === 'menunggu')
                                    Pesanan Anda berada di <strong>urutan ke-{{ $queueInfo['posisi_antrean'] ?? 1 }}</strong> dari total {{ $queueInfo['total_antrean'] ?? 1 }} pesanan aktif. Estimasi waktu tunggu: &plusmn;{{ $queueInfo['estimasi_waktu_menit'] ?? 5 }} menit.
                                @elseif($pesanan->status_pesanan === 'diproses')
                                    Barista kami sedang menyiapkan pesanan Anda dengan cermat. Mohon menunggu sebentar lagi, notifikasi dan suara bel akan berbunyi saat pesanan siap!
                                @elseif($pesanan->status_pesanan === 'selesai')
                                    Silakan sebutkan nomor pesanan <strong>{{ $pesanan->nomor_pesanan }}</strong> di meja kasir untuk mengambil pesanan dan menyelesaikan pembayaran.
                                @elseif($pesanan->status_pesanan === 'diambil')
                                    Terima kasih telah memesan di Sekarsa Coffee. Selamat menikmati sajian kami di Meja {{ $pesanan->meja->nomor_meja }}!
                                @else
                                    Pesanan ini telah dibatalkan.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Testing / Manual Trigger Sound Button (Untuk dosen penguji memastikan suara notifikasi bekerja) -->
                <div class="flex items-center justify-between text-xs bg-gray-50 p-3 rounded-xl border border-gray-200/80">
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="fa-solid fa-volume-high text-emerald-700"></i>
                        <span>Notifikasi Suara Bel Pesanan</span>
                    </div>
                    <button type="button" onclick="playSekarsaChime()" class="px-2.5 py-1 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold rounded-lg text-[11px] shadow-sm transition">
                        <i class="fa-solid fa-play text-[9px] mr-1"></i> Tes Suara Bel
                    </button>
                </div>
            </div>
        </div>

        <!-- Order Items Detail & Receipt Breakdown -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 space-y-3">
            <h3 class="font-bold text-gray-800 text-sm flex items-center justify-between border-b border-gray-100 pb-2">
                <span>Rincian Pesanan Menu</span>
                <span class="text-xs font-semibold text-gray-500">{{ $pesanan->items->sum('jumlah') }} Item</span>
            </h3>

            <div class="divide-y divide-gray-100">
                @foreach($pesanan->items as $item)
                <div class="py-2.5 flex items-start justify-between text-xs">
                    <div>
                        <p class="font-bold text-gray-800">{{ $item->nama_menu }}</p>
                        <p class="text-gray-500 text-[11px]">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }} &times; {{ $item->jumlah }}</p>
                        @if($item->catatan)
                            <p class="text-[10px] text-amber-700 italic mt-0.5"><i class="fa-solid fa-pen-nib"></i> {{ $item->catatan }}</p>
                        @endif
                    </div>
                    <span class="font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-gray-100 space-y-1.5 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($pesanan->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($pesanan->diskon_promo > 0)
                <div class="flex justify-between text-emerald-700 font-bold">
                    <span>Potongan Promo ({{ $pesanan->promo ? $pesanan->promo->kode_promo : 'Diskon' }})</span>
                    <span>- Rp {{ number_format($pesanan->diskon_promo, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm font-black text-gray-900 pt-1.5 border-t border-gray-200">
                    <span>Total Pembayaran</span>
                    <span class="text-emerald-800 text-base">Rp {{ number_format($pesanan->total_akhir, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Status Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-wallet text-emerald-700 text-base"></i>
                    <h3 class="font-bold text-gray-800 text-sm">Status Pembayaran</h3>
                </div>
                <div>
                    <span id="paymentStatusBadge" class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                        @if($pesanan->status_pembayaran === 'lunas') bg-emerald-100 text-emerald-800 border border-emerald-300
                        @elseif($pesanan->status_pembayaran === 'menunggu_konfirmasi') bg-blue-100 text-blue-800 border border-blue-300
                        @else bg-amber-100 text-amber-800 border border-amber-300 @endif">
                        {{ $pesanan->status_pembayaran_label }}
                    </span>
                </div>
            </div>

            @if($pesanan->status_pembayaran === 'lunas')
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-200 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h4 class="font-bold text-sm text-emerald-900">Pembayaran Lunas</h4>
                    <p class="text-xs text-emerald-700 mt-0.5">
                        Metode: <strong>{{ strtoupper($pesanan->metode_pembayaran ?? 'Kasir') }}</strong> &bull;
                        Waktu: {{ $pesanan->waktu_bayar ? $pesanan->waktu_bayar->format('H:i') . ' WIB' : 'Hari ini' }}
                    </p>
                </div>
            </div>
            @else
            <div class="space-y-3">
                <p class="text-xs text-gray-600">Pilih salah satu metode pembayaran di bawah ini:</p>
                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Option 1: QRIS -->
                    <button type="button" onclick="openQrisModal()"
                        class="p-3 bg-red-50 hover:bg-red-100/80 border border-red-200 rounded-2xl text-left transition flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black text-red-700">QRIS</span>
                            <i class="fa-solid fa-qrcode text-red-600 text-base"></i>
                        </div>
                        <p class="text-[11px] text-gray-600">Scan QRIS dari aplikasi m-banking atau e-wallet Anda.</p>
                        <span class="mt-2 text-[10px] font-bold text-red-700 inline-flex items-center">
                            Buka QRIS &rarr;
                        </span>
                    </button>

                    <!-- Option 2: Cash / Tunai -->
                    <div class="p-3 bg-gray-50 border border-gray-200 rounded-2xl text-left flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-black text-gray-800">Uang Tunai</span>
                            <i class="fa-solid fa-money-bill-wave text-emerald-600 text-base"></i>
                        </div>
                        <p class="text-[11px] text-gray-600">Bayar langsung di kasir saat mengambil pesanan.</p>
                        <span class="mt-2 text-[10px] font-bold text-gray-500">
                            Kasir Sekarsa
                        </span>
                    </div>
                </div>

                <!-- Form Upload Bukti QRIS (Opsional untuk pelanggan) -->
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs font-bold text-gray-700 mb-1.5">Unggah Bukti Bayar QRIS (Opsional)</p>
                    <form action="{{ route('customer.upload_qris', $pesanan->nomor_pesanan) }}" method="POST" enctype="multipart/form-data" class="flex space-x-2">
                        @csrf
                        <input type="file" name="bukti_qris" accept="image/*" required class="flex-1 text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        <button type="submit" class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-xl transition shrink-0">
                            Unggah
                        </button>
                    </form>
                    @if($pesanan->bukti_qris)
                        <p class="text-[11px] text-emerald-700 mt-1 font-semibold flex items-center">
                            <i class="fa-solid fa-circle-check mr-1"></i> Bukti telah diunggah. Menunggu verifikasi kasir.
                        </p>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Action Back to Menu -->
        <div class="text-center pt-2">
            <a href="{{ route('customer.order', ['meja' => $pesanan->meja->nomor_meja]) }}" class="inline-flex items-center space-x-2 text-xs font-bold text-emerald-800 hover:underline">
                <i class="fa-solid fa-plus"></i>
                <span>Ingin Pesan Menu Tambahan?</span>
            </a>
        </div>

    </main>

</div>

<!-- Modal QRIS Sekarsa Coffee -->
<div id="qrisModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center shadow-2xl border border-gray-100 relative">
        <button onclick="closeQrisModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <h3 class="font-bold text-gray-900 text-base">QRIS Pembayaran Digital</h3>
        <p class="text-xs text-gray-500 mt-0.5">Total Tagihan: <strong class="text-emerald-800 text-sm">Rp {{ number_format($pesanan->total_akhir, 0, ',', '.') }}</strong></p>

        <div class="my-4 p-2 bg-gray-50 rounded-2xl border border-gray-200 inline-block shadow-inner">
            @if($pengaturan->qris_image && file_exists(public_path('storage/' . $pengaturan->qris_image)))
                <img src="{{ asset('storage/' . $pengaturan->qris_image) }}" alt="QRIS Sekarsa" class="w-64 h-auto mx-auto rounded-xl">
            @else
                <img src="{{ asset('images/qris-sekarsa.svg') }}" alt="QRIS Sekarsa" class="w-64 h-auto mx-auto rounded-xl">
            @endif
        </div>

        <p class="text-xs text-gray-600 font-medium">Buka GoPay, OVO, Dana, ShopeePay, atau BCA Mobile lalu arahkan kamera ke kode di atas.</p>

        <button type="button" onclick="closeQrisModal()" class="w-full mt-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs transition">
            Saya Mengerti
        </button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentStatusPesanan = "{{ $pesanan->status_pesanan }}";
    let currentStatusPembayaran = "{{ $pesanan->status_pembayaran }}";

    function openQrisModal() {
        document.getElementById('qrisModal').classList.remove('hidden');
    }
    function closeQrisModal() {
        document.getElementById('qrisModal').classList.add('hidden');
    }

    // Realtime Polling Status Pesanan setiap 3 detik
    const pollInterval = setInterval(() => {
        fetch("{{ route('customer.status_api', $pesanan->nomor_pesanan) }}")
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                // Cek jika status berubah menjadi 'selesai' -> BUNYIKAN BEL / CHIME
                if (currentStatusPesanan !== 'selesai' && data.status_pesanan === 'selesai') {
                    playSekarsaChime();
                    document.getElementById('readyCelebrationBanner').classList.remove('hidden');

                    Swal.fire({
                        icon: 'success',
                        title: 'Pesanan Selesai!',
                        html: `Minuman & makanan untuk nomor <strong>${data.nomor_pesanan}</strong> telah selesai diracik!<br><br>Silakan ambil pesanan Anda di meja kasir.`,
                        confirmButtonText: 'Ambil di Kasir',
                        confirmButtonColor: '#16a34a'
                    });
                }

                // Cek jika status pembayaran berubah menjadi lunas
                if (currentStatusPembayaran !== 'lunas' && data.status_pembayaran === 'lunas') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Lunas',
                        text: 'Terima kasih, pembayaran Anda telah dikonfirmasi oleh kasir!',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });
                }

                currentStatusPesanan = data.status_pesanan;
                currentStatusPembayaran = data.status_pembayaran;

                // Update UI Elemen Dinamis
                updateTrackerUI(data);
            })
            .catch(err => console.error('Polling status error:', err));
    }, 3000);

    function updateTrackerUI(data) {
        // Status Badge
        const badge = document.getElementById('ticketStatusBadge');
        badge.innerText = data.status_pesanan_label;

        if (data.status_pesanan === 'menunggu') {
            badge.className = 'inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-500 text-white';
            document.getElementById('stepperProgressBar').style.width = '15%';
        } else if (data.status_pesanan === 'diproses') {
            badge.className = 'inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-500 text-white';
            document.getElementById('stepperProgressBar').style.width = '50%';
        } else if (data.status_pesanan === 'selesai') {
            badge.className = 'inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-400 text-gray-950 font-black';
            document.getElementById('stepperProgressBar').style.width = '85%';
        } else if (data.status_pesanan === 'diambil') {
            badge.className = 'inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-gray-500 text-white';
            document.getElementById('stepperProgressBar').style.width = '100%';
        }

        // Stepper Circles
        const steps = ['menunggu', 'diproses', 'selesai', 'diambil'];
        const currentIdx = steps.indexOf(data.status_pesanan);

        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById('stepCircle' + i);
            if (i - 1 <= currentIdx) {
                circle.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm bg-emerald-600 text-white';
            } else {
                circle.className = 'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition shadow-sm bg-gray-200 text-gray-500';
            }
        }

        // Queue info text
        const titleEl = document.getElementById('queueStatusTitle');
        const descEl = document.getElementById('queueStatusDesc');

        if (data.status_pesanan === 'menunggu') {
            titleEl.innerText = 'Pesanan Sedang dalam Antrean FIFO';
            descEl.innerHTML = `Pesanan Anda berada di <strong>urutan ke-${data.posisi_antrean}</strong> dari total ${data.total_antrean} pesanan aktif. Estimasi waktu tunggu: &plusmn;${data.estimasi_waktu_menit} menit.`;
        } else if (data.status_pesanan === 'diproses') {
            titleEl.innerText = 'Sedang Diracik oleh Barista Sekarsa';
            descEl.innerText = 'Barista kami sedang menyiapkan pesanan Anda dengan cermat. Mohon menunggu sebentar lagi, notifikasi dan suara bel akan berbunyi!';
        } else if (data.status_pesanan === 'selesai') {
            titleEl.innerText = 'Minuman & Makanan Anda Sudah Siap!';
            descEl.innerHTML = `Silakan sebutkan nomor pesanan <strong>${data.nomor_pesanan}</strong> di meja kasir untuk mengambil pesanan dan melakukan pembayaran.`;
        } else if (data.status_pesanan === 'diambil') {
            titleEl.innerText = 'Pesanan Telah Diambil';
            descEl.innerText = 'Terima kasih telah memesan di Sekarsa Coffee. Selamat menikmati sajian kami!';
        }
    }
</script>
@endpush
