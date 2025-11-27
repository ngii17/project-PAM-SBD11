@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-pink-800">Pesanan Masuk</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($orders->count() == 0)
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-8 text-center">
            <p class="text-2xl text-yellow-800">Belum ada pesanan masuk</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-pink-50">
                    <tr>
                        <th class="px-6 py-4 text-left">Invoice</th>
                        <th class="px-6 py-4 text-left">Penerima</th>
                        <th class="px-6 py-4 text-left">Tanggal Kirim</th>
                        <th class="px-6 py-4 text-left">Status Bayar</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-pink-50 transition">
                        <td class="px-6 py-4 font-mono text-pink-600 font-bold">
                            {{ $order->no_invoice }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium">{{ $order->nama_penerima }}</p>
                            <p class="text-sm text-gray-500">{{ $order->no_hp_penerima }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ \Carbon\Carbon::parse($order->tanggal_pengiriman)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($order->status_pembayaran === 'sudah')
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800">
                                    ✓ Sudah Bayar
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-100 text-red-800">
                                    ✗ Belum Bayar
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center space-x-2">
                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                Lihat
                            </a>
                            @if($order->status_pembayaran !== 'sudah')
                                <form action="{{ route('admin.orders.bayar', $order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
                                        Tandai Sudah Bayar
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4 bg-gray-50">
                {{ $orders->links() }}
            </div>
        </div>
    @endif
</div>
@endsection