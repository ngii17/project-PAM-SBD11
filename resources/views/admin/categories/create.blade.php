@extends('layouts.admin')

@section('content')
<h2 class="text-2xl font-bold mb-4">Tambah Kategori Baru</h2>
<form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white p-6 rounded shadow">
    @csrf
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Nama Kategori</label>
        <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border border-gray-300 p-2 rounded @error('nama') border-red-500 @enderror">
        @error('nama') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Deskripsi (Opsional)</label>
        <textarea name="description" rows="3" class="w-full border border-gray-300 p-2 rounded @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">Simpan Kategori</button>
    <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600 ml-2">Batal</a>
</form>
@endsection