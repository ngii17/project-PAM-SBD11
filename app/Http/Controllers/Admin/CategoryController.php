<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;  // IMPORT INI UNTUK FIX auth()->id()

class CategoryController extends Controller
{
    // HAPUS CONSTRUCTOR INI - PINDAH KE ROUTE WEB.PHP

    /**
     * Tampilkan daftar kategori (dengan search).
     */
    public function index(Request $request)
    {
        $query = Category::withCount('products')  // Hitung jumlah produk per kategori
                        ->with('creator', 'updater');  // Load relasi audit (hindari N+1 query)

        if ($request->filled('q')) {
            $query->where('nama', 'like', "%{$request->q}%")
                ->orWhere('description', 'like', "%{$request->q}%");
        }

        $categories = $query->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Form tambah kategori.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama',
            'description' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $userId = Auth::id();  // Pakai Auth facade (aman kalau gak login, null)
        $data['created_by'] = $userId ?? null;  // Fallback null kalau gak login
        $data['updated_by'] = $userId ?? null;

        Category::create($data);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'nama' => 'required|string|max:100|unique:categories,nama,' . $category->id,
            'description' => 'nullable|string|max:500',
        ]);

        $data = $request->all();
        $userId = Auth::id();
        $data['updated_by'] = $userId ?? null;  // Fallback null

        $category->update($data);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diupdate!');
    }

    /**
     * Hapus kategori (hati-hati: cascade hapus products terkait).
     */
    public function destroy(Category $category)
    {
        $userId = Auth::id();
        $category->updated_by = $userId ?? null;  // Update audit sebelum hapus
        $category->save();

        $category->delete();  // Cascade hapus products (dari migrasi onDelete('cascade'))

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}