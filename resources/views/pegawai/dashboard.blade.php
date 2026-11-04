@extends('layouts.admin')

@section('title', 'Dashboard Pegawai & Antrean FIFO - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Top KPI Bar & Info FIFO -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-list-ol text-emerald-600"></i>
                <span>Pusat Operasional Antrean FIFO</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                First In First Out: Pesanan yang masuk lebih awal berada pada prioritas pengerjaan terdepan.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-100 text-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping mr-2"></span>
                <span id="liveSyncStatus">Auto-Sync Aktif (4s)</span>
            </span>
            <button onclick="location.reload()" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-rotate-right mr-1"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-hourglass-start"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Menunggu</p>
                <p class="text-2xl font-extrabold text-amber-600 font-mono" id="statMenunggu">{{ $metrics['total_menunggu'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-fire-burner"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Sedang Diproses</p>
                <p class="text-2xl font-extrabold text-blue-600 font-mono" id="statDiproses">{{ $metrics['total_diproses'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Siap Diambil</p>
                <p class="text-2xl font-extrabold text-emerald-600 font-mono" id="statSiapDiambil">{{ $readyOrders->count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-200 shadow-sm flex items-center space-x-3">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Selesai Hari Ini</p>
                <p class="text-2xl font-extrabold text-purple-700 font-mono" id="statSelesai">{{ $metrics['total_selesai'] }}</p>
            </div>
        </div>
    </div>

    <!-- Main Operasional Area (2 Kolom: Antrean FIFO Utama & Counter Siap Diambil) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom 1 & 2: Antrean FIFO Aktif (Urutan Ketat received_at ASC) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                        <h2 class="font-bold text-sm text-gray-800 uppercase tracking-wide">
                            Antrean Aktif FIFO (Prioritas Pengerjaan)
                        </h2>
                    </div>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                        Total Antrean: {{ $fifoQueue->count() }}
                    </span>
                </div>

                @if($fifoQueue->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <i class="fa-solid fa-mug-saucer text-5xl mb-3 text-gray-300"></i>
                        <p class="font-bold text-gray-600 text-sm">Tidak ada antrean aktif saat ini</p>
                        <p class="text-xs text-gray-400 mt-1">Pesanan baru yang dikirim pelanggan akan langsung muncul di sini secara berurutan.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($fifoQueue as $index => $order)
                            <div class="p-5 hover:bg-slate-50/80 transition {{ $index === 0 ? 'bg-amber-50/30' : '' }}">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                    <div class="flex items-center space-x-3">
                                        <!-- Ranking Badge FIFO -->
                                        <div class="w-10 h-10 rounded-xl {{ $index === 0 ? 'bg-amber-500 text-white shadow-amber-500/30' : 'bg-gray-100 text-gray-700' }} flex flex-col items-center justify-center font-bold shrink-0 shadow-sm">
                                            <span class="text-[9px] uppercase leading-none {{ $index === 0 ? 'text-amber-100' : 'text-gray-400' }}">FIFO</span>
                                            <span class="text-base font-mono leading-none mt-0.5">#{{ $index + 1 }}</span>
                                        </div>

                                        <!-- Nomor Antrean & Info Pelanggan -->
                                        <div>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-black text-xl text-emerald-900 font-mono tracking-wide">{{ $order->queue_number }}</span>
                                                <span class="text-xs font-mono text-gray-400">({{ $order->order_number }})</span>
                                                @if($order->table_number)
                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-bold">Meja {{ $order->table_number }}</span>
                                                @endif
                                            </div>
                                            <p class="text-xs font-semibold text-gray-700 mt-0.5 flex items-center space-x-2">
                                                <span>{{ $order->customer_name }}</span>
                                                @if($order->customer_phone)
                                                    <span class="text-gray-300">&bull;</span>
                                                    <span class="text-gray-500 font-normal"><i class="fa-brands fa-whatsapp text-emerald-600"></i> {{ $order->customer_phone }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Status Badge & Elapsed Time -->
                                    <div class="flex sm:flex-col items-center sm:items-end justify-between text-right">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                            {{ $order->status === 'menunggu' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 animate-pulse"></span>
                                            {{ $order->status_label }}
                                        </span>
                                        <span class="text-[11px] text-gray-400 mt-1">
                                            <i class="fa-regular fa-clock mr-1"></i> Masuk: {{ $order->received_at->format('H:i') }} ({{ $order->received_at->diffForHumans() }})
                                        </span>
                                    </div>
                                </div>

                                <!-- Detail Item Pesanan -->
                                <div class="bg-white rounded-xl p-3 border border-gray-150 mb-3 space-y-1.5">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Daftar Minuman / Makanan:</p>
                                    @foreach($order->details as $detail)
                                        <div class="flex items-start justify-between text-xs">
                                            <div class="pr-2">
                                                <span class="font-bold text-emerald-700">{{ $detail->quantity }}x</span>
                                                <span class="font-semibold text-gray-800 ml-1">{{ $detail->menu_name }}</span>
                                                @if($detail->notes)
                                                    <span class="text-gray-500 italic block text-[11px] ml-5">"{{ $detail->notes }}"</span>
                                                @endif
                                            </div>
                                            <span class="text-gray-500 text-[11px] shrink-0 font-mono">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach

                                    @if($order->customer_notes)
                                        <div class="mt-2 pt-2 border-t border-gray-100 text-[11px] text-amber-800 bg-amber-50/60 p-2 rounded-lg">
                                            <strong>Catatan Khusus:</strong> {{ $order->customer_notes }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons (Proses Pesanan, Siap Diambil, Batalkan) -->
                                <div class="flex items-center justify-between pt-1">
                                    <div class="text-xs text-gray-500">
                                        Total: <strong class="text-emerald-900 font-bold text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        @if($order->status === 'menunggu')
                                            <!-- Tombol Mulai Proses -->
                                            <form action="{{ route('pegawai.order.process', $order) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                                                    <i class="fa-solid fa-play"></i>
                                                    <span>Mulai Proses</span>
                                                </button>
                                            </form>
                                        @elseif($order->status === 'diproses')
                                            <!-- Tombol Pesanan Siap Diambil (Kirim Notifikasi) -->
                                            <form action="{{ route('pegawai.order.ready', $order) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                                                    <i class="fa-solid fa-bell"></i>
                                                    <span>Pesanan Siap Diambil</span>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Tombol Batalkan Pesanan -->
                                        <button type="button" onclick="openCancelModal({{ $order->id }}, '{{ $order->queue_number }}')" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold transition">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Kolom 3: Pesanan Siap Diambil di Counter -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-emerald-900 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-bell text-amber-300"></i>
                        <h2 class="font-bold text-sm tracking-wide">Meja Counter / Siap Diambil</h2>
                    </div>
                    <span class="text-xs bg-emerald-800 text-emerald-100 font-bold px-2.5 py-0.5 rounded-full font-mono">
                        {{ $readyOrders->count() }}
                    </span>
                </div>

                @if($readyOrders->isEmpty())
                    <div class="p-8 text-center text-gray-400">
                        <i class="fa-solid fa-circle-check text-4xl mb-2 text-gray-300"></i>
                        <p class="font-semibold text-gray-600 text-xs">Belum ada pesanan siap diambil</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Pesanan yang telah siap akan tampil di sini untuk serah terima ke pelanggan.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                        @foreach($readyOrders as $ready)
                            <div class="p-4 bg-emerald-50/40 hover:bg-emerald-50 transition space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="font-mono font-black text-2xl text-emerald-900">{{ $ready->queue_number }}</span>
                                        <p class="text-xs font-bold text-gray-800">{{ $ready->customer_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-semibold text-[11px] font-mono">
                                            {{ $ready->ready_at ? $ready->ready_at->format('H:i') : '' }}
                                        </span>
                                        @if($ready->table_number)
                                            <p class="text-[11px] text-gray-500 font-semibold mt-0.5">Meja {{ $ready->table_number }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-xs text-gray-600">
                                    <span class="text-gray-400">{{ $ready->details->sum('quantity') }} Item:</span>
                                    <span class="font-medium truncate block">{{ $ready->details->pluck('menu_name')->join(', ') }}</span>
                                </div>

                                <!-- Action Buttons: WA & Selesai -->
                                <div class="flex items-center space-x-2 pt-1">
                                    @if($ready->customer_phone)
                                        <a href="{{ route('pegawai.order.whatsapp', $ready) }}" target="_blank"
                                           class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                            <span>Hubungi WA</span>
                                        </a>
                                    @endif

                                    <form action="{{ route('pegawai.order.complete', $ready) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                                            <i class="fa-solid fa-check"></i>
                                            <span>Selesai Diambil</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Card Riwayat Selesai Hari Ini Singkat -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-500 mb-3">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Terakhir Selesai Hari Ini
                </h3>

                @if($completedToday->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-3">Belum ada pesanan selesai hari ini</p>
                @else
                    <div class="space-y-2 text-xs">
                        @foreach($completedToday->take(5) as $comp)
                            <div class="flex items-center justify-between py-1 border-b border-gray-50">
                                <div>
                                    <span class="font-mono font-bold text-emerald-800">#{{ $comp->queue_number }}</span>
                                    <span class="text-gray-700 ml-1.5 font-medium">{{ $comp->customer_name }}</span>
                                </div>
                                <span class="text-gray-400 font-mono text-[11px]">{{ $comp->completed_at ? $comp->completed_at->format('H:i') : '' }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

<!-- Modal Konfirmasi Pembatalan -->
<div id="cancelModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl">
        <h3 class="font-bold text-base text-gray-900 mb-1">Batalkan Pesanan Antrean?</h3>
        <p class="text-xs text-gray-500 mb-4" id="cancelModalSubtext">Nomor Antrean akan dikeluarkan dari antrean aktif FIFO.</p>

        <form id="cancelOrderForm" method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alasan Pembatalan:</label>
                <textarea name="cancel_reason" rows="2" required placeholder="Contoh: Pelanggan membatalkan pesanan" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" onclick="closeCancelModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition">
                    Ya, Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let lastQueueCount = {{ $fifoQueue->count() }};
    let lastReadyCount = {{ $readyOrders->count() }};

    function openCancelModal(orderId, queueNumber) {
        const form = document.getElementById('cancelOrderForm');
        form.action = `/pegawai/pesanan/${orderId}/batal`;
        document.getElementById('cancelModalSubtext').innerText = `Nomor Antrean #${queueNumber} akan dikeluarkan dari antrean aktif FIFO.`;
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Auto-sync antrean pegawai via JSON polling setiap 4 detik
    async function syncQueue() {
        try {
            const res = await fetch("{{ route('pegawai.queue_api') }}");
            if (!res.ok) return;

            const data = await res.json();
            if (data.success) {
                // Update Badge Sidebar
                const badge = document.getElementById('sidebarQueueBadge');
                if (badge) badge.innerText = data.queue_count;

                // Jika jumlah berubah, lakukan reload otomatis agar tampilan selalu up to date
                if (data.queue_count !== lastQueueCount || data.ready_count !== lastReadyCount) {
                    location.reload();
                }
            }
        } catch (e) {
            console.warn('Gagal sync live queue:', e);
        }
    }

    setInterval(syncQueue, 4000);
</script>
@endpush
