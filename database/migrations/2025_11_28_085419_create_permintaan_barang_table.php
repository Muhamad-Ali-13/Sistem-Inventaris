<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_permintaan')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barang_id')->constrained('barang')->onDelete('cascade'); // Pastikan merujuk ke 'barang'
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('total_harga', 12, 2);
            $table->string('tujuan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->text('alasan_penolakan')->nullable();
            $table->text('catatan_approval')->nullable();
            $table->timestamp('tanggal_approval')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_barang');
    }
};
