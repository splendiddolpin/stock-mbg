<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_catalogs', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // Buah, Karbohidrat, Protein, dll
            $table->string('name');     // Ayam Geprek, Melon, dll
            $table->integer('price');   // Harga untuk kalkulator budget siswa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_catalogs');
    }
};