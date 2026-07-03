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
        Schema::table('dokter', function (Blueprint $table) {
            $table->dropColumn(['jam_praktek_mulai', 'jam_praktek_selesai', 'hari_praktek']);
        });
    }

    public function down(): void
    {
        Schema::table('dokter', function (Blueprint $table) {
            $table->time('jam_praktek_mulai')->nullable();
            $table->time('jam_praktek_selesai')->nullable();
            $table->string('hari_praktek', 100)->nullable();
        });
    }
};
