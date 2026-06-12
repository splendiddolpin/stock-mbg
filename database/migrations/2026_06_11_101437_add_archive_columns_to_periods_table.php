<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->string('excel_path')->nullable()->after('is_active'); // Path file di server
            $table->string('google_drive_id')->nullable()->after('excel_path'); // ID file di Google Drive
        });
    }

    public function down()
    {
        Schema::table('periods', function (Blueprint $table) {
            $table->dropColumn(['excel_path', 'google_drive_id']);
        });
    }
};