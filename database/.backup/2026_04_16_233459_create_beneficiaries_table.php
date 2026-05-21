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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->integer('porsi_besar')->default(0); // Kolom untuk porsi besar
            $table->integer('porsi_kecil')->default(0); // Kolom untuk porsi kecil
            $table->integer('allergen_count')->default(0);
            $table->text('allergen_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};