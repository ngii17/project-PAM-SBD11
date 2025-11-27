<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'no_invoice',  // ← INI KUNCI! Buat hindari null violation
        'nama_penerima',  // Dari form Flutter
        'no_hp_penerima',  // Dari form
        'alamat_pengiriman',  // Dari form
        'tanggal_pengiriman',  // Dari form
        'ucapan_kartu',  // Dari form (bisa null)
        'total_harga',  // Total dari cart
        'status_pembayaran',  // Default 'pending'
        'metode_pembayaran',  // Default 'transfer' atau null
        'catatan',  // Opsional
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',  // Biar total jadi number
        'tanggal_pengiriman' => 'date',  // Format tanggal
    ];

    // Relationship: Satu order punya banyak details
    public function details() {
        return $this->hasMany(OrderDetail::class);
    }

    // Relationship ke user
    public function user() {
        return $this->belongsTo(User::class);
    }
}