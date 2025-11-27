<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart; 
use App\Models\CartItem; // Tambah import ini untuk relasi items
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller {
    
public function store(Request $request) {
    $validator = Validator::make($request->all(), [
        'nama_penerima'     => 'required|string',
        'no_hp_penerima'    => 'required|string',
        'alamat_pengiriman' => 'required|string',
        'tanggal_pengiriman'=> 'required|date',
        'ucapan_kartu'      => 'nullable|string',
        'items'             => 'required|array|min:1',
        'items.*.product_id'=> 'required|integer',
        'items.*.quantity'  => 'required|integer|min:1',
        'items.*.price'     => 'required|numeric',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Data tidak lengkap', 'errors' => $validator->errors()], 422);
    }

    $user = Auth::user();
    $items = $request->items;

    try {
        DB::beginTransaction();

        $totalHarga = collect($items)->sum(fn($i) => $i['price'] * $i['quantity']);

        $noInvoice = 'INV-' . date('YmdHis');

        $order = Order::create([
            'user_id'           => $user->id,
            'no_invoice'        => $noInvoice,
            'nama_penerima'     => $request->nama_penerima,
            'no_hp_penerima'    => $request->no_hp_penerima,
            'alamat_pengiriman' => $request->alamat_pengiriman,
            'tanggal_pengiriman'=> $request->tanggal_pengiriman,
            'ucapan_kartu'      => $request->ucapan_kartu,
            'total_price'       => $totalHarga,
            'status'            => 'pending',
        ]);

        foreach ($items as $item) {
            OrderDetail::create([
                'order_id'    => $order->id,
                'product_id'  => $item['product_id'],
                'qty'         => $item['quantity'],
                'harga_satuan'=> $item['price'],     // INI YANG HARUSNYA!
                'subtotal'    => $item['price'] * $item['quantity'],
            ]);
        }

        // Kosongkan cart
        $cart = Cart::active($user->id)->first();
        if ($cart) $cart->items()->delete();

        DB::commit();

        $waUrl = "https://wa.me/6281280163853?text=" . urlencode(
            "Pesanan Baru!\n\n" .
            "Invoice: $noInvoice\n" .
            "Penerima: {$request->nama_penerima}\n" .
            "Total: Rp " . number_format($totalHarga, 0, ',', '.') . "\n\n" .
            "Mohon proses ya kak!"
        );

        return response()->json([
            'message' => 'Checkout berhasil',
            'payment_url' => $waUrl
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Gagal checkout: ' . $e->getMessage()], 500);
    }
}    
    // ... Function index, show dll biarkan di bawah sini ...
}