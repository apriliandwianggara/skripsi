<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('order')->get();
        $query = Menu::with(['category', 'variations']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_available', $request->status === 'available');
        }

        $menus = $query->orderBy('category_id')->orderBy('order')->paginate(15);

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150|unique:menus,name',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'has_variations' => 'nullable|boolean',
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:100',
            'variations.*.price' => 'nullable|numeric|min:0',
        ]);

        $hasVariations = $request->boolean('has_variations');
        $validVariations = [];
        if ($hasVariations && is_array($request->variations)) {
            foreach ($request->variations as $var) {
                if (!empty($var['name']) && isset($var['price']) && is_numeric($var['price'])) {
                    $validVariations[] = [
                        'name' => trim($var['name']),
                        'price' => (float)$var['price'],
                    ];
                }
            }
        }

        // Tentukan harga dasar menu
        $basePrice = (float)($request->price ?? 0);
        if (!empty($validVariations)) {
            // Jika ada variasi, gunakan harga variasi terendah sebagai patokan harga dasar
            $basePrice = (float)min(array_column($validVariations, 'price'));
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/menus');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $imagePath = 'images/menus/' . $filename;
        }

        $menu = Menu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $basePrice,
            'is_available' => $request->boolean('is_available'),
            'image' => $imagePath,
            'order' => (int)Menu::max('order') + 1,
        ]);

        // Simpan variasi jika ada
        if (!empty($validVariations)) {
            foreach ($validVariations as $index => $v) {
                $menu->variations()->create([
                    'name' => $v['name'],
                    'price' => $v['price'],
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$request->name}' berhasil ditambahkan.");
    }

    public function edit(Menu $menu)
    {
        $menu->load('variations');
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150|unique:menus,name,' . $menu->id,
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'has_variations' => 'nullable|boolean',
            'variations' => 'nullable|array',
            'variations.*.name' => 'nullable|string|max:100',
            'variations.*.price' => 'nullable|numeric|min:0',
        ]);

        $hasVariations = $request->boolean('has_variations');
        $validVariations = [];
        if ($hasVariations && is_array($request->variations)) {
            foreach ($request->variations as $var) {
                if (!empty($var['name']) && isset($var['price']) && is_numeric($var['price'])) {
                    $validVariations[] = [
                        'name' => trim($var['name']),
                        'price' => (float)$var['price'],
                    ];
                }
            }
        }

        // Tentukan harga dasar menu
        $basePrice = (float)($request->price ?? $menu->price);
        if (!empty($validVariations)) {
            $basePrice = (float)min(array_column($validVariations, 'price'));
        }

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada di public_path
            if ($menu->image && file_exists(public_path($menu->image))) {
                @unlink(public_path($menu->image));
            }

            $file = $request->file('image');
            $filename = 'menu_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/menus');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $imagePath = 'images/menus/' . $filename;
        }

        $menu->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $basePrice,
            'is_available' => $request->boolean('is_available'),
            'image' => $imagePath,
        ]);

        // Sinkronisasi variasi menu
        $menu->variations()->delete();
        if (!empty($validVariations)) {
            foreach ($validVariations as $index => $v) {
                $menu->variations()->create([
                    'name' => $v['name'],
                    'price' => $v['price'],
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    public function destroy(Menu $menu)
    {
        $name = $menu->name;
        if ($menu->image && file_exists(public_path($menu->image))) {
            @unlink(public_path($menu->image));
        }
        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$name}' berhasil dihapus.");
    }

    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);
        $status = $menu->is_available ? 'Tersedia' : 'Habis';

        return back()->with('success', "Status menu '{$menu->name}' diubah menjadi: {$status}.");
    }
}
