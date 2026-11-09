@extends('layouts.admin')

@section('title', 'Detail Pesanan #' . $order->queue_number . ' - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <span class="text-3xl font-black font-mono text-emerald-900">#{{ $order->queue_number }}</span>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    {{ $order->status === 'menunggu' ? 'bg-amber-100 text-amber-800' : '' }}
                    {{ $order->status === 'diproses' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $order->status === 'siap_diambil' ? 'bg-emerald-100 text-emerald-800' : '' }}
                    {{ $order->status === 'selesai' ? 'bg-gray-100 text-gray-800' : '' }}
                    {{ $order->status === 'dibatalkan' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Nomor Pesanan: <strong class="font-mono text-gray-700">{{ $order->order_number }}</strong></p>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- 2 Kolom Konten -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Rincian Pesanan & Pemesan -->
        <div class="md:col-span-2 space-y-6">
            <!-- Card Pemesan -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-3">
                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-400">Informasi Pemesan</h3>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-gray-400 block">Nama Pelanggan</span>
                        <strong class="text-gray-900 text-sm">{{ $order->customer_name }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Nomor WhatsApp</span>
                        <span class="text-gray-800 font-mono">{{ $order->customer_phone ?: 'Tidak diisi' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Meja Pelanggan</span>
                        <span class="text-gray-800 font-semibold">{{ $order->table_number ? 'Meja ' . $order->table_number : 'Pesan di Tempat' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Waktu Diterima Server (FIFO)</span>
                        <span class="text-gray-800 font-mono">{{ $order->received_at->format('H:i:s d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Metode Pembayaran</span>
                        <span class="font-bold text-gray-800">{{ $order->payment_method_label }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Status Pembayaran</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $order->isPaid() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $order->payment_status_label }}
                            @if($order->paid_at)
                                <span class="ml-1 text-[9px] font-normal font-mono">({{ $order->paid_at->format('H:i') }})</span>
                            @endif
                        </span>
                    </div>
                </div>

                @if($order->customer_notes)
                    <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                        <strong>Catatan Khusus Pelanggan:</strong>
                        <p class="mt-0.5 italic">"{{ $order->customer_notes }}"</p>
                    </div>
                @endif
            </div>

            <!-- Card Rincian Menu -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-gray-500">Rincian Sajian yang Dipesan</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($order->details as $item)
                        <div class="p-4 flex items-center justify-between text-xs hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3">
                                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-800 font-bold flex items-center justify-center font-mono">
                                    {{ $item->quantity }}x
                                </span>
                                <div>
                                    <h4 class="font-bold text-gray-900">{{ $item->menu_name }}</h4>
                                    <span class="text-gray-400 text-[11px]">Rp {{ number_format($item->price, 0, ',', '.') }} per porsi</span>
                                    @if($item->notes)
                                        <p class="text-[11px] text-gray-500 italic mt-0.5">"{{ $item->notes }}"</p>
                                    @endif
                                </div>
                            </div>
                            <span class="font-bold font-mono text-gray-800">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <span class="font-bold text-sm text-gray-900">Total Tagihan:</span>
                    <span class="font-black text-lg text-emerald-900 font-mono">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Timeline Riwayat Status & Notifikasi -->
        <div class="space-y-6">

            <!-- Card Timeline Status FIFO -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4">
                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-400 flex items-center">
                    <i class="fa-solid fa-clock-rotate-left mr-1.5 text-emerald-600"></i> Riwayat Perubahan Status
                </h3>

                <div class="space-y-3">
                    @forelse($order->statusHistory as $hist)
                        <div class="flex items-start space-x-2.5 text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 mt-1 shrink-0"></span>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $hist->new_status_label }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $hist->notes }}</p>
                                <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $hist->created_at->format('H:i:s') }} &bull; {{ $hist->changed_by }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada riwayat perubahan status.</p>
                    @endforelse
                </div>
            </div>

            <!-- Card Riwayat Notifikasi Pengambilan -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-3">
                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-400 flex items-center">
                    <i class="fa-solid fa-bell mr-1.5 text-emerald-600"></i> Notifikasi Pengambilan
                </h3>

                @forelse($order->notifications as $notif)
                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-150 text-xs space-y-1">
                        <div class="flex justify-between items-center text-[10px] text-gray-400">
                            <span class="font-bold uppercase text-emerald-800">{{ $notif->type }}</span>
                            <span class="font-mono">{{ $notif->created_at->format('H:i') }}</span>
                        </div>
                        <p class="text-[11px] text-gray-700">{{ $notif->message }}</p>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 text-center py-2">Belum ada log notifikasi</p>
                @endforelse

                <div class="pt-2">
                    <a href="{{ route('customer.track', $order->tracking_token) }}" target="_blank"
                       class="w-full py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 rounded-xl text-xs font-semibold transition flex items-center justify-center space-x-1.5">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        <span>Lihat Layar Pelanggan</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

