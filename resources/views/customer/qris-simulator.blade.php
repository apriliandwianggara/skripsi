@extends('layouts.app')

@section('title', 'Pembayaran QRIS Berhasil - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="min-h-screen bg-slate-100 flex flex-col justify-between py-6 px-4">
    <div class="max-w-md mx-auto w-full space-y-4">

        <!-- Top Header Brand -->
        <div class="bg-gradient-to-br from-emerald-800 via-emerald-900 to-emerald-950 text-white rounded-3xl p-5 shadow-xl space-y-3 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-amber-300 font-black">
                        <i class="fa-solid fa-qrcode"></i>
                    </span>
                    <div>
                        <h2 class="font-bold text-sm leading-tight">{{ $setting->cafe_name ?? 'Sekarsa Coffee' }}</h2>
                        <p class="text-[10px] text-emerald-200">QRIS Midtrans Payment Gateway</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-600 text-white font-extrabold text-[10px] tracking-wider uppercase">
                    Lunas
                </span>
            </div>

            <!-- Nominal Box -->
            <div class="bg-white/10 rounded-2xl p-4 text-center border border-white/10 space-y-1">
                <span class="text-[10px] uppercase tracking-wider text-emerald-200 font-bold block">TOTAL DIBAYAR</span>
                <span class="text-3xl font-black text-amber-300 font-mono tracking-tight block">
                    Rp {{ number_format($order->total_price, 0, ',', '.') }}
                </span>
            </div>

            <div class="pt-1 flex items-center justify-between text-xs text-emerald-200/90 border-t border-white/10">
                <span>{{ $order->order_number }} &bull; Antrean #{{ $order->queue_number }}</span>
                <span class="font-bold text-white">{{ $order->customer_name }}</span>
            </div>
        </div>

        <!-- Tampilan Sukses Pembayaran Otomatis dari Scan QRIS -->
        <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-150 text-center space-y-4">
            <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-3xl shadow-inner animate-bounce-subtle">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            
            <div class="space-y-1">
                <h3 class="font-extrabold text-gray-900 text-lg font-serif">Pembayaran Berhasil!</h3>
                <p class="text-xs text-gray-600 leading-relaxed px-3">
                    Kode QRIS berhasil dipindai dan pembayaran telah diverifikasi lunas secara otomatis oleh sistem.
                </p>
            </div>

            <div class="bg-emerald-50 border border-emerald-200/70 rounded-2xl p-3 text-xs text-emerald-900 space-y-1">
                <div class="font-bold text-emerald-800">
                    Nomor Antrean Anda: <span class="font-mono text-base text-emerald-950 font-black">#{{ $order->queue_number }}</span>
                </div>
                <p class="text-[11px] text-emerald-700">
                    Pesanan Anda langsung masuk ke antrean barista Sekarsa Coffee dan sedang dipersiapkan.
                </p>
            </div>

            <!-- Rincian Sajian -->
            <div class="bg-gray-50 rounded-2xl p-3 space-y-1.5 text-xs border border-gray-150 text-left">
                <span class="font-bold text-[10px] uppercase text-gray-400 block mb-1">Rincian Sajian:</span>
                @foreach($order->details as $item)
                    <div class="flex justify-between text-gray-700 py-0.5">
                        <span><b>{{ $item->quantity }}x</b> {{ $item->menu_name }}</span>
                        <span class="font-mono text-gray-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('customer.track', $order->tracking_token) }}" class="inline-flex items-center justify-center space-x-2 w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl text-xs shadow-lg shadow-emerald-600/25 transition active:scale-95">
                <i class="fa-solid fa-list-check"></i>
                <span>Lihat Status Antrean Saya</span>
            </a>

            <p class="text-[10px] text-gray-400 flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-circle-notch fa-spin text-emerald-600 text-xs"></i>
                <span>Mengalihkan otomatis ke layar antrean...</span>
            </p>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Otomatis arahkan ke halaman pelacakan antrean dalam 2 detik
    setTimeout(() => {
        window.location.href = "{{ route('customer.track', $order->tracking_token) }}";
    }, 2000);
</script>
@endpush