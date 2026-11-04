<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Promo;
use Illuminate\Http\Request;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::withCount('pesanans')->orderBy('created_at', 'desc')->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_promo' => 'required|string|max:50|unique:promos,kode_promo',
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:persen,nominal',
            'nilai_potongan' => 'required|numeric|min:1',
            'min_pembelian' => 'required|numeric|min:0',
            'maksimal_potongan' => 'nullable|numeric|min:0',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
        ]);

        $promo = Promo::create([
            'kode_promo' => strtoupper(trim($request->kode_promo)),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tipe' => $request->tipe,
            'nilai_potongan' => $request->nilai_potongan,
            'min_pembelian' => $request->min_pembelian,
            'maksimal_potongan' => $request->maksimal_potongan ?: null,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'is_active' => true,
        ]);

        AktivitasLog::catat(
            'TAMBAH_PROMO',
            "Membuat kode promo baru: [{$promo->kode_promo}] - {$promo->judul}"
        );

        return back()->with('success', "Kode Promo '{$promo->kode_promo}' berhasil dibuat.");
    }

    public function update(Request $request, Promo $promo)
    {
        $request->validate([
            'kode_promo' => 'required|string|max:50|unique:promos,kode_promo,' . $promo->id,
            'judul' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'tipe' => 'required|in:persen,nominal',
            'nilai_potongan' => 'required|numeric|min:1',
            'min_pembelian' => 'required|numeric|min:0',
            'maksimal_potongan' => 'nullable|numeric|min:0',
            'tgl_mulai' => 'nullable|date',
            'tgl_selesai' => 'nullable|date|after_or_equal:tgl_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $promo->update([
            'kode_promo' => strtoupper(trim($request->kode_promo)),
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tipe' => $request->tipe,
            'nilai_potongan' => $request->nilai_potongan,
            'min_pembelian' => $request->min_pembelian,
            'maksimal_potongan' => $request->maksimal_potongan ?: null,
            'tgl_mulai' => $request->tgl_mulai,
            'tgl_selesai' => $request->tgl_selesai,
            'is_active' => $request->boolean('is_active'),
        ]);

        AktivitasLog::catat(
            'UBAH_PROMO',
            "Mengubah data kode promo: [{$promo->kode_promo}]"
        );

        return back()->with('success', "Promo '{$promo->kode_promo}' berhasil diperbarui.");
    }

    public function destroy(Promo $promo)
    {
        $kode = $promo->kode_promo;
        $promo->delete();

        AktivitasLog::catat(
            'HAPUS_PROMO',
            "Menghapus promo [{$kode}] dari sistem."
        );

        return back()->with('success', "Promo '{$kode}' berhasil dihapus.");
    }
}
