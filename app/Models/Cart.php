<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // Relasi: Cart belongsTo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Cart hasMany CartItems
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // --- PERBAIKAN DI SINI ---
    // Scope hanya filter query, jangan langsung ->first()
    public function scopeActive($query, $userId)
    {
        return $query->where('user_id', $userId)->where('status', 'active');
    }

    // Helper: Load items + product sekaligus (biar gak N+1 query, dan hindari error relasi undefined)
    public function getItemsWithProductAttribute()
    {
        return $this->items()->with('product')->get();
    }

    // Relasi langsung ke products via items (optional, kalau controller pake $cart->products)
    public function products()
    {
        return $this->belongsToMany(Product::class, 'cart_items')->withPivot('quantity', 'price');
    }
}