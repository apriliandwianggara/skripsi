@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran QRIS - Sekarsa Coffee')

@section('content')
<div class="min-h-screen bg-slate-100 flex flex-col justify-between py-6 px-4">
    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Top Header m-Banking style -->
        <div class="bg-emerald-900 text-white rounded-3xl p-5 shadow-xl space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-amber-300 font-black">
                        <i class="fa-solid fa-qrcode"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-sm leading-tight">Konfirmasi Pembayaran QRIS</h2>
                        <p class="text-[10px] text-emerald-200">Merchant: Sekarsa Coffee</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-red-600 text-white font-extrabold text-[10px] tracking-wider">
                    QRIS
                </span>
            </div>

            <!-- Nominal Box -->
            <div class="bg-white/10 rounded-2xl p-4 text-center border border-white/10 space-y-1">
                <span class="text-[10px] uppercase tracking-wider text-emerald-200 font-bold block">TOTAL PEMBAYARAN</span>
                <span class="text-3xl font-black text-amber-300 font-mono tracking-tight block">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>

            <div class="pt-1 flex items-center justify-between text-xs text-emerald-200/90 border-t border-white/10">
                <span>{{ $order->order_number }} &bull; Antrean #{{ $order->queue_number }}</span>
                <span class="font-bold text-white">{{ $order->customer_name }}</span>
            </div>
        </div>

        @if($order->isPaid())
            <!-- Jika Sudah Lunas -->
            <div class="bg-white rounded-3xl p-6 shadow-md border border-gray-150 text-center space-y-3">
                <div class="w-14 h-14 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-base">Pembayaran Berhasil!</h3>
                <p class="text-xs text-gray-500">Tagihan pesanan ini telah lunas dan sedang dipersiapkan oleh barista.</p>
                <a href="{{ route('customer.track', $order->tracking_token) }}" class="inline-block w-full py-3 bg-emerald-600 text-white font-bold rounded-2xl text-xs shadow-md">
                    Lihat Status Antrean
                </a>
            </div>
        @else
            <!-- Form Konfirmasi Pembayaran -->
            <div class="bg-white rounded-3xl p-5 shadow-md border border-gray-150 space-y-4">
                <h4 class="font-bold text-xs uppercase tracking-wider text-gray-400">Rincian Pembayaran</h4>

                <div class="space-y-2 text-xs divide-y divide-gray-100">
                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-500">Penerima</span>
                        <span class="font-bold text-gray-900">Sekarsa Coffee</span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-500">Nomor Antrean</span>
                        <span class="font-bold text-emerald-700">#{{ $order->queue_number }}</span>
                    </div>
                    @if($order->table_number)
                        <div class="flex justify-between py-1.5">
                            <span class="text-gray-500">Nomor Meja</span>
                            <span class="font-bold text-gray-900">Meja {{ $order->table_number }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-1.5">
                        <span class="text-gray-500">Sumber Dana</span>
                        <span class="font-semibold text-gray-800 flex items-center space-x-1">
                            <i class="fa-solid fa-wallet text-emerald-600 mr-1"></i>
                            <span>Saldo Rekening / E-Wallet</span>
                        </span>
                    </div>
                </div>

                <!-- Rincian Sajian -->
                <div class="bg-gray-50 rounded-2xl p-3 space-y-1.5 text-xs border border-gray-150">
                    <span class="font-bold text-[10px] uppercase text-gray-500 block mb-1">Pesanan:</span>
                    @foreach($order->details as $item)
                        <div class="flex justify-between text-gray-700">
                            <span><b>{{ $item->quantity }}x</b> {{ $item->menu_name }}</span>
                            <span class="font-mono">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Tombol Eksekusi Bayar -->
                <button type="button" onclick="submitQrisPayment()" id="btnSubmitQris" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-black rounded-2xl text-sm transition shadow-lg shadow-emerald-600/30 flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-lock text-sm"></i>
                    <span>Bayar Sekarang (Rp {{ number_format($order->total_price, 0, ',', '.') }})</span>
                </button>

                <p class="text-center text-[10px] text-gray-400 flex items-center justify-center space-x-1">
                    <i class="fa-solid fa-shield-halved text-emerald-600 text-xs"></i>
                    <span>Transaksi diproses secara aman dan langsung terverifikasi.</span>
                </p>
            </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
    const orderToken = "{{ $order->tracking_token }}";

    async function submitQrisPayment() {
        const btn = document.getElementById('btnSubmitQris');
        if (!btn) return;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-base mr-1.5"></i><span>Memproses Transaksi...</span>`;

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
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    text: 'Tagihan telah berhasil dibayar lunas. Dialihkan ke antrean...',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message || 'Gagal memproses transaksi.');
            }
        } catch (e) {
            Swal.fire('Kendala', e.message || 'Gagal menyelesaikan pembayaran.', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }
</script>
@endpush
