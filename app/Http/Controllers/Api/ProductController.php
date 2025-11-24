<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with('category') // <=== WAJIB! Biar Flutter dapet nama kategori
            ->select('id', 'name', 'category_id', 'description', 'price', 'stock', 'image', 'created_at', 'updated_at');

        // Filter berdasarkan category_id (bukan category string!)
        if ($request->filled('category_id') && $request->category_id != 0) {
            $query->where('category_id', $request->category_id);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10);

        // Ubah path image jadi full URL biar langsung bisa di-load Flutter/emulator
        $products->getCollection()->transform(function ($product) {
            if ($product->image) {
                $product->image_url = asset('storage/' . $product->image);
            } else {
                $product->image_url = null;
            }
            return $product;
        });

        return response()->json([
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'last_page' => $products->lastPage(),
        ]);
    }

    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        if ($product->image) {
            $product->image_url = asset('storage/' . $product->image);
        }

        return response()->json($product);
    }

    // ================== ADMIN ONLY ==================
    public function store(Request $request)
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id', // <=== PAKAI category_id + exists
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json($product->load('category'), 201);
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAdmin($request);
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'sometimes|required|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'stock'       => 'sometimes|required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json($product->load('category'));
    }

    public function destroy(Request $request, $id)
    {
        $this->authorizeAdmin($request);
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Produk dihapus']);
    }

    // Helper biar kode bersih
    private function authorizeAdmin($request)
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Hanya Admin yang boleh mengakses.');
        }
    }
}