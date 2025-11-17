<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar kategori (public untuk Flutter, dengan search opsional).
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products'); // Load jumlah produk per kategori

        if ($request->filled('q')) {
            $query->where('nama', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%");
        }

        $categories = $query->paginate(10);

        return response()->json($categories);
    }

    /**
     * Tampilkan detail kategori (public).
     */
    public function show($id)
    {
        $category = Category::with(['products', 'creator', 'updater'])->findOrFail($id); // Load relasi
        return response()->json($category);
    }

    /**
     * Buat kategori baru (admin only, protected).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama',
            'description' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $userId = Auth::id();
        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    /**
     * Update kategori (admin only).
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama,' . $id,
            'description' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $category->update($data);

        return response()->json($category);
    }

    /**
     * Hapus kategori (admin only).
     */
    public function destroy(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $category->updated_by = Auth::id();
        $category->save();

        $category->delete(); // Cascade hapus products

        return response()->json(['message' => 'Kategori dihapus'], 200);
    }
}