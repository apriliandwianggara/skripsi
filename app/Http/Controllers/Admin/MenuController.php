<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Kategori;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('kategori');

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status_stok')) {
            $query->where('status_stok', $request->status_stok);
        }

        if ($request->filled('search')) {
            $query->where('nama', 'like', "%{$request->search}%");
        }

        $menus = $query->orderBy('kategori_id')->orderBy('urutan')->paginate(15)->withQueryString();
        $kategoris = Kategori::where('is_active', true)->orderBy('nama')->get();

        return view('admin.menus.index', compact('menus', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::where('is_active', true)->orderBy('nama')->get();
        return view('admin.menus.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:150|unique:menus,nama',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'harga_promo' => 'nullable|numeric|min:0|lt:harga',
            'status_stok' => 'required|in:tersedia,habis',
            'is_rekomendasi' => 'nullable|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $slug = Str::slug($request->nama);
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('menus', 'public');
        }

        $menu = Menu::create([
            'kategori_id' => $request->kategori_id,
            'nama' => $request->nama,
            'slug' => $slug,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'harga_promo' => $request->harga_promo ?: null,
            'status_stok' => $request->status_stok,
            'is_rekomendasi' => $request->boolean('is_rekomendasi'),
            'foto' => $fotoPath,
            'urutan' => Menu::max('urutan') + 1,
        ]);

        AktivitasLog::catat(
            'TAMBAH_MENU',
            "Menambahkan menu baru: [{$menu->nama}] dengan harga Rp " . number_format($menu->harga, 0, ',', '.')
        );

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$menu->nama}' berhasil ditambahkan.");
    }

    public function edit(Menu $menu)
    {
        $kategoris = Kategori::where('is_active', true)->orderBy('nama')->get();
        return view('admin.menus.edit', compact('menu', 'kategoris'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'nama' => 'required|string|max:150|unique:menus,nama,' . $menu->id,
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'harga_promo' => 'nullable|numeric|min:0|lt:harga',
            'status_stok' => 'required|in:tersedia,habis',
            'is_rekomendasi' => 'nullable|boolean',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $oldHarga = $menu->harga;
        $oldPromo = $menu->harga_promo;

        $slug = Str::slug($request->nama);
        $fotoPath = $menu->foto;

        if ($request->hasFile('foto')) {
            if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
                Storage::disk('public')->delete($menu->foto);
            }
            $fotoPath = $request->file('foto')->store('menus', 'public');
        }

        $menu->update([
            'kategori_id' => $request->kategori_id,
            'nama' => $request->nama,
            'slug' => $slug,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'harga_promo' => $request->harga_promo ?: null,
            'status_stok' => $request->status_stok,
            'is_rekomendasi' => $request->boolean('is_rekomendasi'),
            'foto' => $fotoPath,
        ]);

        $changes = [];
        if ((float) $oldHarga !== (float) $request->harga) {
            $changes[] = "harga diubah dari Rp " . number_format($oldHarga, 0, ',', '.') . " menjadi Rp " . number_format($request->harga, 0, ',', '.');
        }
        if ((float) $oldPromo !== (float) $request->harga_promo) {
            $changes[] = "harga promo diubah";
        }

        $changeDesc = !empty($changes) ? implode(', ', $changes) : "data menu diperbarui";

        AktivitasLog::catat(
            'UBAH_MENU',
            "Mengubah menu [{$menu->nama}]: {$changeDesc}"
        );

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$menu->nama}' berhasil diperbarui.");
    }

    public function destroy(Menu $menu)
    {
        $nama = $menu->nama;
        if ($menu->foto && Storage::disk('public')->exists($menu->foto)) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        AktivitasLog::catat(
            'HAPUS_MENU',
            "Menghapus menu [{$nama}] dari sistem."
        );

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$nama}' berhasil dihapus.");
    }

    public function toggleStock(Menu $menu)
    {
        $newStatus = $menu->status_stok === 'tersedia' ? 'habis' : 'tersedia';
        $menu->update(['status_stok' => $newStatus]);

        AktivitasLog::catat(
            'UBAH_STOK',
            "Mengubah ketersediaan stok menu [{$menu->nama}] menjadi: " . strtoupper($newStatus)
        );

        return back()->with('success', "Status stok {$menu->nama} diubah menjadi {$newStatus}.");
    }
}
