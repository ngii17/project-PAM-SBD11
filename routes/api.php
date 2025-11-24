<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

// ====================== PUBLIC ROUTES (TIDAK BUTUH LOGIN) ======================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// <<<--- INI YANG WAJIB DIPINDAH KE SINI (PUBLIC) --->
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
// <<<--- SELESAI, SEKARANG BISA DIAKSES TANPA TOKEN --->

// ====================== PROTECTED ROUTES (BUTUH LOGIN) ======================
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Order user
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Cart user
    Route::apiResource('carts', CartController::class)->only(['index']);
    Route::post('/carts/clear', [CartController::class, 'clear']);
    Route::apiResource('cart-items', CartController::class)->except(['index', 'show']);

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});