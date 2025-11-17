<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model {
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'qty', 'harga_satuan', 'subtotal'];

    // Relationship ke order
    public function order() {
        return $this->belongsTo(Order::class);
    }

    // Relationship ke product (inventaris)
    public function product() {
        return $this->belongsTo(Product::class); // Asumsi model Product ada
    }
}