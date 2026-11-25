@extends('layouts.admin')

@section('title', 'Manajemen Membership Pelanggan - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight font-serif flex items-center gap-2.5">
                <i class="fa-solid fa-id-card-clip text-emerald-700"></i>
                <span>Program Membership Pelanggan</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Pantau akumulasi pesanan loyalitas pelanggan dan klaim menu gratis di pesanan ke-10.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.membership.rewards') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl shadow-sm text-xs font-semibold transition">
                <i class="fa-solid fa-gift"></i>
                <span>Atur Menu Hadiah Gratis ({{ $totalActiveRewards }})</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Member</p>
                <h3 class="text-xl font-black text-gray-900">{{ number_format($totalMembers) }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Pesanan</p>
                <h3 class="text-xl font-black text-gray-900">{{ number_format($totalOrdersMember) }}</h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-amber-200/80 bg-amber-50/30 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-gift animate-bounce"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Siap Dapat Gratis</p>
                <h3 class="text-xl font-black text-amber-700">{{ number_format($totalEligibleReward) }} <span class="text-xs font-normal text-amber-800">(9/10)</span></h3>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-sm flex items-center space-x-3.5">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-award"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Hadiah Diklaim</p>
                <h3 class="text-xl font-black text-gray-900">{{ number_format($totalRewardsClaimed) }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Tab Filter -->
        <div class="flex items-center space-x-1.5 overflow-x-auto text-xs pb-1 md:pb-0">
            <a href="{{ route('admin.membership.index', ['search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl font-semibold transition {{ empty($filter) || $filter === 'all' ? 'bg-emerald-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua Member
            </a>
            <a href="{{ route('admin.membership.index', ['filter' => 'eligible_reward', 'search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl font-semibold transition flex items-center space-x-1.5 {{ $filter === 'eligible_reward' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                <i class="fa-solid fa-gift"></i>
                <span>Siap Hadiah (9/10)</span>
            </a>
            <a href="{{ route('admin.membership.index', ['filter' => 'claimed', 'search' => $search]) }}"
               class="px-3.5 py-2 rounded-xl font-semibold transition {{ $filter === 'claimed' ? 'bg-purple-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Pernah Klaim Hadiah
            </a>
        </div>

        <!-- Form Cari -->
        <form method="GET" action="{{ route('admin.membership.index') }}" class="flex items-center gap-2">
            @if($filter)
                <input type="hidden" name="filter" value="{{ $filter }}">
            @endif
            <div class="relative w-full md:w-64">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau No. HP..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-gray-400 text-xs"></i>
            </div>
            <button type="submit" class="px-3.5 py-2 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-xl transition">
                Cari
            </button>
            @if($search || $filter)
                <a href="{{ route('admin.membership.index') }}" class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-xl transition" title="Reset Filter">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Member -->
    <div class="bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Member</th>
                        <th class="py-3 px-4">Kontak (HP)</th>
                        <th class="py-3 px-4 text-center">Total Pesanan</th>
                        <th class="py-3 px-4">Status Siklus (10 Pesanan)</th>
                        <th class="py-3 px-4 text-center">Hadiah Diklaim</th>
                        <th class="py-3 px-4">Terakhir Pesan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($members as $m)
                        @php
                            $cycle = $m->total_orders % 10;
                            $percent = $cycle * 10;
                            $isEligible = ($cycle === 9);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isEligible ? 'bg-amber-50/20' : '' }}">
                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $m->name }}</p>
                                        <p class="text-[10px] text-gray-400">ID: #{{ $m->id }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-1.5">
                                    <span class="font-mono text-gray-700 font-medium">{{ $m->phone }}</span>
                                    @if($m->phone)
                                        <a href="https://wa.me/{{ preg_replace('/^0/', '62', $m->phone) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 text-sm ml-1" title="Chat WhatsApp">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-block px-2.5 py-1 bg-gray-100 text-gray-800 rounded-lg font-bold font-mono text-xs">
                                    {{ $m->total_orders }}x
                                </span>
                            </td>

                            <td class="py-3.5 px-4 min-w-[200px]">
                                <div class="space-y-1">
                                    <div class="flex justify-between items-center text-[11px]">
                                        @if($isEligible)
                                            <span class="font-bold text-amber-700 flex items-center gap-1">
                                                <i class="fa-solid fa-gift"></i> Pesanan ke-10 berikutnya GRATIS!
                                            </span>
                                        @else
                                            <span class="text-gray-600 font-medium">Siklus: <b>{{ $cycle }}</b> / 10</span>
                                            <span class="text-gray-400 text-[10px]">Kurang {{ 10 - $cycle }}x</span>
                                        @endif
                                    </div>
                                    <div class="w-full bg-gray-150 h-2 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300 {{ $isEligible ? 'bg-amber-500 animate-pulse' : 'bg-emerald-600' }}" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($m->rewards_claimed > 0)
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800">
                                        <i class="fa-solid fa-award"></i>
                                        <span>{{ $m->rewards_claimed }}x Diklaim</span>
                                    </span>
                                @else
                                    <span class="text-gray-400 text-[11px]">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-gray-500">
                                @if($m->last_ordered_at)
                                    <p class="text-[11px] font-medium text-gray-700">{{ $m->last_ordered_at->format('d M Y, H:i') }}</p>
                                    <p class="text-[10px] text-gray-400">{{ $m->last_ordered_at->diffForHumans() }}</p>
                                @else
                                    <span class="text-gray-400 text-[11px]">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('admin.membership.show', $m->id) }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                                    <i class="fa-regular fa-eye"></i>
                                    <span>Riwayat</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-150 flex items-center justify-center mx-auto mb-3 text-2xl text-gray-300">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                                <p class="font-bold text-gray-700">Belum Ada Data Member</p>
                                <p class="text-xs text-gray-400 mt-1">Data member akan otomatis terdata saat pelanggan melakukan pemesanan dengan nomor HP.</p>
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
