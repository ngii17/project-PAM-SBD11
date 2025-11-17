<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Tampilkan cart user saat ini (protected, user login).
     */
    public function index()
    {
        $user = Auth::user();
        $cart = Cart::active($user->id);  // Ambil cart aktif

        if (!$cart) {
            return response()->json(['data' => []]);  // Cart kosong
        }

        $cart->load('items.product');  // Load items + product detail
        $cart->total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return response()->json($cart);
    }

    /**
     * Tambah item ke cart (protected).
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cart = Cart::active($user->id);

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        $product = Product::findOrFail($request->product_id);

        // Cek stock
        if ($product->stock < $request->quantity) {
            abort(400, 'Stock tidak cukup.');
        }

        // Update or create item
        $item = CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $product->id],
            [
                'quantity' => $request->quantity,
                'price' => $product->price,  // Snapshot harga
            ]
        );

        // Update total cart
        $cart->total = $cart->items->sum(fn($i) => $i->quantity * $i->price);
        $cart->save();

        return response()->json($item, 201);
    }

    /**
     * Update quantity item (protected).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $item = CartItem::where('id', $id)
                        ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
                        ->firstOrFail();

        $product = $item->product;
        if ($product->stock < $request->quantity) {
            abort(400, 'Stock tidak cukup.');
        }

        $item->quantity = $request->quantity;
        $item->save();

        // Update total cart
        $item->cart->total = $item->cart->items->sum(fn($i) => $i->quantity * $i->price);
        $item->cart->save();

        return response()->json($item);
    }

    /**
     * Hapus item dari cart (protected).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $item = CartItem::where('id', $id)
                        ->whereHas('cart', fn($q) => $q->where('user_id', $user->id))
                        ->firstOrFail();

        $item->delete();

        // Update total cart
        $item->cart->total = $item->cart->items->sum(fn($i) => $i->quantity * $i->price);
        $item->cart->save();

        return response()->json(['message' => 'Item dihapus']);
    }

    /**
     * Kosongkan cart (protected).
     */
    public function clear()
    {
        $user = Auth::user();
        $cart = Cart::active($user->id);

        if ($cart) {
            $cart->items()->delete();  // Hapus semua items
            $cart->total = 0;
            $cart->save();
        }

        return response()->json(['message' => 'Cart dikosongkan']);
    }
}