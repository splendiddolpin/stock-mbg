<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
        $table->id();
        $table->date('date_of_cooking'); // Tanggal makanan akan dimasak
        $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
        $table->decimal('qty_ordered', 12, 2); // Jumlah yang diajukan Ahli Gizi
        $table->decimal('qty_received', 12, 2)->nullable(); // Jumlah fisik yang datang (diisi admin gudang)
        $table->enum('status', ['pending', 'completed'])->default('pending');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
