<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relasi: CartItem belongsTo Cart
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // Relasi: CartItem belongsTo Product ← INI YANG BIKIN ERROR KALAU GAK ADA, SEKARANG UDAH ADA!
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Optional: Helper buat load data lengkap (kalau controller butuh)
    public function scopeWithProduct($query)
    {
        return $query->with('product');
    }
}