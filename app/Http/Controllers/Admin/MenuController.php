<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('is_available')) {
            $query->where('is_available', $request->is_available);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $menus = $query->orderBy('category_id')->orderBy('order')->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menus', 'public');
        }

        Menu::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'is_available' => $request->boolean('is_available'),
            'image' => $imagePath,
            'order' => Menu::max('order') + 1,
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $menu->image;
        if ($request->hasFile('image')) {
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $imagePath = $request->file('image')->store('menus', 'public');
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
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', "Menu '{$name}' berhasil dihapus.");
    }

    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => !$menu->is_available]);
        $statusText = $menu->is_available ? 'Tersedia' : 'Habis';
        return back()->with('success', "Status ketersediaan menu {$menu->name} diubah menjadi {$statusText}.");
    }
}
