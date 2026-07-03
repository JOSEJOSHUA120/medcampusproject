<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('jenis_kelamin');
        });
    }

    public function down()
    {
        Schema::table('pasien', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
