@extends('layouts.admin')

@section('title', 'Pusat Operasional FIFO - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Top KPI Bar & Info FIFO -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                    <i class="fa-solid fa-list-ol text-emerald-600"></i>
                    <span>Pusat Operasional Antrean FIFO</span>
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ auth()->user()->name }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                Sistem Multi-Kasir Terintegrasi Real-Time. Pesanan diurutkan ketat berdasarkan waktu server (FIFO).
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                <span id="liveSyncStatus">Real-time Aktif</span>
            </span>
            <button onclick="fetchQueueData()" class="px-3 py-1.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-semibold hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-rotate-right mr-1"></i> Sinkronisasi
            </button>
        </div>
    </div>

    <!-- Banner Suasana Sekarsa Coffee -->
    <div class="relative rounded-2xl overflow-hidden shadow-sm border border-emerald-900/10 h-24 sm:h-28">
        <img src="{{ asset('images/sekarsa-cafe.jpg') }}" alt="Sekarsa Cafe" class="w-full h-full object-cover object-center filter brightness-75">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/90 via-emerald-950/60 to-transparent flex items-center p-4 sm:p-5 text-white space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-white p-1 shadow-md shrink-0 flex items-center justify-center border border-white/40">
                <img src="{{ asset('images/sekarsa-logo.jpg') }}" alt="Sekarsa Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-600 text-white shadow-sm inline-block mb-0.5">
                    Sekarsa Coffee & Space
                </span>
                <h2 class="text-sm sm:text-base font-bold font-serif leading-tight">Pos Barista & Kasir &bull; {{ auth()->user()->name }}</h2>
                <p class="text-[11px] text-emerald-200">Pesanan pelanggan diproses berurutan sesuai waktu masuk (FIFO).</p>
            </div>
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
                        Total Antrean: <span id="queueTotalBadge">{{ $fifoQueue->count() }}</span>
                    </span>
                </div>

                <!-- Container Antrean Dinamis (Diperbarui Real-time Tanpa Reload) -->
                <div id="fifoQueueContainer" class="divide-y divide-gray-100">
                    @forelse($fifoQueue as $index => $order)
                        <div class="p-5 hover:bg-slate-50/80 transition {{ $index === 0 ? 'bg-amber-50/30' : '' }}" id="order-card-{{ $order->id }}">
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
                                            @if($order->pegawai)
                                                <span class="text-gray-300">&bull;</span>
                                                <span class="text-blue-700 font-semibold"><i class="fa-solid fa-user-check text-blue-500"></i> {{ $order->pegawai->name }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <!-- Status Badge & Elapsed Time -->
                                <div class="flex sm:flex-col items-center sm:items-end justify-between text-right">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                        {{ $order->status === 'menunggu' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
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

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-1">
                                <div class="text-xs text-gray-500">
                                    Total: <strong class="text-emerald-900 font-bold text-sm">Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
                                </div>

                                <div class="flex items-center space-x-2">
                                    @if($order->status === 'menunggu')
                                        <button type="button" onclick="actionProcess({{ $order->id }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                                            <i class="fa-solid fa-play"></i>
                                            <span>Mulai Proses</span>
                                        </button>
                                    @elseif($order->status === 'diproses')
                                        <button type="button" onclick="actionReady({{ $order->id }})" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                                            <i class="fa-solid fa-bell"></i>
                                            <span>Pesanan Siap Diambil</span>
                                        </button>
                                    @endif

                                    <button type="button" onclick="openCancelModal({{ $order->id }}, '{{ $order->queue_number }}')" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold transition" title="Batalkan Pesanan">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-400">
                            <i class="fa-solid fa-mug-saucer text-5xl mb-3 text-gray-300"></i>
                            <p class="font-bold text-gray-600 text-sm">Tidak ada antrean aktif saat ini</p>
                            <p class="text-xs text-gray-400 mt-1">Pesanan baru yang dikirim pelanggan akan langsung muncul di sini secara berurutan.</p>
                        </div>
                    @endforelse
                </div>
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
                    <span id="readyTotalBadge" class="text-xs bg-emerald-800 text-emerald-100 font-bold px-2.5 py-0.5 rounded-full font-mono">
                        {{ $readyOrders->count() }}
                    </span>
                </div>

                <div id="readyOrdersContainer" class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                    @forelse($readyOrders as $ready)
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

                            <div class="flex items-center space-x-2 pt-1">
                                @if($ready->customer_phone)
                                    <a href="{{ route('pegawai.order.whatsapp', $ready) }}" target="_blank"
                                       class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                                        <i class="fa-brands fa-whatsapp text-sm"></i>
                                        <span>Hubungi WA</span>
                                    </a>
                                @endif

                                <button type="button" onclick="actionComplete({{ $ready->id }})" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                                    <i class="fa-solid fa-check"></i>
                                    <span>Selesai Diambil</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-400">
                            <i class="fa-solid fa-circle-check text-4xl mb-2 text-gray-300"></i>
                            <p class="font-semibold text-gray-600 text-xs">Belum ada pesanan siap diambil</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Pesanan yang telah siap akan tampil di sini untuk serah terima ke pelanggan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Card Riwayat Selesai Hari Ini Singkat -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                <h3 class="font-bold text-xs uppercase tracking-wider text-gray-500 mb-3">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i> Terakhir Selesai Hari Ini
                </h3>

                <div id="completedListContainer" class="space-y-2 text-xs">
                    @forelse($completedToday->take(5) as $comp)
                        <div class="flex items-center justify-between py-1 border-b border-gray-50">
                            <div>
                                <span class="font-mono font-bold text-emerald-800">#{{ $comp->queue_number }}</span>
                                <span class="text-gray-700 ml-1.5 font-medium">{{ $comp->customer_name }}</span>
                            </div>
                            <span class="text-gray-400 font-mono text-[11px]">{{ $comp->completed_at ? $comp->completed_at->format('H:i') : '' }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-3">Belum ada pesanan selesai hari ini</p>
                    @endforelse
                </div>
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
                <textarea name="reason" rows="2" required placeholder="Contoh: Pelanggan membatalkan pesanan" class="w-full text-xs p-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500"></textarea>
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
    let currentVersion = 0;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Action AJAX Process (Mulai Proses) Anti-Tabrakan
    async function actionProcess(orderId) {
        try {
            const res = await fetch(`/pegawai/pesanan/${orderId}/proses`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Diproses',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                fetchQueueData();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian Multi-Kasir',
                    text: data.message || 'Pesanan sudah diubah oleh kasir lain.',
                    confirmButtonColor: '#16a34a'
                });
                fetchQueueData();
            }
        } catch (e) {
            console.error('Error actionProcess:', e);
        }
    }

    // Action AJAX Ready (Siap Diambil)
    async function actionReady(orderId) {
        try {
            const res = await fetch(`/pegawai/pesanan/${orderId}/siap`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Siap!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });

                if (data.whatsapp_url) {
                    window.open(data.whatsapp_url, '_blank');
                }

                fetchQueueData();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian Multi-Kasir',
                    text: data.message || 'Status pesanan telah berubah di kasir lain.',
                    confirmButtonColor: '#16a34a'
                });
                fetchQueueData();
            }
        } catch (e) {
            console.error('Error actionReady:', e);
        }
    }

    // Action AJAX Complete (Selesai Diambil)
    async function actionComplete(orderId) {
        try {
            const res = await fetch(`/pegawai/pesanan/${orderId}/selesai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await res.json();

            if (res.ok && data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pesanan Selesai',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
                fetchQueueData();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: data.message || 'Pesanan sudah diselesaikan di kasir lain.',
                    confirmButtonColor: '#16a34a'
                });
                fetchQueueData();
            }
        } catch (e) {
            console.error('Error actionComplete:', e);
        }
    }

    function openCancelModal(orderId, queueNumber) {
        const form = document.getElementById('cancelOrderForm');
        form.action = `/pegawai/pesanan/${orderId}/batal`;
        document.getElementById('cancelModalSubtext').innerText = `Nomor Antrean #${queueNumber} akan dikeluarkan dari antrean aktif FIFO.`;
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    // Real-time Fetch Data Tanpa Reload Halaman (Zero-Flicker Multi-Kasir Sync)
    async function fetchQueueData() {
        try {
            const res = await fetch("{{ route('pegawai.queue_api') }}", {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;

            const data = await res.json();
            if (!data.success) return;

            // Update KPI Stats Cards
            document.getElementById('statMenunggu').innerText = data.metrics.total_menunggu;
            document.getElementById('statDiproses').innerText = data.metrics.total_diproses;
            document.getElementById('statSiapDiambil').innerText = data.ready_count;
            document.getElementById('statSelesai').innerText = data.metrics.total_selesai;
            document.getElementById('queueTotalBadge').innerText = data.queue_count;
            document.getElementById('readyTotalBadge').innerText = data.ready_count;

            const sidebarBadge = document.getElementById('sidebarQueueBadge');
            if (sidebarBadge) sidebarBadge.innerText = data.queue_count;

            // Render Ulang Antrean FIFO
            renderQueue(data.queue);

            // Render Ulang Meja Counter Siap Diambil
            renderReady(data.ready);

            // Render Ulang Riwayat Selesai
            renderCompleted(data.completed);

            currentVersion = data.version;

        } catch (e) {
            console.warn('Gagal sinkronisasi data real-time:', e);
        }
    }

    function renderQueue(queue) {
        const container = document.getElementById('fifoQueueContainer');
        if (!container) return;

        if (queue.length === 0) {
            container.innerHTML = `
                <div class="p-12 text-center text-gray-400">
                    <i class="fa-solid fa-mug-saucer text-5xl mb-3 text-gray-300"></i>
                    <p class="font-bold text-gray-600 text-sm">Tidak ada antrean aktif saat ini</p>
                    <p class="text-xs text-gray-400 mt-1">Pesanan baru yang dikirim pelanggan akan langsung muncul di sini secara berurutan.</p>
                </div>
            `;
            return;
        }

        let html = '';
        queue.forEach((order, index) => {
            const isFirst = index === 0;
            const rankBg = isFirst ? 'bg-amber-500 text-white shadow-amber-500/30' : 'bg-gray-100 text-gray-700';
            const cardBg = isFirst ? 'bg-amber-50/30' : '';
            const statusBadge = order.status === 'menunggu'
                ? 'bg-amber-100 text-amber-800 border border-amber-200'
                : 'bg-blue-100 text-blue-800 border border-blue-200';

            let itemsHtml = '';
            order.items.forEach(d => {
                itemsHtml += `
                    <div class="flex items-start justify-between text-xs">
                        <div class="pr-2">
                            <span class="font-bold text-emerald-700">${d.quantity}x</span>
                            <span class="font-semibold text-gray-800 ml-1">${d.menu_name}</span>
                            ${d.notes ? `<span class="text-gray-500 italic block text-[11px] ml-5">"${d.notes}"</span>` : ''}
                        </div>
                        <span class="text-gray-500 text-[11px] shrink-0 font-mono">Rp ${d.subtotal}</span>
                    </div>
                `;
            });

            const actionBtn = order.status === 'menunggu'
                ? `<button type="button" onclick="actionProcess(${order.id})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                        <i class="fa-solid fa-play"></i>
                        <span>Mulai Proses</span>
                   </button>`
                : `<button type="button" onclick="actionReady(${order.id})" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5">
                        <i class="fa-solid fa-bell"></i>
                        <span>Pesanan Siap Diambil</span>
                   </button>`;

            html += `
                <div class="p-5 hover:bg-slate-50/80 transition ${cardBg}" id="order-card-${order.id}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl ${rankBg} flex flex-col items-center justify-center font-bold shrink-0 shadow-sm">
                                <span class="text-[9px] uppercase leading-none ${isFirst ? 'text-amber-100' : 'text-gray-400'}">FIFO</span>
                                <span class="text-base font-mono leading-none mt-0.5">#${order.fifo_rank}</span>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-black text-xl text-emerald-900 font-mono tracking-wide">${order.queue_number}</span>
                                    <span class="text-xs font-mono text-gray-400">(${order.order_number})</span>
                                    ${order.table_number ? `<span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-bold">Meja ${order.table_number}</span>` : ''}
                                </div>
                                <p class="text-xs font-semibold text-gray-700 mt-0.5 flex items-center space-x-2">
                                    <span>${order.customer_name}</span>
                                    ${order.customer_phone ? `<span class="text-gray-300">&bull;</span><span class="text-gray-500 font-normal"><i class="fa-brands fa-whatsapp text-emerald-600"></i> ${order.customer_phone}</span>` : ''}
                                    ${order.handler_name ? `<span class="text-gray-300">&bull;</span><span class="text-blue-700 font-semibold"><i class="fa-solid fa-user-check text-blue-500"></i> ${order.handler_name}</span>` : ''}
                                </p>
                            </div>
                        </div>
                        <div class="flex sm:flex-col items-center sm:items-end justify-between text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold ${statusBadge}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                ${order.status_label}
                            </span>
                            <span class="text-[11px] text-gray-400 mt-1">
                                <i class="fa-regular fa-clock mr-1"></i> Masuk: ${order.received_at} (${order.received_ago})
                            </span>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-3 border border-gray-150 mb-3 space-y-1.5">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Daftar Minuman / Makanan:</p>
                        ${itemsHtml}
                        ${order.customer_notes ? `<div class="mt-2 pt-2 border-t border-gray-100 text-[11px] text-amber-800 bg-amber-50/60 p-2 rounded-lg"><strong>Catatan Khusus:</strong> ${order.customer_notes}</div>` : ''}
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div class="text-xs text-gray-500">
                            Total: <strong class="text-emerald-900 font-bold text-sm">Rp ${order.total_price}</strong>
                        </div>
                        <div class="flex items-center space-x-2">
                            ${actionBtn}
                            <button type="button" onclick="openCancelModal(${order.id}, '${order.queue_number}')" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold transition" title="Batalkan Pesanan">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function renderReady(ready) {
        const container = document.getElementById('readyOrdersContainer');
        if (!container) return;

        if (ready.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center text-gray-400">
                    <i class="fa-solid fa-circle-check text-4xl mb-2 text-gray-300"></i>
                    <p class="font-semibold text-gray-600 text-xs">Belum ada pesanan siap diambil</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Pesanan yang telah siap akan tampil di sini untuk serah terima ke pelanggan.</p>
                </div>
            `;
            return;
        }

        let html = '';
        ready.forEach(ord => {
            html += `
                <div class="p-4 bg-emerald-50/40 hover:bg-emerald-50 transition space-y-2.5">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-mono font-black text-2xl text-emerald-900">${ord.queue_number}</span>
                            <p class="text-xs font-bold text-gray-800">${ord.customer_name}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-semibold text-[11px] font-mono">
                                ${ord.ready_at}
                            </span>
                            ${ord.table_number ? `<p class="text-[11px] text-gray-500 font-semibold mt-0.5">Meja ${ord.table_number}</p>` : ''}
                        </div>
                    </div>

                    <div class="text-xs text-gray-600">
                        <span class="text-gray-400">${ord.total_qty} Item:</span>
                        <span class="font-medium truncate block">${ord.item_summary}</span>
                        ${ord.handler_name ? `<span class="text-[10px] text-emerald-700 block mt-0.5">Dibuat oleh: ${ord.handler_name}</span>` : ''}
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        ${ord.customer_phone ? `
                            <a href="/pegawai/pesanan/${ord.id}/whatsapp" target="_blank"
                               class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                                <span>Hubungi WA</span>
                            </a>
                        ` : ''}

                        <button type="button" onclick="actionComplete(${ord.id})" class="flex-1 py-2 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center justify-center space-x-1.5 shadow-sm">
                            <i class="fa-solid fa-check"></i>
                            <span>Selesai Diambil</span>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function renderCompleted(completed) {
        const container = document.getElementById('completedListContainer');
        if (!container) return;

        if (completed.length === 0) {
            container.innerHTML = `<p class="text-xs text-gray-400 text-center py-3">Belum ada pesanan selesai hari ini</p>`;
            return;
        }

        let html = '';
        completed.slice(0, 5).forEach(c => {
            html += `
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <div>
                        <span class="font-mono font-bold text-emerald-800">#${c.queue_number}</span>
                        <span class="text-gray-700 ml-1.5 font-medium">${c.customer_name}</span>
                    </div>
                    <span class="text-gray-400 font-mono text-[11px]">${c.completed_at}</span>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Auto Polling Real-time setiap 2.5 Detik (Tanpa Refresh Halaman)
    setInterval(fetchQueueData, 2500);
</script>
@endpush
