@extends('layouts.app')

@section('title', 'Pembayaran QRIS Rp ' . number_format($order->total_price, 0, ',', '.') . ' - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col py-6 px-4 pb-20">
    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Header Kafe -->
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-xl bg-white p-0.5 border border-emerald-600/30 flex items-center justify-center shadow overflow-hidden">
                    <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="font-bold text-sm text-gray-800 font-serif">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h2>
                    <p class="text-[10px] text-gray-500">Pembayaran Pesanan</p>
                </div>
            </div>

            <div class="flex items-center space-x-1.5 text-[11px] text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-full font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>QRIS Standar Nasional</span>
            </div>
        </div>

        <!-- Banner Info Tagihan -->
        <div class="bg-gradient-to-br from-emerald-800 via-emerald-900 to-emerald-950 text-white rounded-3xl p-5 shadow-xl text-center space-y-2 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl pointer-events-none"></div>

            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-300 block">TOTAL TAGIHAN</span>
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

        <!-- Box Tampilan QRIS Resmi Sekarsa Coffee -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-150 p-6 space-y-4 text-center">
            
            <!-- Header QRIS Standar Nasional (GPN) -->
            <div class="flex items-center justify-between border-b border-gray-150 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="font-black text-2xl tracking-tighter text-red-600 font-sans">QRIS</span>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Standar Pembayaran Nasional</span>
                </div>
                <div class="flex items-center space-x-1">
                    <span class="px-2 py-0.5 bg-red-600 text-white font-extrabold text-[10px] rounded">GPN</span>
                </div>
            </div>

            <!-- Identitas Merchant -->
            <div class="space-y-0.5 text-center">
                <h3 class="font-bold text-sm text-gray-900 leading-tight">
                    {{ $danaMerchantName ?? 'M Aprilian Dwi Anggara, S (Sekarsa Coffee)' }}
                </h3>
                <p class="text-[11px] text-gray-500 font-mono">NMID: {{ $nmid ?? 'ID1026635802105' }} &bull; A01</p>
            </div>

            <!-- Barcode QRIS Resmi (Dapat discan langsung oleh kamera HP atau pemindai QR) -->
            <div class="relative inline-block mx-auto bg-white p-3 rounded-2xl border-2 border-dashed border-emerald-500/40 shadow-inner">
                <img src="{{ $simulatorQrUri }}" alt="QRIS Sekarsa Coffee" class="w-56 h-56 sm:w-64 sm:h-64 object-contain mx-auto rounded-xl">
                
                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-emerald-700 text-white text-[10px] font-bold px-3 py-0.5 rounded-full shadow">
                    QRIS Dinamis
                </div>
            </div>

            <!-- Petunjuk Pemindaian -->
            <p class="text-xs text-gray-500 leading-relaxed px-2">
                Pindai kode QRIS di atas menggunakan kamera HP, m-Banking, atau E-Wallet untuk menyelesaikan pembayaran.
            </p>

            <!-- Tombol Utama: Bayar Sekarang -->
            <button type="button" onclick="selesaikanPembayaran()" id="btnBayarSekarang" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold rounded-2xl text-sm transition shadow-lg shadow-emerald-600/25 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-lock text-sm"></i>
                <span>Bayar Sekarang (Rp {{ number_format($order->total_price, 0, ',', '.') }})</span>
            </button>

            <!-- Logo Bank & E-Wallet Terdaftar -->
            <div class="pt-3 border-t border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Menerima Pembayaran:</p>
                <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] text-gray-600 font-medium">
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">BCA Mobile</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">Livin' Mandiri</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">BRImo</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">BNI Mobile</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">DANA</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">GoPay</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">ShopeePay</span>
                </div>
            </div>

        </div>

        <!-- Rincian Sajian Singkat -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 space-y-2 text-xs shadow-sm">
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

    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderToken = "{{ $order->tracking_token }}";
    const totalAmount = {{ (int)$order->total_price }};
    let isAlreadyPaid = {{ $order->isPaid() ? 'true' : 'false' }};

    function copyNominal() {
        navigator.clipboard.writeText(totalAmount.toString()).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Nominal Disalin',
                text: 'Rp ' + totalAmount.toLocaleString('id-ID'),
                timer: 1200,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }

    // Proses Pembayaran Langsung dari Perangkat Ini
    async function selesaikanPembayaran() {
        const btn = document.getElementById('btnBayarSekarang');
        if (!btn) return;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-sm mr-2"></i><span>Memproses Pembayaran...</span>`;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";
            const res = await fetch(`/pesanan/${orderToken}/qris/bayar`, {
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
                isAlreadyPaid = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    text: 'Tagihan telah berhasil dibayar lunas. Dialihkan ke antrean...',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message || 'Gagal memproses pembayaran.');
            }
        } catch (err) {
            Swal.fire('Kendala', err.message || 'Gagal memproses transaksi.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    // Pemantauan Status Pembayaran (Otomatis mendeteksi jika dibayar via pemindaian kamera HP lain)
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
                    title: 'Pembayaran Lunas',
                    text: 'Terima kasih, pembayaran Anda telah berhasil diterima!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = `/pesanan/${orderToken}`;
                });
            }
        } catch (e) {
            // silent catch
        }
    }

    // Polling setiap 2 detik
    setInterval(pollPaymentStatus, 2000);
</script>
@endpush