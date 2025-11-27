<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 

class OrderController extends Controller
{
    // =================================================================
    // 1. STORE (CHECKOUT) - MENYIMPAN PESANAN BARU
    // =================================================================
    public function store(Request $request)
    {   
        // Validasi input dari Flutter
        $validator = Validator::make($request->all(), [
            'nama_penerima'     => 'required|string|max:255',
            'no_hp_penerima'    => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string',
            'tanggal_pengiriman'=> 'required|date',
            'ucapan_kartu'      => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|integer|exists:products,id',
            'items.*.quantity'  => 'required|integer|min:1',
            'items.*.price'     => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak lengkap',
                'errors'  => $validator->errors()
            ], 422);
        }

        $user  = Auth::user();
        $items = $request->items;

        try {
            DB::beginTransaction();

            // Hitung total harga
            $totalHarga = collect($items)->sum(fn($item) => $item['price'] * $item['quantity']);

            // Buat nomor invoice unik
            $noInvoice = 'INV-' . date('Ymd') . str_pad(Order::count() + 1, 5, '0', STR_PAD_LEFT);

            // SIMPAN PESANAN KE TABEL ORDERS
            $order = Order::create([
                'user_id'            => $user->id,
                'no_invoice'         => $noInvoice,
                'nama_penerima'      => $request->nama_penerima,
                'no_hp_penerima'     => $request->no_hp_penerima,
                'alamat_pengiriman'  => $request->alamat_pengiriman,
                'tanggal_pengiriman' => $request->tanggal_pengiriman,
                'ucapan_kartu'       => $request->ucapan_kartu,
                'total_price'        => $totalHarga,
                'status'             => 'pending',
                'status_pembayaran'  => 'belum', // Default belum bayar
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // SIMPAN DETAIL PESANAN
            foreach ($items as $item) {
                OrderDetail::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'qty'          => $item['quantity'],
                    'harga_satuan' => $item['price'],
                    'subtotal'     => $item['price'] * $item['quantity'],
                ]);
            }

            // Kosongkan keranjang user
            $cart = Cart::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->first();
            if ($cart) {
                $cart->items()->delete();
            }

            DB::commit();

            // Buat pesan WhatsApp
            $pesan = "Pesanan Baru!\n\n" .
                     "Invoice: *$noInvoice*\n" .
                     "Penerima: {$request->nama_penerima}\n" .
                     "No HP: {$request->no_hp_penerima}\n" .
                     "Alamat: {$request->alamat_pengiriman}\n" .
                     "Tanggal Kirim: {$request->tanggal_pengiriman}\n" .
                     "Ucapan: " . ($request->ucapan_kartu ?: 'Tidak ada') . "\n\n" .
                     "*Total Bayar:* Rp " . number_format($totalHarga, 0, ',', '.') . "\n\n" .
                     "*Detail Pesanan:*\n";

            foreach ($items as $item) {
                $pesan .= "• Produk ID: {$item['product_id']}\n" .
                          "  Jumlah: {$item['quantity']} x Rp " . number_format($item['price'], 0, ',', '.') . "\n" .
                          "  Subtotal: Rp " . number_format($item['price'] * $item['quantity'], 0, ',', '.') . "\n\n";
            }

            $pesan .= "Terima kasih kak! Mohon segera diproses ya";

            $waUrl = "https://wa.me/6281280163853?text=" . urlencode($pesan);

            return response()->json([
                'message'     => 'Pesanan berhasil disimpan!',
                'payment_url' => $waUrl,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout gagal: ' . $e->getMessage()); 
            return response()->json([
                'message' => 'Gagal checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    // =================================================================
    // 2. HISTORY - MENAMPILKAN RIWAYAT YANG SUDAH DIBAYAR
    // =================================================================
public function history()
    {
        $user = Auth::user();

        // SEKARANG KITA AMBIL SEMUA (Hapus where status_pembayaran = sudah)
        // Biar nanti Flutter yang filter sendiri mana yang pending/selesai
        $orders = Order::where('user_id', $user->id)
                       ->with(['details.product']) 
                       ->orderBy('created_at', 'desc')
                       ->get();

        return response()->json([
            'message' => 'Data pesanan berhasil diambil',
            'data'    => $orders
        ]);
    }
}