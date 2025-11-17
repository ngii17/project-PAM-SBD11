<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'total_harga', 'metode_pembayaran', 'status_pesanan', 'alamat_pengiriman', 'catatan'];

    // Relationship: Satu order punya banyak details
    public function details() {
        return $this->hasMany(OrderDetail::class);
    }

    // Relationship ke user
    public function user() {
        return $this->belongsTo(User::class);
    }
}