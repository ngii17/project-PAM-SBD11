<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    // =================================================================
    // 1. AMBIL LIST ULASAN BERDASARKAN PRODUK (PUBLIC)
    // =================================================================
    public function index($productId)
    {
        // Ambil review sesuai ID produk
        // with('user:id,name') -> Kita ambil nama user yang mereview
        $reviews = Review::where('product_id', $productId)
                         ->with('user:id,nama') 
                         ->latest() // Urutkan dari yang terbaru
                         ->get();

        return response()->json([
            'message' => 'List ulasan berhasil diambil',
            'data'    => $reviews
        ]);
    }

    // =================================================================
    // 2. KIRIM ULASAN BARU (BUTUH LOGIN)
    // =================================================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // Cek apakah user sudah pernah review produk ini di order ini (Anti Spam)
        $existing = Review::where('user_id', Auth::id())
                          ->where('order_id', $request->order_id)
                          ->where('product_id', $request->product_id)
                          ->first();

        if ($existing) {
            return response()->json(['message' => 'Anda sudah mengulas produk ini.'], 400);
        }

        // Simpan Review
        Review::create([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
            'order_id'   => $request->order_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return response()->json(['message' => 'Terima kasih atas ulasannya!'], 201);
    }
}