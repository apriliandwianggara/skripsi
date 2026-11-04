<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\PesananItem;
use App\Models\User;
use App\Models\Meja;
use App\Services\FifoQueueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    protected FifoQueueService $fifoService;

    public function __construct(FifoQueueService $fifoService)
    {
        $this->fifoService = $fifoService;
    }

    /**
     * Dashboard Utama Admin/Owner
     */
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // 1. KPI Cards
        $pendapatanHariIni = Pesanan::whereDate('waktu_masuk', $today)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_akhir');

        $pendapatanBulanIni = Pesanan::where('waktu_masuk', '>=', $thisMonth)
            ->where('status_pembayaran', 'lunas')
            ->sum('total_akhir');

        $totalPesananHariIni = Pesanan::whereDate('waktu_masuk', $today)->count();
        $pesananAktif = Pesanan::whereIn('status_pesanan', ['menunggu', 'diproses'])->count();

        // Metrik FIFO
        $fifoMetrics = $this->fifoService->getQueuePerformanceMetrics();

        // 2. Data Grafik Penjualan 7 Hari Terakhir
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D, d M');
            $omzet = Pesanan::whereDate('waktu_masuk', $date)
                ->where('status_pembayaran', 'lunas')
                ->sum('total_akhir');
            $chartData[] = (float) $omzet;
        }

        // 3. Menu Terlaris (Top Selling)
        $topMenus = PesananItem::select('menu_id', 'nama_menu', DB::raw('SUM(jumlah) as total_terjual'), DB::raw('SUM(subtotal) as total_pendapatan'))
            ->groupBy('menu_id', 'nama_menu')
            ->orderBy('total_terjual', 'desc')
            ->take(5)
            ->get();

        // 4. Pesanan Terbaru
        $recentOrders = Pesanan::with(['meja', 'items'])
            ->orderBy('waktu_masuk', 'desc')
            ->take(8)
            ->get();

        // 5. Log Aktivitas Terbaru
        $recentLogs = AktivitasLog::orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $totalMeja = Meja::count();
        $totalMenu = Menu::count();

        return view('admin.dashboard', compact(
            'pendapatanHariIni',
            'pendapatanBulanIni',
            'totalPesananHariIni',
            'pesananAktif',
            'fifoMetrics',
            'chartLabels',
            'chartData',
            'topMenus',
            'recentOrders',
            'recentLogs',
            'totalMeja',
            'totalMenu'
        ));
    }

    /**
     * Laporan Penjualan dan Riwayat Transaksi
     */
    public function reports(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::today()->endOfDay();

        $query = Pesanan::with(['meja', 'items', 'promo', 'kasir'])
            ->whereBetween('waktu_masuk', [$startDate, $endDate]);

        if ($request->filled('status_pesanan')) {
            $query->where('status_pesanan', $request->status_pesanan);
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        $pesanans = $query->orderBy('waktu_masuk', 'desc')->paginate(20)->withQueryString();

        $totalPendapatan = (clone $query)->where('status_pembayaran', 'lunas')->sum('total_akhir');
        $totalItemTerjual = PesananItem::whereIn('pesanan_id', (clone $query)->pluck('id'))->sum('jumlah');
        $totalTransaksi = (clone $query)->count();

        return view('admin.reports.index', compact(
            'pesanans',
            'startDate',
            'endDate',
            'totalPendapatan',
            'totalItemTerjual',
            'totalTransaksi'
        ));
    }

    /**
     * Export / Print Friendly Report View
     */
    public function printReport(Request $request)
    {
        $startDate = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : Carbon::today()->startOfMonth();
        $endDate = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : Carbon::today()->endOfDay();

        $query = Pesanan::with(['meja', 'items', 'kasir'])
            ->whereBetween('waktu_masuk', [$startDate, $endDate]);

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $pesanans = $query->orderBy('waktu_masuk', 'asc')->get();
        $totalPendapatan = $pesanans->where('status_pembayaran', 'lunas')->sum('total_akhir');

        return view('admin.reports.print', compact('pesanans', 'startDate', 'endDate', 'totalPendapatan'));
    }
}
