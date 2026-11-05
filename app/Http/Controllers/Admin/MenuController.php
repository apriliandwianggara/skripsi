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
        $query = Menu::with('category');

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
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

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

        Menu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'is_available' => $request->boolean('is_available'),
            'image' => $imagePath,
            'order' => (int)Menu::max('order') + 1,
        ]);

        return redirect()->route('admin.menus.index')->with('success', "Menu '{$request->name}' berhasil ditambahkan.");
    }

    public function edit(Menu $menu)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150|unique:menus,name,' . $menu->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

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
            'price' => $request->price,
            'is_available' => $request->boolean('is_available'),
            'image' => $imagePath,
        ]);

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
