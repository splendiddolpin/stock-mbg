<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kunci target ke tabel 'menu_ingredients'
        Schema::table('menu_ingredients', function (Blueprint $table) {
            
            // Pengaman cek kolom
            if (!Schema::hasColumn('menu_ingredients', 'gramasi_balita')) {
                $table->integer('gramasi_balita')->default(0);
            }
            
            if (!Schema::hasColumn('menu_ingredients', 'gramasi_bumil')) {
                $table->integer('gramasi_bumil')->default(0);
            }
            
        });
    }

    public function down(): void
    {
        Schema::table('menu_ingredients', function (Blueprint $table) {
            $table->dropColumn(['gramasi_balita', 'gramasi_bumil']);
        });
    }
};