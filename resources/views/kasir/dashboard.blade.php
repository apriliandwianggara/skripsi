@extends('layouts.admin')

@section('title', 'Dashboard Antrean FIFO Kasir - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <!-- Top KPI Performance Bar -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Antrean Menunggu -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Antrean Menunggu</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1" id="kpiWaitingCount">
                    {{ $antreanFifo->where('status_pesanan', 'menunggu')->count() }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Prioritas Algoritma FIFO</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hourglass-start"></i>
            </div>
        </div>

        <!-- Sedang Diracik -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sedang Diracik</p>
                <h3 class="text-2xl font-black text-blue-600 mt-1" id="kpiProcessingCount">
                    {{ $antreanFifo->where('status_pesanan', 'diproses')->count() }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Oleh Barista di Meja Bar</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
        </div>

        <!-- Siap Diambil di Kasir -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Siap di Meja Kasir</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1" id="kpiReadyCount">
                    {{ $pesananSiap->count() }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Menunggu Pelanggan & Kasir</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>

        <!-- Rata-rata Layanan Hari Ini -->
        <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rata-rata Waktu FIFO</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">
                    {{ $metrics['rata_rata_waktu_layanan_menit'] }} <span class="text-sm font-normal text-gray-500">menit</span>
                </h3>
                <p class="text-[11px] text-emerald-600 font-semibold mt-0.5">{{ $metrics['total_hari_ini'] }} total pesanan hari ini</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sekarsa-50 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-stopwatch"></i>
            </div>
        </div>
    </div>

    <!-- Main Workspace: 2-Column Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Column 1 (7 cols): Active FIFO Queue (Menunggu & Diproses) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <h2 class="text-base font-bold text-gray-900 font-serif">Antrean Pengerjaan FIFO</h2>
                    <span class="text-xs font-mono font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full" id="liveQueueTotalBadge">
                        {{ $antreanFifo->count() }} Pesanan
                    </span>
                </div>
                <span class="text-xs text-gray-400 flex items-center">
                    <i class="fa-solid fa-arrows-rotate fa-spin mr-1 text-emerald-600 text-[11px]"></i> Live Sync 4s
                </span>
            </div>

            @if($antreanFifo->isEmpty())
            <div class="bg-white rounded-3xl p-10 text-center border border-gray-200/80 shadow-sm">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3 class="text-base font-bold text-gray-800">Semua Antrean Bersih!</h3>
                <p class="text-xs text-gray-500 mt-1">Belum ada pesanan aktif yang menunggu saat ini.</p>
            </div>
            @else
            <div class="space-y-3.5" id="fifoQueueCardsContainer">
                @foreach($antreanFifo as $index => $order)
                <div class="bg-white rounded-2xl border-2 {{ $order->status_pesanan === 'diproses' ? 'border-blue-400 bg-blue-50/20' : ($index === 0 ? 'border-amber-400 shadow-md' : 'border-gray-200') }} p-4 shadow-sm transition">
                    
                    <!-- Top Info Line -->
                    <div class="flex items-start justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <!-- FIFO Rank Badge -->
                            <div class="px-2.5 py-1 rounded-xl text-xs font-black uppercase tracking-wider {{ $index === 0 ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-700' }}">
                                Urutan #{{ $index + 1 }}
                            </div>

                            <div>
                                <div class="flex items-center space-x-2">
                                    <h3 class="text-lg font-black font-mono text-gray-900">{{ $order->nomor_pesanan }}</h3>
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        Meja {{ $order->meja->nomor_meja }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Pemesan: <strong class="text-gray-800">{{ $order->nama_pelanggan }}</strong> &bull;
                                    Masuk: <span class="font-mono">{{ $order->waktu_masuk->format('H:i') }}</span>
                                    ({{ $order->waktu_masuk->diffForHumans() }})
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            @if($order->status_pesanan === 'diproses')
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-100 text-blue-800 animate-pulse">
                                    <i class="fa-solid fa-mug-hot"></i>
                                    <span>Sedang Diracik</span>
                                </span>
                            @else
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Menunggu</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="py-3 space-y-1.5">
                        @foreach($order->items as $item)
                        <div class="flex items-start justify-between text-xs">
                            <div class="flex items-start space-x-2">
                                <span class="font-extrabold text-emerald-800 w-5">{{ $item->jumlah }}x</span>
                                <div>
                                    <span class="font-semibold text-gray-800">{{ $item->nama_menu }}</span>
                                    @if($item->catatan)
                                        <p class="text-[11px] text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60 inline-block mt-0.5">
                                            <i class="fa-solid fa-pen-to-square text-[9px] mr-1"></i> {{ $item->catatan }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <span class="font-mono text-gray-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach

                        @if($order->catatan_umum)
                        <div class="mt-2 p-2 bg-gray-50 rounded-xl border border-gray-200 text-xs text-gray-700">
                            <strong>Catatan Tambahan:</strong> {{ $order->catatan_umum }}
                        </div>
                        @endif
                    </div>

                    <!-- Bottom Bar & Actions -->
                    <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="text-xs text-gray-400">Total:</span>
                            <span class="text-sm font-black text-gray-900 ml-1">Rp {{ number_format($order->total_akhir, 0, ',', '.') }}</span>
                            <span class="ml-2 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $order->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">
                                {{ $order->status_pembayaran_label }}
                            </span>
                        </div>

                        <!-- Action Buttons (Alur 4, 6) -->
                        <div class="flex items-center space-x-2">
                            @if($order->status_pesanan === 'menunggu')
                                <form action="{{ route('kasir.order.process', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-1.5">
                                        <i class="fa-solid fa-fire text-[11px]"></i>
                                        <span>Proses Pesanan</span>
                                    </button>
                                </form>
                            @elseif($order->status_pesanan === 'diproses')
                                <form action="{{ route('kasir.order.complete', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center space-x-1.5">
                                        <i class="fa-solid fa-check text-[11px]"></i>
                                        <span>Pesanan Selesai (Bunyikan Bel)</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Cancel Button -->
                            <button type="button" onclick="openCancelModal({{ $order->id }}, '{{ $order->nomor_pesanan }}')" class="p-2 text-gray-400 hover:text-red-600 transition" title="Batalkan Pesanan">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Column 2 (5 cols): Ready for Pickup & Payment Desk (`selesai`) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-cash-register text-emerald-700"></i>
                    <h2 class="text-base font-bold text-gray-900 font-serif">Meja Kasir & Penyerahan</h2>
                </div>
                <span class="text-xs font-mono font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                    {{ $pesananSiap->count() }} Siap
                </span>
            </div>

            @if($pesananSiap->isEmpty())
            <div class="bg-white rounded-3xl p-8 text-center border border-gray-200/80 shadow-sm">
                <i class="fa-solid fa-bell-slash text-gray-300 text-3xl mb-2"></i>
                <p class="text-xs font-semibold text-gray-500">Belum ada pesanan selesai yang menunggu pengambilan di kasir.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($pesananSiap as $readyOrder)
                <div class="bg-white rounded-2xl border-2 border-emerald-300 p-4 shadow-sm space-y-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-xl font-black font-mono text-emerald-800">{{ $readyOrder->nomor_pesanan }}</h3>
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    Meja {{ $readyOrder->meja->nomor_meja }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 font-medium mt-0.5">{{ $readyOrder->nama_pelanggan }} &bull; {{ $readyOrder->items->count() }} Item</p>
                            <p class="text-[11px] text-gray-400">Selesai: {{ $readyOrder->waktu_selesai ? $readyOrder->waktu_selesai->format('H:i') : '-' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Total Tagihan:</p>
                            <p class="text-base font-black text-gray-900">Rp {{ number_format($readyOrder->total_akhir, 0, ',', '.') }}</p>
                            
                            @if($readyOrder->status_pembayaran === 'lunas')
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <i class="fa-solid fa-circle-check"></i> LUNAS
                                </span>
                            @elseif($readyOrder->status_pembayaran === 'menunggu_konfirmasi')
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-100 text-blue-800 animate-pulse">
                                    Verifikasi QRIS
                                </span>
                            @else
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800">
                                    BELUM DIBAYAR
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Bukti QRIS jika pelanggan upload -->
                    @if($readyOrder->bukti_qris)
                    <div class="p-2.5 bg-blue-50 rounded-xl border border-blue-200 flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-xs text-blue-800 font-medium">
                            <i class="fa-solid fa-receipt text-blue-600"></i>
                            <span>Bukti bayar QRIS diunggah</span>
                        </div>
                        <a href="{{ asset('storage/' . $readyOrder->bukti_qris) }}" target="_blank" class="text-xs text-blue-700 hover:underline font-bold">
                            Lihat Foto &rarr;
                        </a>
                    </div>
                    @endif

                    <!-- Action Controls (Bayar, Struk, Diambil) -->
                    <div class="pt-2 border-t border-gray-100 flex flex-wrap gap-2">
                        @if($readyOrder->status_pembayaran !== 'lunas')
                            <button type="button" onclick="openPaymentModal({{ json_encode($readyOrder) }})"
                                class="flex-1 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center space-x-1.5">
                                <i class="fa-solid fa-wallet"></i>
                                <span>Bayar (Lunas)</span>
                            </button>
                        @endif

                        <a href="{{ route('kasir.order.receipt', $readyOrder->id) }}" target="_blank"
                            class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition flex items-center space-x-1" title="Cetak Nota Transaksi">
                            <i class="fa-solid fa-print"></i>
                            <span>Struk</span>
                        </a>

                        <!-- Konfirmasi Diambil Pelanggan -->
                        <form action="{{ route('kasir.order.pickup', $readyOrder->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center justify-center space-x-1">
                                <i class="fa-solid fa-hand-holding"></i>
                                <span>Serahkan (Diambil)</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Riwayat Transaksi Selesai Hari Ini -->
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 shadow-sm mt-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center justify-between">
                    <span>Riwayat Transaksi Hari Ini</span>
                    <span class="text-[11px] font-normal text-gray-400">{{ $riwayatHariIni->count() }} transaksi</span>
                </h3>
                <div class="divide-y divide-gray-100 max-h-56 overflow-y-auto pr-1">
                    @forelse($riwayatHariIni as $r)
                    <div class="py-2 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold font-mono text-gray-800">{{ $r->nomor_pesanan }}</span>
                            <span class="text-gray-400 ml-1">Meja {{ $r->meja->nomor_meja }} &bull; {{ $r->nama_pelanggan }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-gray-900">Rp {{ number_format($r->total_akhir, 0, ',', '.') }}</span>
                            <a href="{{ route('kasir.order.receipt', $r->id) }}" target="_blank" class="text-emerald-700 ml-2 hover:underline">
                                <i class="fa-solid fa-print"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 py-3 text-center">Belum ada riwayat pesanan hari ini.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Modal Pembayaran (Tunai / QRIS) -->
<div id="paymentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-gray-900 text-base">Proses Pembayaran</h3>
                <p class="text-xs text-gray-500" id="payModalSubtitle">Pesanan #A001</p>
            </div>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="payForm" action="" method="POST" class="mt-4 space-y-4">
            @csrf

            <!-- Tagihan -->
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100 text-center">
                <p class="text-xs text-emerald-800 font-semibold uppercase">Total Tagihan</p>
                <h2 class="text-3xl font-black text-emerald-900 mt-0.5" id="payModalTotalFormatted">Rp 0</h2>
                <input type="hidden" id="payOrderTotal" value="0">
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pilih Metode</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="tunai" checked onchange="togglePayMethod('tunai')" class="peer sr-only">
                        <div class="p-3 rounded-xl border-2 border-gray-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/50 text-center font-bold text-xs text-gray-700 transition">
                            <i class="fa-solid fa-money-bill-wave text-base block mb-1 text-emerald-700"></i>
                            Uang Tunai
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="qris" onchange="togglePayMethod('qris')" class="peer sr-only">
                        <div class="p-3 rounded-xl border-2 border-gray-200 peer-checked:border-emerald-600 peer-checked:bg-emerald-50/50 text-center font-bold text-xs text-gray-700 transition">
                            <i class="fa-solid fa-qrcode text-base block mb-1 text-red-600"></i>
                            QRIS
                        </div>
                    </label>
                </div>
            </div>

            <!-- Cash Input & Quick Amount Buttons -->
            <div id="cashInputSection" class="space-y-2">
                <label for="payAmount" class="block text-xs font-bold text-gray-700 uppercase">Jumlah Uang Diterima (Rp)</label>
                <input type="number" name="jumlah_bayar" id="payAmount" required min="0" oninput="calculateChange()"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:outline-none focus:ring-2 focus:ring-emerald-500">

                <div class="flex flex-wrap gap-1.5 pt-1">
                    <button type="button" onclick="setCashAmount('pas')" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">
                        Uang Pas
                    </button>
                    <button type="button" onclick="setCashAmount(20000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">
                        20.000
                    </button>
                    <button type="button" onclick="setCashAmount(50000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">
                        50.000
                    </button>
                    <button type="button" onclick="setCashAmount(100000)" class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">
                        100.000
                    </button>
                </div>

                <!-- Kembalian Display -->
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 flex justify-between items-center text-xs">
                    <span class="font-bold text-gray-600">Kembalian:</span>
                    <span class="text-base font-black text-emerald-800" id="payChangeFormatted">Rp 0</span>
                </div>
            </div>

            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closePaymentModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan Lunas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Batalkan Pesanan -->
<div id="cancelModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-gray-100">
        <h3 class="font-bold text-gray-900 text-base" id="cancelModalTitle">Batalkan Pesanan</h3>
        <p class="text-xs text-gray-500 mt-1">Harap sertakan alasan pembatalan untuk arsip aktivitas.</p>

        <form id="cancelForm" action="" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
                <label for="alasanBatal" class="block text-xs font-bold text-gray-700 mb-1">Alasan Pembatalan</label>
                <input type="text" name="alasan" id="alasanBatal" required placeholder="Contoh: Pelanggan salah pesan / minta batal"
                    class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closeCancelModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Tutup
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition">
                    Batalkan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let activePayOrder = null;

    function openPaymentModal(order) {
        activePayOrder = order;
        document.getElementById('payForm').action = "/kasir/pesanan/" + order.id + "/bayar";
        document.getElementById('payModalSubtitle').innerText = "Pesanan " + order.nomor_pesanan + " (Meja " + order.meja.nomor_meja + " - " + order.nama_pelanggan + ")";
        document.getElementById('payModalTotalFormatted').innerText = "Rp " + Number(order.total_akhir).toLocaleString('id-ID');
        document.getElementById('payOrderTotal').value = order.total_akhir;
        
        // Reset inputs
        document.getElementById('payAmount').value = order.total_akhir;
        calculateChange();

        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    function togglePayMethod(method) {
        const cashSec = document.getElementById('cashInputSection');
        if (method === 'qris') {
            document.getElementById('payAmount').value = activePayOrder.total_akhir;
            calculateChange();
        }
    }

    function setCashAmount(val) {
        if (!activePayOrder) return;
        if (val === 'pas') {
            document.getElementById('payAmount').value = activePayOrder.total_akhir;
        } else {
            document.getElementById('payAmount').value = val;
        }
        calculateChange();
    }

    function calculateChange() {
        const total = parseFloat(document.getElementById('payOrderTotal').value || 0);
        const amount = parseFloat(document.getElementById('payAmount').value || 0);
        const change = Math.max(0, amount - total);
        document.getElementById('payChangeFormatted').innerText = "Rp " + change.toLocaleString('id-ID');
    }

    // Modal Cancel
    function openCancelModal(orderId, orderNum) {
        document.getElementById('cancelForm').action = "/kasir/pesanan/" + orderId + "/batal";
        document.getElementById('cancelModalTitle').innerText = "Batalkan Pesanan " + orderNum;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Auto Refresh FIFO Queue Polling (setiap 4 detik)
    setInterval(() => {
        fetch("{{ route('kasir.queue_api') }}")
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                const liveBadge = document.getElementById('liveQueueTotalBadge');
                if (liveBadge) liveBadge.innerText = data.antrean_count + " Pesanan";

                const sideBadge = document.getElementById('sidebarQueueBadge');
                if (sideBadge) sideBadge.innerText = data.antrean_count;

                const kpiWaiting = document.getElementById('kpiWaitingCount');
                // If the number of orders in queue changes, we can refresh the page gently or update the badge
                const currentCards = document.querySelectorAll('#fifoQueueCardsContainer > div').length;
                if (currentCards !== data.antrean_count) {
                    window.location.reload();
                }
            })
            .catch(e => console.error("Error polling queue:", e));
    }, 4000);
</script>
@endpush
