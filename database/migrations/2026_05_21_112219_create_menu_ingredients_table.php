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
        Schema::create('menu_ingredients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('menu_id')->index('menu_ingredients_menu_id_foreign');
            $table->unsignedBigInteger('item_id')->index('menu_ingredients_item_id_foreign');
            $table->decimal('gramasi_besar')->default(0);
            $table->decimal('gramasi_kecil')->default(0);
            $table->timestamps();
            $table->integer('gramasi_balita')->default(0);
            $table->integer('gramasi_bumil')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_ingredients');
    }
};
