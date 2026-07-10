<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrian_new', function ($table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokter')->cascadeOnDelete();
            $table->string('nomor_antrian', 10);
            $table->date('tanggal_antrian');
            $table->time('jam_antrian');
            $table->string('status', 20)->default('menunggu');
            $table->text('complaint')->nullable();
            $table->string('duration')->nullable();
            $table->integer('pain_level')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->timestamps();
        });

        $cols = 'id, pasien_id, dokter_id, nomor_antrian, tanggal_antrian, jam_antrian, status, complaint, duration, pain_level, notes, NULL as room_id, created_at, updated_at';
        DB::statement("INSERT INTO antrian_new SELECT {$cols} FROM antrian");

        Schema::drop('antrian');
        Schema::rename('antrian_new', 'antrian');
    }

    public function down(): void
    {
        Schema::table('antrian', function ($table) {
            $table->dropForeign(['room_id']);
            $table->dropColumn('room_id');
        });
    }
};
