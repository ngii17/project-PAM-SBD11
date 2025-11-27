<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;

// ====================== PUBLIC ROUTES (BISA DIAKSES TANPA LOGIN) ======================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Produk & Kategori
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// [BARU] LIHAT ULASAN PRODUK (PUBLIC)
// Ini biar customer bisa liat review tanpa harus login dulu
Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// ====================== PROTECTED ROUTES (BUTUH LOGIN) ======================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // --- CHECKOUT & TRANSAKSI ---
    Route::post('/checkout', [OrderController::class, 'store']);

    // RIWAYAT PESANAN
    Route::get('/orders/history', [OrderController::class, 'history']); 

    // KIRIM ULASAN (Hanya user login yang bisa kirim)
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Order Management Lainnya
    Route::apiResource('orders', OrderController::class)->except(['store']); 
    Route::post('orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Keranjang (Cart)
    Route::apiResource('carts', CartController::class)->only(['index']);
    Route::post('/carts/clear', [CartController::class, 'clear']);
    Route::apiResource('cart-items', CartController::class)->except(['index', 'show']);

    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});