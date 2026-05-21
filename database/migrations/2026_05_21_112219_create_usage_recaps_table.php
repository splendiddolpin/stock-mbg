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
        Schema::create('usage_recaps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->unsignedBigInteger('item_id')->index('usage_recaps_item_id_foreign');
            $table->unsignedBigInteger('menu_id')->index('usage_recaps_menu_id_foreign');
            $table->decimal('quantity_out', 12, 4);
            $table->string('unit');
            $table->decimal('total_cost', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_recaps');
    }
};
