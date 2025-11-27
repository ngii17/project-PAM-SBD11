<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController; // Pastikan ini OrderController

// ====================== PUBLIC ROUTES ======================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Produk & Kategori (Bisa dilihat tanpa login)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// ====================== PROTECTED ROUTES (BUTUH LOGIN) ======================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // --- KHUSUS CHECKOUT (INI YANG PENTING BUAT FLUTTER KAMU) ---
    // ← TAMBAH INI SAJA UNTUK CHECKOUT! (Di dalam middleware auth kalau ada)
    Route::post('/checkout', [OrderController::class, 'store']);

    // Order History & Status
    Route::apiResource('orders', OrderController::class)->except(['store']); // store udah dipake di checkout
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