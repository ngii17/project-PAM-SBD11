<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');  // FK ke carts
            $table->foreignId('product_id')->constrained()->onDelete('cascade');  // FK ke products
            $table->integer('quantity')->default(1);  // Jumlah item
            $table->decimal('price', 10, 2);  // Harga snapshot saat add
            $table->timestamps();

            // Unique: Gak boleh duplicate product di cart yang sama
            $table->unique(['cart_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};