<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('plat_nomor')->unique();
            $table->string('tipe');
            $table->decimal('harga', 15, 2)->default(0); // Tambahan harga kendaraan
            $table->integer('konsumsi_bahan_bakar')->nullable();
            $table->date('perawatan_terakhir')->nullable();
            $table->boolean('tersedia')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kendaraan');
    }
};