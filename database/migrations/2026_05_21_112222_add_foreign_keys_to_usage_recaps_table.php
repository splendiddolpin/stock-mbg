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
        Schema::table('usage_recaps', function (Blueprint $table) {
            $table->foreign(['item_id'])->references(['id'])->on('items')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['menu_id'])->references(['id'])->on('menus')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usage_recaps', function (Blueprint $table) {
            $table->dropForeign('usage_recaps_item_id_foreign');
            $table->dropForeign('usage_recaps_menu_id_foreign');
        });
    }
};
