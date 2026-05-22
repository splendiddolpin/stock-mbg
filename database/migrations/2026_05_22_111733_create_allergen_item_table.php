<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('allergen_item', function (Blueprint $table) {
            $table->id();
            // Sambungan ke Penerima & Bahan
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            
            // Kolom jumlah anak (Langsung kita masukkan di sini!)
            $table->integer('anak_count')->default(1);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('allergen_item');
    }
};