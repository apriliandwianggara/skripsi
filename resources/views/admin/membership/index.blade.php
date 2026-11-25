@extends('layouts.admin')

@section('title', 'Membership Pelanggan - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight font-serif flex items-center gap-2.5">
                <i class="fa-solid fa-id-card-clip text-emerald-700"></i>
                <span>Data Membership Pelanggan</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Data nomor HP pelanggan dan akumulasi berapa kali sudah memesan di Sekarsa Coffee.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.membership.rewards') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl shadow-sm text-xs font-semibold transition">
                <i class="fa-solid fa-gift"></i>
                <span>Atur Menu Hadiah Gratis ({{ $totalActiveRewards }})</span>
            </a>
        </div>
    </div>

    <!-- Kotak Pencarian Khusus Nomor HP -->
    <div class="bg-white rounded-2xl border-2 border-emerald-600/30 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.membership.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
            <div class="relative flex-1">
                <input type="text"
                       name="phone_search"
                       value="{{ request('phone_search') ?? request('search') }}"
                       placeholder="Ketik Nomor HP Pelanggan (contoh: 0812...)"
                       class="w-full pl-10 pr-4 py-3 text-sm rounded-xl border-2 border-gray-300 focus:border-emerald-600 focus:outline-none font-mono">
                <i class="fa-solid fa-phone absolute left-3.5 top-4 text-emerald-700 text-sm"></i>
            </div>
            <button type="submit" class="px-6 py-3 bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white text-sm font-bold rounded-xl transition shadow-md flex items-center justify-center space-x-2 shrink-0 cursor-pointer">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span>Cari No. HP</span>
            </button>
            @if(request('phone_search') || request('search'))
                <a href="{{ route('admin.membership.index') }}" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition flex items-center justify-center shrink-0">
                    Reset
                </a>
            @endif
        </form>

        @if(request('phone_search') || request('search'))
            <p class="text-xs text-emerald-800 font-semibold mt-2.5">
                <i class="fa-solid fa-filter mr-1"></i> Menampilkan hasil pencarian untuk: "<strong>{{ request('phone_search') ?? request('search') }}</strong>" (Ditemukan: {{ $members->total() }} member)
            </p>
        @endif
    </div>

    <!-- Ringkasan Statistik Singkat -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase">Total Member</p>
            <h3 class="text-lg font-black text-gray-900 mt-0.5">{{ number_format($totalMembers) }}</h3>
        </div>
        <div class="bg-white p-3.5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase">Total Pesanan Member</p>
            <h3 class="text-lg font-black text-gray-900 mt-0.5">{{ number_format($totalOrdersMember) }}</h3>
        </div>
        <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200 shadow-sm">
            <p class="text-[10px] font-bold text-amber-800 uppercase">Siap Menu Gratis (9/10)</p>
            <h3 class="text-lg font-black text-amber-700 mt-0.5">{{ number_format($totalEligibleReward) }}</h3>
        </div>
        <div class="bg-purple-50 p-3.5 rounded-xl border border-purple-200 shadow-sm">
            <p class="text-[10px] font-bold text-purple-700 uppercase">Total Hadiah Diklaim</p>
            <h3 class="text-lg font-black text-purple-900 mt-0.5">{{ number_format($totalRewardsClaimed) }}</h3>
        </div>
    </div>

    <!-- Tabel Daftar Member (Langsung No HP & Berapa Kali Pesan) -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-[11px] font-bold text-gray-600 uppercase tracking-wider">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nomor HP Pelanggan</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4 text-center">Jumlah Pesan</th>
                        <th class="py-3 px-4">Status Hadiah Member</th>
                        <th class="py-3 px-4">Terakhir Pesan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($members as $index => $m)
                        @php
                            $cycle = $m->total_orders % 10;
                            $isEligible = ($cycle === 9);
                        @endphp
                        <tr class="hover:bg-slate-50 transition {{ $isEligible ? 'bg-amber-50/30' : '' }}">
                            <td class="py-3.5 px-4 text-center font-mono text-gray-400">
                                {{ $members->firstItem() + $index }}
                            </td>

                            <!-- Nomor HP -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-2">
                                    <span class="font-mono text-sm font-bold text-emerald-900 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        {{ $m->phone }}
                                    </span>
                                    @if($m->phone)
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $m->phone) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 text-sm" title="Chat WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <!-- Nama Pelanggan -->
                            <td class="py-3.5 px-4 font-bold text-gray-800 text-sm">
                                {{ $m->name }}
                            </td>

                            <!-- Jumlah Pesan (Langsung Angka Jelas: contoh 2 kali) -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-900 rounded-xl font-black font-mono text-base border border-gray-200">
                                    {{ $m->total_orders }}
                                </span>
                                <span class="block text-[10px] text-gray-500 font-semibold mt-0.5">kali pesan</span>
                            </td>

                            <!-- Status Hadiah -->
                            <td class="py-3.5 px-4">
                                @if($isEligible)
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-lg font-bold text-xs">
                                        <i class="fa-solid fa-gift text-amber-600"></i>
                                        <span>Pesanan ke-10 Gratis!</span>
                                    </span>
                                @elseif($m->rewards_claimed > 0)
                                    <span class="text-xs text-purple-700 font-bold">
                                        {{ $m->rewards_claimed }}x klaim gratis <span class="text-gray-400 font-normal">(Siklus ke-{{ $cycle }}/10)</span>
                                    </span>
                                @else
                                    <span class="text-xs text-gray-600">
                                        Siklus: <b>{{ $cycle }}</b> / 10 <span class="text-gray-400">(Kurang {{ 10 - $cycle }}x lagi)</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Terakhir Pesan -->
                            <td class="py-3.5 px-4 text-gray-500">
                                @if($m->last_ordered_at)
                                    <p class="text-xs font-semibold text-gray-700">{{ $m->last_ordered_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $m->last_ordered_at->diffForHumans() }}</p>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.membership.show', $m->id) }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold rounded-xl text-xs transition border border-emerald-200">
                                    <i class="fa-regular fa-eye"></i>
                                    <span>Riwayat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center mx-auto mb-2 text-xl text-gray-400">
                                    <i class="fa-solid fa-phone-slash"></i>
                                </div>
                                <p class="font-bold text-gray-700">Data Tidak Ditemukan</p>
                                <p class="text-xs text-gray-400 mt-1">Tidak ada nomor HP yang cocok dengan pencarian Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $members->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
