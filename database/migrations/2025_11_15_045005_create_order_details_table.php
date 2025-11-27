<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel orders (Wajib ada)
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Relasi ke produk (Wajib ada)
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); 
            
            $table->integer('qty'); // Jumlah barang
            
            // Kita ubah jadi 12,2 biar sama kayak tabel orders
            $table->decimal('harga_satuan', 12, 2); 
            $table->decimal('subtotal', 12, 2); // qty * harga_satuan
            
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('order_details');
    }
};