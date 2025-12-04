<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKaryawanIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom karyawan_id
            $table->unsignedBigInteger('karyawan_id')->nullable()->after('id');
            
            // Foreign key constraint
            $table->foreign('karyawan_id')
                  ->references('id')
                  ->on('karyawan')
                  ->onDelete('set null');
                  
            // Index untuk performa
            $table->index('karyawan_id');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['karyawan_id']);
            $table->dropColumn('karyawan_id');
        });
    }
}