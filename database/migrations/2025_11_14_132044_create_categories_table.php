<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();  // Nama kategori unik (misalnya 'Mawar', 'Melati')
            $table->text('description')->nullable();  // Deskripsi opsional
            $table->unsignedBigInteger('created_by')->nullable();  // FK ke users
            $table->unsignedBigInteger('updated_by')->nullable();  // FK ke users
            $table->timestamps();

            // Foreign key untuk audit
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};