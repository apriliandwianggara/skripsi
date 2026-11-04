<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use Illuminate\Http\Request;

class AktivitasLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AktivitasLog::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('aksi', 'like', "%{$request->search}%")
                    ->orWhere('deskripsi', 'like', "%{$request->search}%")
                    ->orWhere('user_name', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        $aksiList = AktivitasLog::select('aksi')->distinct()->pluck('aksi');

        return view('admin.logs.index', compact('logs', 'aksiList'));
    }
}
