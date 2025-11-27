<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">Admin Eleven Flower</h1>
            <div class="flex items-center space-x-6">
                <!-- MENU PESANAN MASUK DITAMBAH DI SINI -->
                <a href="{{ route('admin.orders.index') }}" 
                   class="text-pink-600 font-semibold hover:text-pink-800 border-b-2 {{ request()->is('admin/orders*') ? 'border-pink-600' : 'border-transparent' }}">
                    Pesanan Masuk
                </a>
                <!-- END MENU PESANAN -->

                <a href="{{ route('admin.products.index') }}" 
                   class="text-blue-600 hover:text-blue-800">Produk</a>
                
                <a href="{{ route('admin.categories.index') }}" 
                   class="text-blue-600 hover:text-blue-800">Kategori</a>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 font-medium">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>