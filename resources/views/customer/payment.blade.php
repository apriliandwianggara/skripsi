@extends('layouts.app')

@section('title', 'Pembayaran QRIS Rp ' . number_format($order->total_price, 0, ',', '.') . ' - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col py-6 px-4 pb-20">
    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Top Header & Brand -->
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-white p-0.5 border border-emerald-600/30 flex items-center justify-center shadow overflow-hidden">
                    <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="font-bold text-sm text-gray-800 font-serif">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h2>
                    <p class="text-[10px] text-gray-500">Pembayaran Digital QRIS</p>
                </div>
            </div>

            <div class="flex items-center space-x-1.5 text-[11px] text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-full font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>QRIS Otomatis</span>
            </div>
        </div>

        <!-- Banner Info Pembayaran -->
        <div class="bg-gradient-to-br from-emerald-800 via-emerald-900 to-emerald-950 text-white rounded-3xl p-5 shadow-xl text-center space-y-2 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>

            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-300 block">TOTAL TAGIHAN PEMESANAN</span>
            <div class="flex items-baseline justify-center space-x-1.5">
                <span class="text-3xl sm:text-4xl font-black text-amber-300 font-mono tracking-tight" id="nominalDisplay">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
                <button type="button" onclick="copyNominal()" class="text-xs text-emerald-200 hover:text-white px-2 py-1 bg-white/10 hover:bg-white/20 rounded-lg transition" title="Salin Nominal">
                    <i class="fa-regular fa-copy"></i>
                </button>
            </div>

            <div class="pt-1 flex items-center justify-center space-x-2 text-xs text-emerald-200/90">
                <span class="font-mono font-semibold">{{ $order->order_number }}</span>
                <span>&bull;</span>
                <span class="font-bold text-amber-200">Antrean #{{ $order->queue_number }}</span>
                @if($order->table_number)
                    <span>&bull;</span>
                    <span>Meja {{ $order->table_number }}</span>
                @endif
            </div>

            <p class="text-[11px] text-emerald-200/80 pt-0.5">
                Atas Nama: <strong class="text-white">{{ $order->customer_name }}</strong>
            </p>
        </div>

        <!-- Card Kartu QRIS Nasional -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Header QRIS Resmi -->
            <div class="bg-white border-b border-gray-150 p-4 text-center">
                <div class="flex items-center justify-between px-2">
                    <!-- Logo QRIS Resmi SVG Text -->
                    <div class="flex items-center space-x-1.5">
                        <span class="font-black text-xl tracking-tighter text-red-600 font-sans">QRIS</span>
                        <div class="text-left leading-tight pl-1 border-l border-gray-300">
                            <span class="text-[8px] font-extrabold uppercase text-gray-500 block">Quick Response Code</span>
                            <span class="text-[8px] font-bold text-gray-400 block">Indonesian Standard</span>
                        </div>
                    </div>

                    <!-- Logo GPN -->
                    <div class="flex items-center space-x-1">
                        <span class="text-[9px] font-black px-1.5 py-0.5 bg-red-600 text-white rounded font-sans tracking-wide">GPN</span>
                    </div>
                </div>

                <div class="mt-2.5 pt-2 border-t border-gray-100 text-center">
                    <h3 class="font-bold text-sm text-gray-900 tracking-wide">{{ $danaMerchantName ?? 'M Aprilian Dwi Anggara, S (Sekarsa Coffee)' }}</h3>
                    <div class="flex items-center justify-center space-x-2 text-[11px] text-gray-500 font-mono mt-0.5">
                        <span class="font-bold text-emerald-800">NMID: {{ $nmid ?? 'ID1026635802105' }}</span>
                        <span>&bull;</span>
                        <span>A01</span>
                    </div>
                </div>

                <!-- Tab Pemilih Mode QRIS -->
                <div class="mt-3 flex p-1 bg-gray-100 rounded-2xl text-xs font-semibold">
                    <button type="button" id="tabDynamicQris" onclick="switchQrisView('dynamic')" class="flex-1 py-1.5 px-2 rounded-xl bg-white text-emerald-800 font-bold shadow-sm transition flex items-center justify-center space-x-1">
                        <i class="fa-solid fa-bolt text-emerald-600"></i>
                        <span>Nominal Otomatis</span>
                    </button>
                    <button type="button" id="tabStaticQris" onclick="switchQrisView('static')" class="flex-1 py-1.5 px-2 rounded-xl text-gray-600 hover:text-gray-900 transition flex items-center justify-center space-x-1">
                        <i class="fa-solid fa-qrcode text-gray-500"></i>
                        <span>QRIS Standar Toko</span>
                    </button>
                </div>
            </div>

            <!-- Area Tampilan Kode QR -->
            <div class="p-6 flex flex-col items-center justify-center bg-white space-y-4">
                <!-- Tampilan QR Dynamic (Default) -->
                <div id="qrisDynamicBox" class="flex flex-col items-center space-y-3 w-full">
                    <div class="relative p-3 bg-white rounded-2xl border-2 border-dashed border-emerald-500/40 shadow-inner flex items-center justify-center">
                        <img src="{{ $qrisDataUri }}" alt="Kode QRIS Dinamis Rp {{ number_format($order->total_price, 0, ',', '.') }}" class="w-56 h-56 sm:w-64 sm:h-64 object-contain">
                        
                        <!-- Watermark Logo Sekarsa Kecil di Tengah QR -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-md p-1 border border-emerald-600/30 flex items-center justify-center">
                                <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Logo" class="w-full h-full object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk Nominal Terkunci -->
                    <div class="w-full bg-emerald-50 border border-emerald-200/80 rounded-2xl p-3 text-center space-y-1">
                        <span class="text-xs font-bold text-emerald-900 flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-lock text-emerald-600"></i>
                            <span>Nominal Otomatis: Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </span>
                        <p class="text-[11px] text-emerald-700/80">
                            Scan QRIS ini dengan BCA, Mandiri, BRI, BNI, DANA, GoPay, OVO, ShopeePay. Nominal Rp {{ number_format($order->total_price, 0, ',', '.') }} otomatis terkunci.
                        </p>
                    </div>
                </div>

                <!-- Tampilan QR Static (Alternatif) -->
                <div id="qrisStaticBox" class="hidden flex flex-col items-center space-y-3 w-full">
                    <div class="relative p-3 bg-white rounded-2xl border-2 border-dashed border-blue-500/40 shadow-inner flex items-center justify-center">
                        <img src="{{ $staticQrisDataUri }}" alt="Kode QRIS Standar NMID {{ $nmid ?? 'ID1026635802105' }}" class="w-56 h-56 sm:w-64 sm:h-64 object-contain">
                        
                        <!-- Watermark Logo Sekarsa Kecil di Tengah QR -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-12 h-12 bg-white rounded-xl shadow-md p-1 border border-emerald-600/30 flex items-center justify-center">
                                <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Logo" class="w-full h-full object-contain">
                            </div>
                        </div>
                    </div>

                    <!-- Petunjuk QRIS Statis -->
                    <div class="w-full bg-blue-50 border border-blue-200/80 rounded-2xl p-3 text-center space-y-1">
                        <span class="text-xs font-bold text-blue-900 flex items-center justify-center space-x-1.5">
                            <i class="fa-solid fa-keyboard text-blue-600"></i>
                            <span>Input Manual: Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </span>
                        <p class="text-[11px] text-blue-700/80">
                            Gunakan mode ini jika m-Banking Anda meminta mengetikkan nominal pembayaran secara manual.
                        </p>
                    </div>
                </div>

                <!-- Logo E-Wallet & Bank yang Didukung -->
                <div class="text-center w-full pt-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-2">Terverifikasi Bank Indonesia (Semua Bank & E-Wallet)</span>
                    <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] text-gray-600 font-semibold">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md border border-blue-200 font-bold">DANA Bisnis</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 rounded-md border border-blue-200 font-bold">BCA Mobile</span>
                        <span class="px-2 py-0.5 bg-yellow-50 text-yellow-800 rounded-md border border-yellow-200 font-bold">Livin' Mandiri</span>
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-900 rounded-md border border-blue-200 font-bold">BRImo</span>
                        <span class="px-2 py-0.5 bg-orange-50 text-orange-800 rounded-md border border-orange-200 font-bold">BNI Mobile</span>
                        <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded-md border border-green-200">GoPay</span>
                        <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md border border-purple-200">OVO</span>
                        <span class="px-2 py-0.5 bg-orange-50 text-orange-700 rounded-md border border-orange-200">ShopeePay</span>
                    </div>
                </div>
            </div>

            <!-- Transfer Alternatif DANA -->
            <div class="bg-gray-50 border-t border-gray-150 p-5 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-semibold text-gray-700">Tujuan Akun DANA:</span>
                    <span class="font-bold text-gray-900 font-serif">Sekarsa Coffee</span>
                </div>

                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-2.5">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs font-black">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 block leading-tight">Nomor DANA</span>
                            <span class="font-mono font-bold text-sm text-gray-900 leading-tight" id="danaNumberText">{{ $danaNumber }}</span>
                        </div>
                    </div>
                    <button type="button" onclick="copyDanaNumber()" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition active:scale-95 flex items-center space-x-1">
                        <i class="fa-regular fa-copy"></i>
                        <span>Salin</span>
                    </button>
                </div>

                <!-- Tombol Konfirmasi Pembayaran & Multi-Device -->
                <div class="pt-2 space-y-2">
                    @if($order->isWaitingVerification())
                        <div class="w-full py-3.5 bg-purple-600 text-white font-bold rounded-2xl text-sm text-center shadow-lg shadow-purple-600/30 flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-clock-rotate-left fa-spin"></i>
                            <span>Sedang Diverifikasi Kasir</span>
                        </div>
                        <p class="text-[11px] text-purple-800 bg-purple-50 p-2.5 rounded-xl text-center leading-relaxed border border-purple-200">
                            Konfirmasi pembayaran Anda telah masuk ke kasir. Kasir sedang memverifikasi mutasi DANA Bisnis. Layar ini otomatis terupdate menjadi <strong>LUNAS</strong> begitu kasir memverifikasi.
                        </p>
                    @else
                        <button type="button" onclick="confirmPaymentAction()" id="btnConfirmPay" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-bold rounded-2xl text-sm transition shadow-lg shadow-emerald-600/30 flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Saya Sudah Bayar (Konfirmasi ke Kasir)</span>
                        </button>
                    @endif

                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="openShareModal()" class="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 font-bold rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-sm">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                            <span>Bayar di HP Lain / Kirim</span>
                        </button>

                        <a href="{{ route('customer.track', $order->tracking_token) }}" class="py-2.5 px-3 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition text-center flex items-center justify-center space-x-1.5" title="Pantau Antrean">
                            <i class="fa-solid fa-satellite-dish text-emerald-600"></i>
                            <span class="hidden sm:inline">Antrean</span>
                        </a>

                        <button type="button" onclick="openUploadModal()" class="py-2.5 px-3 bg-white hover:bg-gray-100 border border-gray-200 text-gray-600 font-semibold rounded-xl text-xs transition" title="Unggah Bukti Struk (Opsional)">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rincian Pesanan Singkat -->
        <div class="bg-white rounded-2xl border border-gray-150 p-4 space-y-2 text-xs">
            <h4 class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">Rincian Sajian:</h4>
            <div class="divide-y divide-gray-100">
                @foreach($order->details as $item)
                    <div class="py-1.5 flex justify-between items-center">
                        <span class="text-gray-800"><b class="text-emerald-700 mr-1">{{ $item->quantity }}x</b> {{ $item->menu_name }}</span>
                        <span class="font-mono font-semibold text-gray-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Live Sync Status -->
        <p class="text-center text-[11px] text-gray-400">
            <i class="fa-solid fa-rotate fa-spin text-emerald-600 mr-1"></i> Mendeteksi pembayaran secara real-time...
        </p>

    </div>
</div>

<!-- Modal Bayar di HP Lain / Multi-Device -->
<div id="sharePaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <div class="flex items-center space-x-2">
                <i class="fa-solid fa-mobile-screen text-blue-600"></i>
                <h3 class="font-bold text-sm text-gray-900">Bayar Melalui HP Lain</h3>
            </div>
            <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <p class="text-xs text-gray-500 leading-relaxed">
            Arahkan kamera pada <strong>HP 2</strong> ke QR Code di bawah untuk langsung membuka halaman tagihan pesanan ini:
        </p>

        <div class="p-3 bg-slate-50 rounded-2xl border border-gray-200 inline-block shadow-inner mx-auto">
            <img src="{{ $paymentShareQrUri }}" alt="QR Tautan Pembayaran" class="w-48 h-48 mx-auto object-contain">
        </div>

        <div class="bg-blue-50 border border-blue-200/80 rounded-2xl p-2.5 text-[11px] text-blue-900 text-left flex items-start space-x-2">
            <i class="fa-solid fa-circle-nodes text-blue-600 text-sm mt-0.5 shrink-0"></i>
            <div>
                <strong>Sinkronisasi Otomatis:</strong> Saat HP 2 menyelesaikan pembayaran, layar HP ini akan langsung otomatis berganti menjadi <b>Lunas</b> secara real-time.
            </div>
        </div>

        <div class="pt-1 flex flex-col space-y-2">
            <a href="https://api.whatsapp.com/send?text={{ urlencode('Halo! Ini tautan tagihan Sekarsa Coffee sebesar Rp ' . number_format($order->total_price, 0, ',', '.') . ' untuk antrean #' . $order->queue_number . ' (' . $order->order_number . '). Silakan bayar lewat link ini: ' . $paymentShareUrl) }}" target="_blank" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Kirim Link via WhatsApp</span>
            </a>

            <button type="button" onclick="copyPaymentLink()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition flex items-center justify-center space-x-1.5">
                <i class="fa-regular fa-copy"></i>
                <span>Salin Tautan Tagihan</span>
            </button>
        </div>
    </div>
</div>

<!-- Modal Unggah Bukti Bayar (Opsional) -->
<div id="uploadProofModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-900">Unggah Bukti Transfer (Opsional)</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 p-1">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <form id="uploadProofForm" onsubmit="submitProofForm(event)" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs font-semibold text-gray-700 block mb-1">Foto Bukti Transfer QRIS / DANA:</label>
                <input type="file" id="proofFileInput" name="payment_proof" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-gray-200 rounded-xl p-1.5">
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-700 block mb-1">Catatan Tambahan (opsional):</label>
                <input type="text" id="proofNoteInput" name="payment_note" placeholder="Contoh: Transfer via DANA a.n. Budi" class="w-full text-xs px-3 py-2 rounded-xl border border-gray-200 focus:outline-none focus:ring-1 focus:ring-emerald-500">
            </div>

            <div class="pt-2 flex items-center space-x-2">
                <button type="button" onclick="closeUploadModal()" class="w-1/3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" id="btnSubmitProof" class="w-2/3 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition flex items-center justify-center space-x-1.5 shadow-md">
                    <i class="fa-solid fa-upload"></i>
                    <span>Kirim & Konfirmasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderToken = "{{ $order->tracking_token }}";
    const totalAmount = {{ (int)$order->total_price }};
    const danaNumber = "{{ $danaNumber }}";
    let isAlreadyPaid = {{ $order->isPaid() ? 'true' : 'false' }};

    function switchQrisView(type) {
        const dynBox = document.getElementById('qrisDynamicBox');
        const staBox = document.getElementById('qrisStaticBox');
        const dynTab = document.getElementById('tabDynamicQris');
        const staTab = document.getElementById('tabStaticQris');

        if (type === 'static') {
            dynBox.classList.add('hidden');
            staBox.classList.remove('hidden');

            dynTab.className = 'flex-1 py-1.5 px-2 rounded-xl text-gray-600 hover:text-gray-900 transition flex items-center justify-center space-x-1';
            staTab.className = 'flex-1 py-1.5 px-2 rounded-xl bg-white text-blue-800 font-bold shadow-sm transition flex items-center justify-center space-x-1';
        } else {
            staBox.classList.add('hidden');
            dynBox.classList.remove('hidden');

            staTab.className = 'flex-1 py-1.5 px-2 rounded-xl text-gray-600 hover:text-gray-900 transition flex items-center justify-center space-x-1';
            dynTab.className = 'flex-1 py-1.5 px-2 rounded-xl bg-white text-emerald-800 font-bold shadow-sm transition flex items-center justify-center space-x-1';
        }
    }

    function copyNominal() {
        navigator.clipboard.writeText(totalAmount.toString()).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Nominal Disalin!',
                text: 'Nominal Rp ' + totalAmount.toLocaleString('id-ID') + ' telah disalin ke clipboard.',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    function copyDanaNumber() {
        navigator.clipboard.writeText(danaNumber).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Nomor DANA Disalin!',
                text: danaNumber + ' telah disalin ke clipboard.',
                timer: 1500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    function openShareModal() {
        document.getElementById('sharePaymentModal').classList.remove('hidden');
    }

    function closeShareModal() {
        document.getElementById('sharePaymentModal').classList.add('hidden');
    }

    function copyPaymentLink() {
        const link = "{{ $paymentShareUrl ?? url()->current() }}";
        navigator.clipboard.writeText(link).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Tautan Disalin!',
                text: 'Tautan tagihan berhasil disalin. Buka atau kirimkan ke HP 2 untuk membayar.',
                timer: 1800,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    function openUploadModal() {
        document.getElementById('uploadProofModal').classList.remove('hidden');
    }

    function closeUploadModal() {
        document.getElementById('uploadProofModal').classList.add('hidden');
    }

    async function confirmPaymentAction() {
        const btn = document.getElementById('btnConfirmPay');
        if (!btn) return;
        const originalHtml = btn.innerHTML;

        const result = await Swal.fire({
            title: 'Konfirmasi Pembayaran QRIS',
            html: `Pastikan Anda telah berhasil menyelesaikan transfer sebesar <b>Rp ${totalAmount.toLocaleString('id-ID')}</b> via m-Banking/QRIS DANA Bisnis.<br><br><span class="text-xs text-amber-700 font-semibold bg-amber-50 p-2.5 rounded-xl block text-left border border-amber-200"><i class="fa-solid fa-shield-halved mr-1 text-amber-600"></i>Kasir akan memeriksa mutasi masuk di akun DANA Bisnis kafe sebelum pesanan diracik barista.</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Sudah Bayar',
            cancelButtonText: 'Cek Dulu'
        });

        if (!result.isConfirmed) return;

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i><span>Mengirim ke Kasir...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";
            const res = await fetch(`/pesanan/${orderToken}/konfirmasi-bayar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ _token: csrfToken })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Konfirmasi Terkirim!',
                    text: data.message || 'Kasir sedang memeriksa mutasi DANA Bisnis. Anda dialihkan ke halaman antrean.',
                    timer: 2500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message || 'Gagal mengonfirmasi');
            }
        } catch (e) {
            Swal.fire('Gagal', e.message || 'Terjadi kesalahan sistem, silakan coba lagi.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    async function submitProofForm(e) {
        e.preventDefault();
        const form = document.getElementById('uploadProofForm');
        const formData = new FormData(form);
        const btn = document.getElementById('btnSubmitProof');
        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i><span>Mengunggah...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";
            const res = await fetch(`/pesanan/${orderToken}/konfirmasi-bayar`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                isAlreadyPaid = true;
                closeUploadModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Bukti Diterima & Lunas!',
                    text: 'Terima kasih, pembayaran pesanan Anda berhasil diselesaikan.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message || 'Gagal mengunggah bukti');
            }
        } catch (e) {
            Swal.fire('Gagal', e.message || 'Gagal mengirim bukti pembayaran.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    // Auto Polling Cek Status Pembayaran Real-Time (Sync Antar-HP 1 & HP 2)
    async function pollPaymentStatus() {
        if (isAlreadyPaid) return;

        try {
            const res = await fetch(`/api/pesanan/${orderToken}/status`);
            if (!res.ok) return;

            const data = await res.json();
            if (data.success && data.is_paid) {
                isAlreadyPaid = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Dikonfirmasi!',
                    html: `Pembayaran sebesar <b>Rp ${data.total_price}</b> telah berhasil dikonfirmasi.<br>Pesanan Anda kini otomatis masuk antrean pembuatan barista.`,
                    timer: 2200,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = `/pesanan/${orderToken}`;
                });
            }
        } catch (e) {
            // silent catch
        }
    }

    setInterval(pollPaymentStatus, 2000);

    setInterval(pollPaymentStatus, 2500);
</script>
@endpush

