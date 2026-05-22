<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daily_menus', function (Blueprint $table) {
            // Menambahkan target: 'sekolah', 'posyandu', atau 'semua'
            $table->enum('target_type', ['sekolah', 'posyandu', 'semua'])->default('semua')->after('menu_id');
        });
    }

    public function down()
    {
        Schema::table('daily_menus', function (Blueprint $table) {
            $table->dropColumn('target_type');
        });
    }
};