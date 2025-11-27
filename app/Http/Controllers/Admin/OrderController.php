<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'details.product'])
                       ->latest()
                       ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('details.product');
        return view('admin.orders.show', compact('order'));
    }

    // INI YANG BARU DITAMBAH — TOMBOL "TANDAI SUDAH BAYAR"
    public function tandaiBayar(Order $order)
    {
        $order->update(['status_pembayaran' => 'sudah']);

        return redirect()->back()->with('success', 'Pesanan berhasil ditandai sudah dibayar!');
    }
}