<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique();
            $table->text('deskripsi')->nullable();
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->integer('stok');
            $table->integer('stok_minimal')->default(0);
            $table->decimal('harga', 15, 2)->default(0); // Tambahan harga
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barang');
    }
};