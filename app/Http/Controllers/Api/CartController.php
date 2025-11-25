<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Tampilkan cart user saat ini
     */
    public function index()
    {
        $user = Auth::user();
        
        // Pastikan model Cart punya scopeActive atau pakai where manual
        $cart = Cart::where('user_id', $user->id)->where('status', 'active')->first();

        // FIX: Kalau cart kosong, balikan struktur JSON yang dimengerti Flutter
        if (!$cart) {
            return response()->json([
                'id' => 0,
                'total' => 0,
                'items' => [] // List kosong biar Flutter gak error looping
            ], 200);
        }

        $cart->load('items.product'); // Load relasi

        // Hitung total on-the-fly biar akurat
        $total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return response()->json([
            'id' => $cart->id,
            'total' => $total,
            'items' => $cart->items
        ]);
    }

    /**
     * Tambah item ke cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        
        // Cek atau buat cart baru
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active'],
            ['total' => 0] // Default value lain jika ada
        );

        $product = Product::findOrFail($request->product_id);

        // Cek apakah item sudah ada di cart?
        $existingItem = CartItem::where('cart_id', $cart->id)
                                ->where('product_id', $product->id)
                                ->first();

        if ($existingItem) {
            // FIX LOGIC: Kalau sudah ada, tambah quantity-nya (Increment)
            // Cek stok dulu apakah cukup kalau ditambah
            if ($product->stock < ($existingItem->quantity + $request->quantity)) {
                return response()->json(['message' => 'Stock tidak cukup'], 400);
            }

            $existingItem->quantity += $request->quantity;
            $existingItem->price = $product->price; // Update harga jika berubah
            $existingItem->save();
        } else {
            // Kalau belum ada, buat baru
            if ($product->stock < $request->quantity) {
                return response()->json(['message' => 'Stock tidak cukup'], 400);
            }

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }

        return response()->json(['message' => 'Berhasil masuk keranjang'], 201);
    }

    /**
     * Update quantity item
     */
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = CartItem::with('product')->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Cek stok
        if ($item->product->stock < $request->quantity) {
            return response()->json(['message' => 'Stock tidak cukup'], 400);
        }

        $item->quantity = $request->quantity;
        $item->save();

        return response()->json(['message' => 'Cart updated'], 200);
    }

    /**
     * Hapus item dari cart
     */
    public function destroy($id)
    {
        $item = CartItem::find($id);
        
        if ($item) {
            $item->delete();
            return response()->json(['message' => 'Item removed'], 200);
        }

        return response()->json(['message' => 'Item not found'], 404);
    }

    /**
     * Kosongkan cart
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = Cart::where('user_id', $user->id)->where('status', 'active')->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared'], 200);
    }
}