<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'alamat',
        'no_telepon',
        'email',
        'password',
        'roles',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->roles === 'admin';
    }

    // INI YANG BENAR — ADA SPASI SETELAH return!
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function carts()
    {
        return $this->hasMany(\App\Models\Cart::class);
    }

    public function activeCart()
    {
        return $this->hasOne(\App\Models\Cart::class)->where('status', 'active');
    }

    public function createdProducts()
    {
        return $this->hasMany(\App\Models\Product::class, 'created_by');
    }

    public function updatedProducts()
    {
        return $this->hasMany(\App\Models\Product::class, 'updated_by');
    }

    public function createdCategories()
    {
        return $this->hasMany(\App\Models\Category::class, 'created_by');
    }
}