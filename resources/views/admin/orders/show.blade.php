@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-3xl font-bold text-pink-800">#{{ $order->no_invoice }}</h1>
                <p class="text-gray-600">Dibuat: {{ $order->created_at->format('d F Y H:i') }}</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded hover:bg-gray-600">
                Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Info Penerima -->
            <div class="bg-pink-50 p-6 rounded-lg">
                <h3 class="font-bold text-lg mb-4">Penerima</h3>
                <p><strong>Nama:</strong> {{ $order->nama_penerima }}</p>
                <p><strong>HP:</strong> {{ $order->no_hp_penerima }}</p>
                <p><strong>Alamat:</strong> {{ $order->alamat_pengiriman }}</p>
                <p><strong>Tanggal Kirim:</strong> {{ \Carbon\Carbon::parse($order->tanggal_pengiriman)->format('d F Y') }}</p>
                @if($order->ucapan_kartu)
                    <p class="mt-3"><strong>Ucapan:</strong> <em>"{{ $order->ucapan_kartu }}"</em></p>
                @endif
            </div>

            <!-- Status Bayar — FOKUS UTAMA! -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-lg text-center">
                <h3 class="text-2xl font-bold mb-6 text-pink-800">Status Pembayaran</h3>
                @if($order->status_pembayaran == 'sudah')
                    <div class="text-8xl mb-4 text-green-600">✓</div>
                    <p class="text-4xl font-bold text-green-600">SUDAH DIBAYAR</p>
                @else
                    <div class="text-8xl mb-4 text-red-600">✗</div>
                    <p class="text-4xl font-bold text-red-600">BELUM DIBAYAR</p>
                @endif
            </div>
        </div>

        <!-- Detail Pesanan — TANPA TOTAL TAGIHAN -->
        <h3 class="font-bold text-xl mb-4">Detail Pesanan</h3>
        <div class="border rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Produk</th>
                        <th class="px-6 py-3 text-center">Jumlah</th>
                        <th class="px-6 py-3 text-right">Harga</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $d)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $d->product?->name ?? 'Produk ID: ' . $d->product_id }}</td>
                        <td class="px-6 py-4 text-center font-medium">{{ $d->qty }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-green-600">
                            Rp {{ number_format($d->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tombol Tandai Bayar kalau belum -->
        @if($order->status_pembayaran == 'belum')
            <div class="mt-10 text-center">
                <form action="{{ route('admin.orders.bayar', $order) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-12 py-5 rounded-xl text-2xl font-bold shadow-xl transform hover:scale-105 transition">
                        Tandai Sudah Dibayar
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection