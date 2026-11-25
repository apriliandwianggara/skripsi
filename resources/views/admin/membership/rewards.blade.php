@extends('layouts.admin')

@section('title', 'Atur Menu Hadiah Member Gratis - ' . ($setting->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header & Back Button -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.membership.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-50 flex items-center justify-center transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight font-serif flex items-center gap-2.5">
                    <i class="fa-solid fa-gift text-emerald-700"></i>
                    <span>Pilihan Menu Hadiah Gratis (Pesanan ke-10)</span>
                </h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Tentukan menu mana saja yang boleh dipilih secara GRATIS oleh member saat melakukan pemesanan ke-10.
                </p>
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 text-amber-900 px-4 py-2 rounded-xl text-xs flex items-center gap-2 font-medium">
            <i class="fa-solid fa-circle-info text-amber-600"></i>
            <span>Saat ini ada <strong id="activeRewardCount">{{ $rewardMenus->count() }}</strong> menu aktif sebagai hadiah.</span>
        </div>
    </div>

    <!-- Daftar Kategori & Menu -->
    <div class="space-y-6">
        @foreach($categories as $category)
            <div class="bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
                <div class="bg-gray-50/80 px-5 py-3 border-b border-gray-150 flex items-center justify-between">
                    <h3 class="font-bold text-sm text-gray-900 font-serif flex items-center gap-2">
                        <i class="fa-solid fa-folder text-emerald-600"></i>
                        <span>{{ $category->name }}</span>
                    </h3>
                    <span class="text-[11px] text-gray-500 font-medium">{{ $category->menus->count() }} Menu</span>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    @forelse($category->menus as $menu)
                        <div class="p-3 rounded-xl border {{ $menu->is_member_reward ? 'border-amber-300 bg-amber-50/30' : 'border-gray-200 bg-white' }} flex items-center justify-between transition hover:shadow-sm" id="card-menu-{{ $menu->id }}">
                            <div class="flex items-center space-x-3 min-w-0 pr-2">
                                <img src="{{ $menu->image ? asset('storage/' . $menu->image) : asset('images/menu-placeholder.svg') }}"
                                     alt="{{ $menu->name }}" class="w-12 h-12 rounded-lg object-cover bg-gray-100 shrink-0"
                                     onerror="this.onerror=null; this.src='/images/menu-placeholder.svg';">
                                <div class="min-w-0">
                                    <h4 class="font-bold text-xs text-gray-900 truncate">{{ $menu->name }}</h4>
                                    <p class="text-[11px] font-mono text-gray-500">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                    <span class="inline-block mt-0.5 text-[9px] font-bold px-2 py-0.2 rounded-full {{ $menu->is_member_reward ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-400' }}" id="badge-menu-{{ $menu->id }}">
                                        {{ $menu->is_member_reward ? '★ Menu Hadiah' : 'Bukan Hadiah' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Switch / Toggle -->
                            <form action="{{ route('admin.membership.rewards.toggle', $menu->id) }}" method="POST" onsubmit="handleToggleReward(event, {{ $menu->id }})">
                                @csrf
                                <button type="submit" id="btn-toggle-{{ $menu->id }}"
                                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 {{ $menu->is_member_reward ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm' : 'bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-emerald-700 border border-gray-200' }}">
                                    <i class="fa-solid {{ $menu->is_member_reward ? 'fa-check' : 'fa-plus' }}"></i>
                                    <span>{{ $menu->is_member_reward ? 'Aktif' : 'Jadikan Hadiah' }}</span>
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="col-span-full py-4 text-center text-xs text-gray-400">
                            Tidak ada menu dalam kategori ini.
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

</div>

@push('scripts')
<script>
    async function handleToggleReward(e, menuId) {
        e.preventDefault();
        const form = e.target;
        const btn = document.getElementById('btn-toggle-menu-' + menuId) || form.querySelector('button');
        const card = document.getElementById('card-menu-' + menuId);
        const badge = document.getElementById('badge-menu-' + menuId);
        const countEl = document.getElementById('activeRewardCount');

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await res.json();

            if (data.success) {
                if (data.is_member_reward) {
                    btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-amber-500 hover:bg-amber-600 text-white shadow-sm';
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Aktif</span>';
                    if (card) {
                        card.className = 'p-3 rounded-xl border border-amber-300 bg-amber-50/30 flex items-center justify-between transition hover:shadow-sm';
                    }
                    if (badge) {
                        badge.className = 'inline-block mt-0.5 text-[9px] font-bold px-2 py-0.2 rounded-full bg-amber-100 text-amber-800';
                        badge.innerText = '★ Menu Hadiah';
                    }
                } else {
                    btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 bg-gray-100 hover:bg-emerald-50 text-gray-600 hover:text-emerald-700 border border-gray-200';
                    btn.innerHTML = '<i class="fa-solid fa-plus"></i> <span>Jadikan Hadiah</span>';
                    if (card) {
                        card.className = 'p-3 rounded-xl border border-gray-200 bg-white flex items-center justify-between transition hover:shadow-sm';
                    }
                    if (badge) {
                        badge.className = 'inline-block mt-0.5 text-[9px] font-bold px-2 py-0.2 rounded-full bg-gray-100 text-gray-400';
                        badge.innerText = 'Bukan Hadiah';
                    }
                }

                // Update count
                if (countEl) {
                    const current = parseInt(countEl.innerText) || 0;
                    countEl.innerText = data.is_member_reward ? (current + 1) : Math.max(0, current - 1);
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kendala saat mengubah status menu hadiah.'
            });
            btn.innerHTML = originalHtml;
        } finally {
            btn.disabled = false;
        }
    }
</script>
@endpush
@endsection
