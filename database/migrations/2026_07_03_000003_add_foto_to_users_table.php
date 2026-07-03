<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('remember_token');
        });

        Schema::table('dokter', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('hari_praktek');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
        Schema::table('dokter', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
