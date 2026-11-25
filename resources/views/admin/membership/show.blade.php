@extends('layouts.admin')

@section('title', 'Detail Member: ' . $member->name . ' - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header & Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.membership.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight font-serif">
                    Profil Member & Riwayat Pesanan
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">Informasi akumulasi transaksi dan klaim reward loyalty pelanggan.</p>
            </div>
        </div>
    </div>

    <!-- Profil Card -->
    <div class="bg-white rounded-2xl border border-gray-150 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-emerald-700 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-emerald-700/20">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <h2 class="text-xl font-bold text-gray-900">{{ $member->name }}</h2>
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 font-bold text-[10px] rounded-full uppercase tracking-wider">Member Aktif</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs text-gray-500">
                        <span class="font-mono text-gray-700 font-semibold flex items-center gap-1">
                            <i class="fa-solid fa-phone text-emerald-600"></i> {{ $member->phone }}
                        </span>
                        @if($member->phone)
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $member->phone) }}" target="_blank" class="text-emerald-700 font-bold hover:underline flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Metrik Loyalitas -->
            <div class="grid grid-cols-3 gap-3 border-t md:border-t-0 md:border-l border-gray-150 pt-4 md:pt-0 md:pl-6 text-center">
                <div class="p-3 bg-gray-50 rounded-xl">
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Total Pesanan</p>
                    <p class="text-lg font-black text-gray-900 mt-0.5">{{ $member->total_orders }}x</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200">
                    <p class="text-[10px] font-bold text-amber-800 uppercase">Posisi Siklus</p>
                    <p class="text-lg font-black text-amber-700 mt-0.5">{{ $member->total_orders % 10 }} / 10</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl">
                    <p class="text-[10px] font-bold text-purple-700 uppercase">Hadiah Klaim</p>
                    <p class="text-lg font-black text-purple-900 mt-0.5">{{ $member->rewards_claimed }}x</p>
                </div>
            </div>
        </div>

        <!-- Progress Bar Status Hadiah -->
        @php
            $cycle = $member->total_orders % 10;
            $percent = $cycle * 10;
            $isEligible = ($cycle === 9);
        @endphp
        <div class="mt-6 pt-6 border-t border-gray-100 space-y-2">
            <div class="flex justify-between items-center text-xs">
                <span class="font-bold text-gray-700">Progres Menuju Menu Hadiah Gratis:</span>
                @if($isEligible)
                    <span class="font-bold text-amber-700 bg-amber-100 px-3 py-1 rounded-full text-xs animate-pulse">
                        <i class="fa-solid fa-gift mr-1"></i> Pesanan berikutnya (Ke-10) BERHAK Menu Gratis!
                    </span>
                @else
                    <span class="text-gray-500 font-semibold">{{ $cycle }} dari 10 pesanan (Kurang {{ 10 - $cycle }} pesanan)</span>
                @endif
            </div>
            <div class="w-full bg-gray-150 h-3 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 {{ $isEligible ? 'bg-amber-500 animate-pulse' : 'bg-emerald-600' }}" style="width: {{ $percent }}%"></div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-gray-800 font-serif flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-emerald-700"></i>
                <span>Riwayat Semua Pesanan ({{ $orders->total() }})</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-150 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4">No. Pesanan</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Meja</th>
                        <th class="py-3 px-4">Menu Dipesan</th>
                        <th class="py-3 px-4 text-center">Hadiah Member</th>
                        <th class="py-3 px-4 text-right">Total Bayar</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $o)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-gray-900">
                                <a href="{{ route('admin.orders.show', $o->id) }}" class="text-emerald-700 hover:underline">
                                    {{ $o->order_number }}
                                </a>
                                <span class="block text-[10px] text-gray-400 font-normal">Antrean #{{ $o->queue_number }}</span>
                            </td>

                            <td class="py-3.5 px-4 text-gray-600">
                                <p class="font-medium">{{ $o->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $o->created_at->diffForHumans() }}</p>
                            </td>

                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-semibold">
                                    {{ $o->table_number ? 'Meja ' . $o->table_number : 'Takeaway' }}
                                </span>
                            </td>

                            <td class="py-3.5 px-4">
                                <ul class="space-y-0.5 text-[11px] text-gray-700">
                                    @foreach($o->details as $d)
                                        <li><b>{{ $d->quantity }}x</b> {{ $d->menu_name }}</li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                @if($o->is_member_reward_order)
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <i class="fa-solid fa-gift"></i>
                                        <span>{{ $o->reward_menu_name ?? 'Menu Gratis' }}</span>
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>

                            <td class="py-3.5 px-4 text-right font-mono font-bold text-gray-900">
                                Rp {{ number_format($o->total_price, 0, ',', '.') }}
                            </td>

                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $o->payment_status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ strtoupper($o->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-400 text-xs">
                                Belum ada riwayat pesanan tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
