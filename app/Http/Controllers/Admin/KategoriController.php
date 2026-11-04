<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('menus')->orderBy('urutan')->get();
        return view('admin.kategoris.index', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategoris,nama',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $kategori = Kategori::create([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'icon' => $request->icon ?: 'coffee',
            'urutan' => Kategori::max('urutan') + 1,
            'is_active' => true,
        ]);

        AktivitasLog::catat(
            'TAMBAH_KATEGORI',
            "Menambahkan kategori menu baru: [{$kategori->nama}]"
        );

        return back()->with('success', "Kategori '{$kategori->nama}' berhasil dibuat.");
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:kategoris,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'slug' => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'icon' => $request->icon ?: 'coffee',
            'is_active' => $request->boolean('is_active'),
        ]);

        AktivitasLog::catat(
            'UBAH_KATEGORI',
            "Mengubah kategori menu: [{$kategori->nama}]"
        );

        return back()->with('success', "Kategori '{$kategori->nama}' berhasil diperbarui.");
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->menus()->count() > 0) {
            return back()->with('error', "Kategori '{$kategori->nama}' tidak dapat dihapus karena masih memiliki menu terkait.");
        }

        $nama = $kategori->nama;
        $kategori->delete();

        AktivitasLog::catat(
            'HAPUS_KATEGORI',
            "Menghapus kategori menu: [{$nama}]"
        );

        return back()->with('success', "Kategori '{$nama}' berhasil dihapus.");
    }
}
