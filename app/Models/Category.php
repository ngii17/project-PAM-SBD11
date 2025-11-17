<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;  // Import untuk relasi audit

class Category extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'nama',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Relasi: Satu kategori punya banyak produk.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi audit: Siapa yang buat/update.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}