@extends('layouts.admin')

@section('content')
@php use Illuminate\Support\Str; @endphp  {{-- IMPORT UNTUK Str::limit --}}

<h2 class="text-2xl font-bold mb-4">Daftar Kategori</h2>

<!-- Form search -->
<form method="GET" action="{{ route('admin.categories.index') }}" class="mb-4">
    <div class="flex">
        <input type="text" name="q" placeholder="Cari nama atau deskripsi..." value="{{ request('q') }}" class="flex-1 border border-gray-300 p-2 rounded-l-md">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-r-md">Cari</button>
    </div>
</form>

<!-- Tombol tambah -->
<a href="{{ route('admin.categories.create') }}" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block">+ Tambah Kategori</a>

<!-- Tabel -->
<table class="w-full bg-white border border-gray-300 rounded shadow">
    <thead class="bg-gray-200">
        <tr>
            <th class="border px-4 py-2">ID</th>
            <th class="border px-4 py-2">Nama</th>
            <th class="border px-4 py-2">Deskripsi</th>
            <th class="border px-4 py-2">Dibuat Oleh</th>
            <th class="border px-4 py-2">Diupdate Oleh</th>
            <th class="border px-4 py-2">Jumlah Produk</th>
            <th class="border px-4 py-2">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($categories as $category)
            <tr>
                <td class="border px-4 py-2">{{ $category->id }}</td>
                <td class="border px-4 py-2">{{ $category->nama }}</td>
                <td class="border px-4 py-2">{{ Str::limit($category->description ?? '', 50) }}</td>
                <td class="border px-4 py-2">{{ $category->creator?->nama ?? 'Sistem' }}</td>
                <td class="border px-4 py-2">{{ $category->updater?->nama ?? 'Sistem' }}</td>
                <td class="border px-4 py-2">{{ $category->products_count ?? 0 }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="bg-yellow-500 text-white px-3 py-1 rounded mr-2">Edit</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Yakin hapus? Ini akan hapus produk terkait!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="border px-4 py-2 text-center">Belum ada kategori.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
{{ $categories->appends(request()->query())->links() }}
@endsection