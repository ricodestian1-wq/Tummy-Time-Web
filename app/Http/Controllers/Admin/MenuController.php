<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->orderBy('sort_order')->get();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menu', compact('menus', 'categories'));
    }

    /**
     * Tambah / update menu (termasuk field stock).
     * stock kosong/null = tidak dibatasi; angka >= 0 = stok terlacak.
     */
    public function save(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer|exists:menus,id',
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
            'stock' => 'nullable|integer|min:0',
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $menu = ! empty($validated['id']) ? Menu::findOrFail($validated['id']) : new Menu;
        $imagePath = $menu->image_url;

        if ($request->hasFile('image') && ! $request->file('image')->isValid()) {
            return response()->json([
                'success' => false,
                'error' => 'Upload gambar gagal. Pastikan file tidak rusak dan ukurannya maksimal 10 MB.',
            ], 422);
        }

        if ($request->hasFile('image')) {
            $oldStoragePath = $imagePath ? ltrim(str_replace('/storage/', '', parse_url($imagePath, PHP_URL_PATH) ?: $imagePath), '/') : null;
            if ($oldStoragePath && Storage::disk('public')->exists($oldStoragePath)) {
                Storage::disk('public')->delete($oldStoragePath);
            }

            $imagePath = Storage::disk('public')->url($request->file('image')->store('menus', 'public'));
        }

        $menu->fill([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'price' => $validated['price'],
            'is_available' => $validated['is_available'],
            'stock' => $validated['stock'] ?? null,
            'image_url' => $imagePath,
        ]);
        $menu->save();

        return response()->json([
            'success' => true,
            'id' => $menu->id,
            'image_url' => $menu->image_url,
        ]);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->json(['success' => true]);
    }

    public function toggleAvailability(Menu $menu)
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        return response()->json(['success' => true, 'is_available' => $menu->is_available]);
    }

    /**
     * Set stok ke angka tertentu (dari form edit menu).
     */
    public function updateStock(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'stock' => 'nullable|integer|min:0',
        ]);

        $menu->update(['stock' => $validated['stock'] ?? null]);

        return response()->json(['success' => true, 'stock' => $menu->stock]);
    }

    /**
     * Tambah/kurangi stok cepat (tombol +/- di dashboard). Tidak berlaku untuk stok "tidak dibatasi".
     */
    public function adjustStock(Request $request, Menu $menu)
    {
        $validated = $request->validate(['delta' => 'required|integer']);

        if ($menu->stock === null) {
            return response()->json([
                'success' => false,
                'error' => 'Menu ini stoknya tidak dibatasi. Set angka awal dulu lewat form edit menu.',
            ], 400);
        }

        $newStock = max(0, $menu->stock + $validated['delta']);
        $menu->update(['stock' => $newStock]);

        return response()->json(['success' => true, 'stock' => $newStock]);
    }

    public function saveCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $name = trim($validated['name']);
        $category = Category::firstOrCreate(['name' => $name]);

        return response()->json([
            'success' => true,
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function destroyCategory(Category $category)
    {
        $category->delete();

        return response()->json(['success' => true]);
    }
}
