@extends('layouts.admin')

@section('content')
<h2 class="text-2xl font-bold mb-4">Edit Produk: {{ $product->name }}</h2>
<form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Nama Produk</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full border border-gray-300 p-2 rounded @error('name') border-red-500 @enderror">
        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Kategori</label>
        <select name="category_id" required class="w-full border border-gray-300 p-2 rounded @error('category_id') border-red-500 @enderror">
            <option value="">Pilih Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->nama }}</option>
            @endforeach
        </select>
        @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Deskripsi</label>
        <textarea name="description" rows="3" required class="w-full border border-gray-300 p-2 rounded @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Harga (Rp)</label>
        <input type="number" name="price" step="0.01" value="{{ old('price', $product->price) }}" required class="w-full border border-gray-300 p-2 rounded @error('price') border-red-500 @enderror">
        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Stock</label>
        <input type="number" name="stock" min="0" value="{{ old('stock', $product->stock) }}" required class="w-full border border-gray-300 p-2 rounded @error('stock') border-red-500 @enderror">
        @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Image Saat Ini</label>
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover mb-2">
        @else
            <p class="text-gray-500">Tidak ada image.</p>
        @endif
        <label class="block text-sm font-medium mb-2 mt-4">Ganti Image (Opsional)</label>
        <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 p-2 rounded @error('image') border-red-500 @enderror">
        @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Update Produk</button>
    <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 ml-2">Batal</a>
</form>
@endsection