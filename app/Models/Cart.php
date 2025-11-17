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

    // Scope untuk cart aktif user
    public function scopeActive($query, $userId)
    {
        return $query->where('user_id', $userId)->where('status', 'active')->first();
    }
}