@extends('layouts.admin')

@section('content')
@php use Illuminate\Support\Str; @endphp

<h2 class="text-2xl font-bold mb-4">Daftar Produk</h2>

<!-- Form search -->
<form method="GET" action="{{ route('admin.products.index') }}" class="mb-4">
    <div class="flex">
        <input type="text" name="q" placeholder="Cari nama atau deskripsi..." value="{{ request('q') }}" class="flex-1 border border-gray-300 p-2 rounded-l-md">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md">Cari</button>
    </div>
</form>

<!-- Tombol tambah -->
<a href="{{ route('admin.products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block">+ Tambah Produk</a>

<!-- Tabel (tanpa kolom rating sementara) -->
<table class="w-full bg-white border border-gray-300 rounded shadow">
    <thead class="bg-gray-200">
        <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Nama</th>
            <th class="border px-4 py-2">Kategori</th>
            <th class="border px-4 py-2">Harga</th>
            <th class="border px-4 py-2">Stock</th>
            <th class="border px-4 py-2">Image</th>
            <th class="border px-4 py-2">Dibuat Oleh</th>
            <th class="border px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $product)
            <tr>
                <td class="border px-4 py-2">{{ $product->id }}</td>
                <td class="border px-4 py-2">{{ $product->name }}</td>
                <td class="border px-4 py-2">{{ $product->category->nama ?? 'Tidak ada' }}</td>
                <td class="border px-4 py-2">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td class="border px-4 py-2">{{ $product->stock }}</td>
                <td class="border px-4 py-2">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover">
                    @else
                        No Image
                    @endif
                </td>
                <td class="border px-4 py-2">{{ $product->creator?->nama ?? 'Sistem' }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2">Edit</a>
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Yakin hapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="border px-4 py-2 text-center">Belum ada produk.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
{{ $products->appends(request()->query())->links() }}
@endsection