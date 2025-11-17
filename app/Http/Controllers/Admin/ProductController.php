<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;  // Import untuk dropdown
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;  // Import untuk audit

class ProductController extends Controller
{
    // HAPUS CONSTRUCTOR - PINDAH KE ROUTE WEB.PHP

    /**
     * Tampilkan daftar produk (dengan search).
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'creator', 'updater']);  // Load relasi untuk tampilan (tanpa reviews sementara)

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        $products = $query->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Form tambah produk (load categories untuk dropdown).
     */
    public function create()
    {
        $categories = Category::all();  // Load semua kategori untuk dropdown
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Simpan produk baru + upload image + audit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',  // Validasi FK
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $userId = Auth::id();  // ID user login
        $data['created_by'] = $userId ?? null;  // Audit
        $data['updated_by'] = $userId ?? null;  // Audit

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil ditambahkan!');
    }

    /**
     * Form edit produk (load categories).
     */
    public function edit(Product $product)
    {
        $categories = Category::all();  // Load untuk dropdown
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk + ganti image + audit.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',  // Validasi FK
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id() ?? null;  // Audit update

        if ($request->hasFile('image')) {
            // Hapus image lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil diupdate!');
    }

    /**
     * Hapus produk + image.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Produk berhasil dihapus!');
    }
}