<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;  // Import untuk relasi category
use App\Models\User;     // Import untuk relasi audit
use App\Models\CartItem; // Import untuk relasi cartItems

class Product extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi massal (dari form/API).
     */
    protected $fillable = [
        'name',
        'category_id',  // FK ke categories
        'description',
        'price',
        'stock',
        'image',
        'created_by',   // Audit field
        'updated_by',   // Audit field
    ];

    /**
     * Cast tipe data (price jadi decimal).
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relasi untuk ulasan/keranjang
    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }

    // public function carts()
    // {
    //     return $this->hasMany(Cart::class);
    // }

    // Relasi utama: Produk belongsTo satu kategori
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi audit: Siapa yang buat/update
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi balik: Product hasMany CartItem (untuk lengkapi relasi cart)
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // Scope untuk filter/search (dipakai di controller)
    public function scopeByCategoryId($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('description', 'like', "%{$search}%");
    }

    // Getter untuk rating rata-rata
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}