<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Category;  // IMPORT INI UNTUK FIX ERROR

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'alamat',
        'no_telepon',
        'email',
        'password',
        'roles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // Auto hash password
    ];

    // Method helper untuk cek role
    public function isAdmin(): bool
    {
        return $this->roles === 'admin';
    }

    // Relasi untuk proyek (contoh, tambah sesuai kebutuhan)
    // public function carts()
    // {
    //     return $this->hasMany(Cart::class);  // Asumsi model Cart nanti
    // }

    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class);
    // }
    // Di app/Models/User.php, tambah relasi
    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    public function updatedProducts()
    {
        return $this->hasMany(Product::class, 'updated_by');
    }

    // Serupa untuk categories kalau ada
    public function createdCategories()
    {
        return $this->hasMany(Category::class, 'created_by');
    }
    // Tambah di relasi existing
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function activeCart()
    {
        return $this->hasOne(Cart::class)->where('status', 'active');
    }
}