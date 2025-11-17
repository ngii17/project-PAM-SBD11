<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;  // Tambah import ini
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
// Public routes (no auth)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (butuh token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::apiResource('orders', OrderController::class); // CRUD orders
    Route::post('orders/{id}/status', [OrderController::class, 'updateStatus']); // Update status
    
    // Katalog (public, tapi di sini protected—ubah kalau mau public)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::apiResource('carts', CartController::class)->only(['index']);  // GET /api/carts
    Route::post('/carts/clear', [CartController::class, 'clear']);  // Kosongkan cart

    // Cart Items API (user protected)
    Route::apiResource('cart-items', CartController::class)->except(['index', 'show']);  // POST, PUT, DELETE /api/cart-items
    // CRUD admin untuk products (protected + admin middleware)
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });

    // Categories: List public, CRUD admin
    Route::get('/categories', [CategoryController::class, 'index']);  // Public
    Route::get('/categories/{id}', [CategoryController::class, 'show']);  // Public
    
    // CRUD admin untuk categories (protected + admin middleware)
    Route::middleware('admin')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
    
    
});