<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Services\FifoQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected FifoQueueService $fifoService;

    public function __construct(FifoQueueService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

    /**
     * B. Dashboard Admin: Ringkasan Jumlah Pesanan dan Statusnya
     */
    public function index()
    {
        $today = Carbon::today();

        // 1. Ringkasan Status Pesanan Hari Ini
        $totalOrdersToday = Order::whereDate('received_at', $today)->count();
        $waitingCount = Order::whereDate('received_at', $today)->where('status', 'menunggu')->count();
        $processingCount = Order::whereDate('received_at', $today)->where('status', 'diproses')->count();
        $readyCount = Order::whereDate('received_at', $today)->where('status', 'siap_diambil')->count();
        $completedCount = Order::whereDate('received_at', $today)->where('status', 'selesai')->count();
        $cancelledCount = Order::whereDate('received_at', $today)->where('status', 'dibatalkan')->count();

        $totalRevenueToday = Order::whereDate('received_at', $today)
            ->whereIn('status', ['siap_diambil', 'selesai'])
            ->sum('total_price');

        // Metrik Algoritma FIFO
        $fifoMetrics = $this->fifoService->getFifoMetrics();

        // 2. Data Grafik Penjualan 7 Hari Terakhir
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D, d M');
            $omzet = Order::whereDate('received_at', $date)
                ->whereIn('status', ['siap_diambil', 'selesai'])
                ->sum('total_price');
            $chartData[] = (float) $omzet;
        }

        // 3. Menu Terlaris (Top Selling)
        $topMenus = OrderDetail::select('menu_id', 'menu_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->groupBy('menu_id', 'menu_name')
            ->orderBy('total_qty', 'desc')
            ->take(5)
            ->get();

        // 4. Pesanan Terbaru
        $recentOrders = Order::with('details')
            ->orderBy('received_at', 'desc')
            ->take(8)
            ->get();

        $totalMenu = Menu::count();
        $totalCategory = Category::count();
        $totalUser = User::count();

        return view('admin.dashboard', compact(
            'totalOrdersToday',
            'waitingCount',
            'processingCount',
            'readyCount',
            'completedCount',
            'cancelledCount',
            'totalRevenueToday',
            'fifoMetrics',
            'chartLabels',
            'chartData',
            'topMenus',
            'recentOrders',
            'totalMenu',
            'totalCategory',
            'totalUser'
        ));
    }

    /**
     * Endpoint API Live Stats Real-Time untuk Owner / Admin (Tanpa Reload)
     */
    public function liveStatsApi()
    {
        $today = Carbon::today();

        $totalOrdersToday = Order::whereDate('received_at', $today)->count();
        $waitingCount = Order::whereDate('received_at', $today)->where('status', 'menunggu')->count();
        $processingCount = Order::whereDate('received_at', $today)->where('status', 'diproses')->count();
        $readyCount = Order::whereDate('received_at', $today)->where('status', 'siap_diambil')->count();
        $completedCount = Order::whereDate('received_at', $today)->where('status', 'selesai')->count();
        $cancelledCount = Order::whereDate('received_at', $today)->where('status', 'dibatalkan')->count();

        $totalRevenueToday = Order::whereDate('received_at', $today)
            ->whereIn('status', ['siap_diambil', 'selesai'])
            ->sum('total_price');

        $recentOrders = Order::with('details', 'pegawai')
            ->orderBy('received_at', 'desc')
            ->take(8)
            ->get();

        $latestUpdate = Order::max('updated_at') ?? '0';

        return response()->json([
            'success' => true,
            'version' => strtotime($latestUpdate),
            'kpi' => [
                'total_orders' => $totalOrdersToday,
                'waiting' => $waitingCount,
                'processing' => $processingCount,
                'ready' => $readyCount,
                'completed' => $completedCount,
                'cancelled' => $cancelledCount,
                'revenue_raw' => (float) $totalRevenueToday,
                'revenue_formatted' => 'Rp ' . number_format($totalRevenueToday, 0, ',', '.'),
            ],
            'recent_orders' => $recentOrders->map(function ($ord) {
                return [
                    'id' => $ord->id,
                    'queue_number' => $ord->queue_number,
                    'order_number' => $ord->order_number,
                    'customer_name' => $ord->customer_name,
                    'table_number' => $ord->table_number,
                    'received_at' => $ord->received_at->format('H:i:s'),
                    'total_items' => $ord->details->sum('quantity'),
                    'total_price' => 'Rp ' . number_format($ord->total_price, 0, ',', '.'),
                    'status' => $ord->status,
                    'status_label' => $ord->status_label,
                    'handler_name' => $ord->pegawai ? $ord->pegawai->name : '-',
                    'detail_url' => route('admin.orders.show', $ord),
                ];
            }),
        ]);
    }

    /**
     * B. Laporan: Rekap Pesanan Berdasarkan Tanggal
     */
    public function reports(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::today()->endOfDay();

        $query = Order::with(['details.menu', 'pegawai'])
            ->whereBetween('received_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('received_at', 'desc')->paginate(20)->withQueryString();

        $totalOmzet = (clone $query)->whereIn('status', ['siap_diambil', 'selesai'])->sum('total_price');
        $totalQuantity = OrderDetail::whereIn('order_id', (clone $query)->pluck('id'))->sum('quantity');
        $totalOrders = (clone $query)->count();

        return view('admin.reports.index', compact(
            'orders',
            'startDate',
            'endDate',
            'totalOmzet',
            'totalQuantity',
            'totalOrders'
        ));
    }

    /**
     * Cetak Lembar Rekapitulasi Laporan Resmi
     */
    public function printReport(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::today()->startOfMonth();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::today()->endOfDay();

        $query = Order::with(['details.menu', 'pegawai'])
            ->whereBetween('received_at', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('received_at', 'asc')->get();
        $totalOmzet = $orders->whereIn('status', ['siap_diambil', 'selesai'])->sum('total_price');

        return view('admin.reports.print', compact('orders', 'startDate', 'endDate', 'totalOmzet'));
    }
}

