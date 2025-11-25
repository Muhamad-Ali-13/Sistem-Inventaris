<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Employees foreign keys
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });

        // Items foreign keys
        Schema::table('items', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // Transactions foreign keys
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Requests foreign keys
        Schema::table('requests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });

        // Vehicle usage foreign keys
        Schema::table('vehicle_usage', function (Blueprint $table) {
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Drop foreign keys
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['item_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('requests', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['approved_by']);
        });

        Schema::table('vehicle_usage', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['approved_by']);
        });
    }
};