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
                <span>QRIS & E-Wallet</span>
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

        <!-- Card Pembayaran QRIS -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="font-black text-lg tracking-tighter text-red-600 font-sans">QRIS</span>
                    <span class="text-xs text-gray-400 font-bold">&bull;</span>
                    <span class="text-xs font-semibold text-gray-600">Semua m-Banking & E-Wallet</span>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Resmi
                </span>
            </div>

            <div class="space-y-1">
                <h3 class="font-bold text-sm text-gray-900">Pindai QRIS untuk Menyelesaikan Pembayaran</h3>
                <p class="text-xs text-gray-500 leading-relaxed">
                    Mendukung pembayaran dari BCA Mobile, Livin' Mandiri, BRImo, BNI Mobile, DANA, GoPay, dan ShopeePay.
                </p>
            </div>

            <!-- Tombol Utama: Buka QRIS -->
            <button type="button" onclick="payWithMidtrans()" id="btnPayMidtrans" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white font-extrabold rounded-2xl text-sm transition shadow-lg shadow-emerald-600/25 flex items-center justify-center space-x-2">
                <i class="fa-solid fa-qrcode text-lg"></i>
                <span>Tampilkan Barcode QRIS</span>
            </button>

            <!-- Tombol Alternatif / Konfirmasi Cepat -->
            <button type="button" onclick="confirmPaymentDirect()" id="btnConfirmPay" class="w-full py-3 bg-gray-100 hover:bg-gray-200 active:scale-95 text-gray-700 font-bold rounded-2xl text-xs transition flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>Saya Sudah Selesai Bayar</span>
            </button>

            <!-- Logo Bank & E-Wallet -->
            <div class="pt-2 border-t border-gray-100 text-center">
                <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] text-gray-500 font-medium">
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">BCA</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">Mandiri</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">BRI</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">BNI</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">DANA</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">GoPay</span>
                    <span class="px-2 py-0.5 bg-gray-50 rounded border border-gray-200">ShopeePay</span>
                </div>
            </div>
        </div>

        <!-- Rincian Pesanan Singkat -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 space-y-2 text-xs">
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
<script src="{{ $midtransSnapJs }}" data-client-key="{{ $midtransClientKey }}"></script>
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

    // Tampilkan QRIS Resmi
    async function payWithMidtrans() {
        const btn = document.getElementById('btnPayMidtrans');
        if (!btn) return;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin text-base mr-1.5"></i><span>Memuat QRIS...</span>`;

        try {
            const res = await fetch(`/pesanan/${orderToken}/midtrans-token`);
            const data = await res.json();

            if (!res.ok || !data.success) {
                throw new Error(data.message || 'Gagal memuat sesi QRIS.');
            }

            if (data.is_paid) {
                window.location.href = data.redirect_url;
                return;
            }

            if (!window.snap || typeof window.snap.pay !== 'function') {
                throw new Error('Modul pembayaran belum siap. Silakan muat ulang halaman.');
            }

            // Buka Modal Snap QRIS
            window.snap.pay(data.snap_token, {
                onSuccess: async function(result) {
                    await markAsPaidAndRedirect(result?.payment_type || 'qris');
                },
                onPending: function(result) {
                    pollPaymentStatus();
                },
                onError: function(result) {
                    Swal.fire('Kendala Pembayaran', 'Terjadi kesalahan pada transaksi. Silakan coba lagi.', 'error');
                },
                onClose: function() {
                    pollPaymentStatus();
                }
            });
        } catch (err) {
            Swal.fire('Kendala', err.message || 'Gagal memuat QRIS.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    // Tombol Konfirmasi Langsung Selesai Bayar
    async function confirmPaymentDirect() {
        const confirm = await Swal.fire({
            title: 'Konfirmasi Pembayaran',
            text: 'Apakah Anda telah menyelesaikan pembayaran sebesar Rp ' + totalAmount.toLocaleString('id-ID') + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Sudah Bayar',
            cancelButtonText: 'Batal'
        });

        if (!confirm.isConfirmed) return;

        await markAsPaidAndRedirect('qris');
    }

    // Eksekusi Ubah Status Menjadi LUNAS Seketika & Alihkan ke Antrean
    async function markAsPaidAndRedirect(paymentType) {
        isAlreadyPaid = true;

        Swal.fire({
            title: 'Memverifikasi Pembayaran...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";
            const res = await fetch(`/pesanan/${orderToken}/konfirmasi-bayar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    _token: csrfToken,
                    payment_type: paymentType
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Lunas',
                    text: 'Terima kasih, pembayaran pesanan Anda telah lunas!',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                throw new Error(data.message || 'Gagal mengonfirmasi');
            }
        } catch (e) {
            // Fallback ke tracking jika redirect
            window.location.href = `/pesanan/${orderToken}`;
        }
    }

    // Polling Status Otomatis
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
                    text: 'Terima kasih, pembayaran pesanan Anda telah lunas!',
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

    setInterval(pollPaymentStatus, 2000);
</script>
@endpush
