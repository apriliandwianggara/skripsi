<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('menus')->orderBy('order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'coffee',
            'order' => Category::max('order') + 1,
            'is_active' => true,
        ]);

        return back()->with('success', "Kategori '{$category->name}' berhasil dibuat.");
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'icon' => $request->icon ?: 'coffee',
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy(Category $category)
    {
        if ($category->menus()->count() > 0) {
            return back()->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih memuat menu.");
        }

        $name = $category->name;
        $category->delete();

        return back()->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}

