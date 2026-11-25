<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Member;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * Tampilan Utama Manajemen Membership Pelanggan
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter'); // 'all', 'eligible_reward', 'claimed'

        $query = Member::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($filter === 'eligible_reward') {
            $query->whereRaw('(total_orders % 10) = 9');
        } elseif ($filter === 'claimed') {
            $query->where('rewards_claimed', '>', 0);
        }

        $members = $query->orderByDesc('total_orders')->orderByDesc('updated_at')->paginate(15)->withQueryString();

        // Statistik Ringkasan
        $totalMembers = Member::count();
        $totalOrdersMember = Member::sum('total_orders');
        $totalRewardsClaimed = Member::sum('rewards_claimed');
        $totalEligibleReward = Member::whereRaw('(total_orders % 10) = 9')->count();

        // Jumlah Menu Hadiah yang Aktif
        $totalActiveRewards = Menu::where('is_member_reward', true)->where('is_available', true)->count();

        return view('admin.membership.index', compact(
            'members',
            'search',
            'filter',
            'totalMembers',
            'totalOrdersMember',
            'totalRewardsClaimed',
            'totalEligibleReward',
            'totalActiveRewards'
        ));
    }

    /**
     * Detail Member & Riwayat Transaksi Pesanan
     */
    public function show($id)
    {
        $member = Member::findOrFail($id);

        $orders = Order::with(['details', 'rewardMenu'])
            ->where('customer_phone', $member->phone)
            ->latest('id')
            ->paginate(10);

        return view('admin.membership.show', compact('member', 'orders'));
    }

    /**
     * Halaman Pengaturan Menu Hadiah Gratis untuk Member (Pesanan ke-10)
     */
    public function rewards()
    {
        $categories = Category::with(['menus' => function ($q) {
            $q->orderBy('order')->orderBy('name');
        }])->orderBy('order')->get();

        $rewardMenus = Menu::where('is_member_reward', true)->orderBy('name')->get();

        return view('admin.membership.rewards', compact('categories', 'rewardMenus'));
    }

    /**
     * Toggle status menu sebagai reward gratis member
     */
    public function toggleReward(Request $request, Menu $menu)
    {
        $menu->is_member_reward = !$menu->is_member_reward;
        $menu->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_member_reward' => $menu->is_member_reward,
                'message' => $menu->is_member_reward
                    ? "Menu '{$menu->name}' sekarang aktif sebagai Hadiah Member Gratis!"
                    : "Menu '{$menu->name}' dinonaktifkan dari Hadiah Member."
            ]);
        }

        return back()->with('success', $menu->is_member_reward
            ? "Menu '{$menu->name}' berhasil ditambahkan ke pilihan Hadiah Member Gratis."
            : "Menu '{$menu->name}' dihapus dari pilihan Hadiah Member.");
    }
}
