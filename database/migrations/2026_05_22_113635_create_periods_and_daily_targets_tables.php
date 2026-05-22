<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // KITA HAPUS BAGIAN CREATE 'periods' KARENA TABELNYA SUDAH ADA.
        // Kita langsung buat tabel 'daily_targets' saja:

        Schema::create('daily_targets', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel 'periods' yang sudah ada di databasemu
            $table->foreignId('period_id')->constrained()->onDelete('cascade');
            $table->date('date'); // Tanggal spesifik
            
            // Relasi ke PM mana yang sedang dicatat
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            
            // Porsi khusus untuk hari itu
            $table->integer('porsi_besar')->default(0);
            $table->integer('porsi_kecil')->default(0);
            $table->integer('total_balita')->default(0);
            $table->integer('total_bumil_busui')->default(0);
            
            // Penanda kalau admin meliburkan PM ini di tanggal tersebut
            $table->boolean('is_holiday')->default(false); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        // Cukup hapus daily_targets, jangan sentuh periods agar data lamamu aman
        Schema::dropIfExists('daily_targets');
    }
};