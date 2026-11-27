@extends('layouts.app')

@section('title', 'Pembayaran Pesanan Rp ' . number_format($order->total_price, 0, ',', '.') . ' - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-50 flex flex-col py-6 px-4 pb-20">
    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Header Kafe -->
        <div class="flex items-center justify-between px-1">
            <div class="flex items-center space-x-2.5">
                <div class="w-10 h-10 rounded-xl bg-white p-0.5 border border-emerald-600/30 flex items-center justify-center shadow overflow-hidden">
                    <img src="{{ $setting->logo_url }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h2 class="font-bold text-sm text-gray-800 font-serif">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h2>
                    <p class="text-[10px] text-gray-500">Pembayaran Pesanan</p>
                </div>
            </div>

            <div class="flex items-center space-x-1.5 text-[11px] text-amber-800 bg-amber-100/80 px-2.5 py-1 rounded-full font-medium">
                <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
                <span>Menunggu Pembayaran</span>
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

        <!-- Box Portal Pembayaran -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-150 p-6 space-y-5 text-center">
            
            <div class="py-2">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-700 rounded-2xl mx-auto flex items-center justify-center text-2xl shadow-inner border border-emerald-100">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h3 class="font-bold text-base text-gray-900 mt-3">
                    Selesaikan Pembayaran
                </h3>
                <p class="text-xs text-gray-500 max-w-xs mx-auto mt-1 leading-relaxed">
                    Pilih metode pembayaran favorit Anda (QRIS, Transfer Bank, atau E-Wallet).
                </p>
            </div>

            <!-- Tombol Utama: Bayar Sekarang -->
            <div class="space-y-2.5 pt-1">
                <button type="button" id="payBtn" onclick="payNow()" class="w-full py-4 px-4 bg-emerald-700 hover:bg-emerald-800 active:scale-[0.98] text-white font-bold rounded-2xl shadow-lg shadow-emerald-700/25 transition flex items-center justify-center space-x-2 text-sm cursor-pointer">
                    <i class="fa-solid fa-credit-card text-base"></i>
                    <span>Bayar Sekarang</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>

                <p class="text-[11px] text-gray-400">
                    Mendukung QRIS, Virtual Account & E-Wallet
                </p>
            </div>

            <!-- Status Pemantauan Ringkas -->
            <div class="flex items-center justify-center space-x-2 py-1 text-xs text-gray-500">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>Menunggu konfirmasi pembayaran</span>
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
    const csrfToken = "{{ csrf_token() }}";
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

    // Pembayaran Otomatis
    async function payNow() {
        const btn = document.getElementById('payBtn');
        const origText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Menghubungkan ke Pembayaran...';

        try {
            const res = await fetch(`/pesanan/${orderToken}/doku-checkout`);
            const data = await res.json();

            if (!data.success || !data.payment_url) {
                throw new Error(data.message || 'Gagal memuat halaman pembayaran.');
            }

            if (data.is_paid) {
                window.location.href = data.redirect_url;
                return;
            }

            // Arahkan ke halaman pembayaran
            window.location.href = data.payment_url;
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Pembayaran',
                text: err.message || 'Terjadi kendala saat memuat pembayaran. Silakan coba lagi.'
            });
            btn.disabled = false;
            btn.innerHTML = origText;
        }
    }

    // Pemantauan Transaksi Real-Time
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
                    title: 'Pembayaran Berhasil!',
                    text: 'Terima kasih, pembayaran pesanan Anda telah diterima.',
                    timer: 1800,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = `/pesanan/${orderToken}`;
                });
            }
        } catch (e) {
            // silent catch
        }
    }

    // Polling aktif memeriksa status setiap 2 detik
    setInterval(pollPaymentStatus, 2000);
</script>
@endpush
