<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('staff_cashes', function (Blueprint $table) {
            // Kategori pengeluaran
            $table->string('category')->default('kas')->after('description'); 
            // Penanda apakah ini uang yang dipinjam (utang)
            $table->boolean('is_debt')->default(false)->after('category'); 
            // Penanda apakah utangnya sudah dibayar/dikembalikan
            $table->boolean('is_paid')->default(false)->after('is_debt');
        });
    }

    public function down()
    {
        Schema::table('staff_cashes', function (Blueprint $table) {
            $table->dropColumn(['category', 'is_debt', 'is_paid']);
        });
    }
};