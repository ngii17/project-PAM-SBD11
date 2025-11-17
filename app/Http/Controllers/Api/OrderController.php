<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Untuk user login

class OrderController extends Controller {
    // Buat order baru (user pesan)
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.qty' => 'required|integer|min:1',
            'metode_pembayaran' => 'required|string',
            'alamat_pengiriman' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $userId = Auth::id(); // ID user login
        $totalHarga = 0;

        // Hitung total dari details
        foreach ($request->details as $detail) {
            $product = \App\Models\Product::find($detail['product_id']);
            $subtotal = $product->harga * $detail['qty'];
            $totalHarga += $subtotal;
        }

        // Buat order
        $order = Order::create([
            'user_id' => $userId,
            'total_harga' => $totalHarga,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pesanan' => 'pending',
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'catatan' => $request->catatan,
        ]);

        // Buat details
        foreach ($request->details as $detail) {
            $product = \App\Models\Product::find($detail['product_id']);
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $detail['product_id'],
                'qty' => $detail['qty'],
                'harga_satuan' => $product->harga,
                'subtotal' => $product->harga * $detail['qty']
            ]);
        }

        return response()->json(['order' => $order->load('details.product')], 201); // Return order dengan details
    }

    // Lihat semua orders user
    public function index() {
        $orders = Order::with('details.product')->where('user_id', Auth::id())->get();
        return response()->json($orders);
    }

    // Lihat rincian order spesifik
    public function show($id) {
        $order = Order::with('details.product')->findOrFail($id);
        if ($order->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json($order);
    }

    // Update status (misal user konfirmasi)
    public function updateStatus(Request $request, $id) {
        $order = Order::findOrFail($id);
        if ($order->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $order->update(['status_pesanan' => $request->status_pesanan]);
        return response()->json($order);
    }
}