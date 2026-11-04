<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AktivitasLog;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturan = Pengaturan::get();
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_cafe' => 'required|string|max:100',
            'tagline' => 'nullable|string|max:200',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'instagram' => 'nullable|string|max:100',
            'pesan_struk' => 'nullable|string',
            'estimasi_menit_per_antrean' => 'required|integer|min:1|max:30',
            'qris_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
        ]);

        $pengaturan = Pengaturan::get();

        $qrisPath = $pengaturan->qris_image;
        if ($request->hasFile('qris_image')) {
            if ($pengaturan->qris_image && Storage::disk('public')->exists($pengaturan->qris_image)) {
                Storage::disk('public')->delete($pengaturan->qris_image);
            }
            $qrisPath = $request->file('qris_image')->store('qris', 'public');
        }

        $pengaturan->update([
            'nama_cafe' => $request->nama_cafe,
            'tagline' => $request->tagline,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'instagram' => $request->instagram,
            'pesan_struk' => $request->pesan_struk,
            'estimasi_menit_per_antrean' => $request->estimasi_menit_per_antrean,
            'qris_image' => $qrisPath,
        ]);

        AktivitasLog::catat(
            'UBAH_PENGATURAN',
            "Memperbarui profil cafe dan pengaturan operasional Sekarsa Coffee."
        );

        return back()->with('success', 'Pengaturan Sekarsa Coffee berhasil diperbarui.');
    }
}
