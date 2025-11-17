<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // FK ke users
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->string('metode_pembayaran'); // misal 'transfer', 'cash'
            $table->enum('status_pesanan', ['pending', 'diproses', 'dikirim', 'selesai'])->default('pending');
            $table->text('alamat_pengiriman');
            $table->text('catatan')->nullable();
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down() {
        Schema::dropIfExists('orders');
    }
};