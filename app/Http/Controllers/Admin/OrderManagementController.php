<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    /**
     * B. Manajemen Pesanan: Melihat seluruh pesanan aktif
     */
    public function index(Request $request)
    {
        $query = Order::with(['details.menu', 'pegawai']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhere('queue_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->orderBy('received_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Detail Pesanan Lengkap dengan Timeline Status History & Log Notifikasi
     */
    public function show(Order $order)
    {
        $order->load(['details.menu', 'statusHistory', 'notifications', 'pegawai']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * B. Riwayat Pesanan: Melihat transaksi yang sudah selesai atau dibatalkan
     */
    public function history(Request $request)
    {
        $query = Order::with(['details.menu', 'pegawai'])
            ->whereIn('status', ['selesai', 'dibatalkan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', "%{$request->search}%")
                  ->orWhere('queue_number', 'like', "%{$request->search}%")
                  ->orWhere('customer_name', 'like', "%{$request->search}%");
            });
        }

        $orders = $query->orderBy('received_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.orders.history', compact('orders'));
    }
}

