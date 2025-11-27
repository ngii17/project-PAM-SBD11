<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up() {
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        
        // Data Pembeli (Akun)
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        // Identitas Pesanan
        $table->string('no_invoice'); // Tambahan: Biar keren di WA (misal: INV-251123-001)
        
        // Data Penerima (PENTING BUAT BUNGA)
        $table->string('nama_penerima'); 
        $table->string('no_hp_penerima');
        $table->text('alamat_pengiriman');
        $table->date('tanggal_pengiriman'); // Tambahan: User mau dikirim kapan?
        
        // Detail Pesanan
        $table->text('ucapan_kartu')->nullable(); // Tambahan: "Happy Birthday Sayang..."
        $table->text('catatan')->nullable(); // "Pagar warna hitam, titip satpam"
        
        // Keuangan
        $table->decimal('total_harga', 12, 2)->default(0); // Ubah 10,2 jadi 12,2 biar muat angka jutaan besar
        $table->string('metode_pembayaran')->default('transfer_wa'); // Default aja karena kita arahin ke WA
        
        // Status
        $table->enum('status_pesanan', ['menunggu_pembayaran', 'sudah_bayar', 'diproses', 'dikirim', 'selesai', 'batal'])
              ->default('menunggu_pembayaran');
        $table->timestamps();
    });
}
};