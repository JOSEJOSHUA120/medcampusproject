<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->text('complaint')->nullable()->after('status');
            $table->string('duration')->nullable()->after('complaint');
            $table->integer('pain_level')->nullable()->after('duration');
            $table->text('notes')->nullable()->after('pain_level');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete()->after('notes');
        });

        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('antrian', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'dipanggil', 'dikonfirmasi', 'sedang_dilayani', 'selesai', 'dibatalkan'])->default('menunggu')->after('jam_antrian');
        });
    }

    public function down(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn(['complaint', 'duration', 'pain_level', 'notes', 'room_id']);
        });

        Schema::table('antrian', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('antrian', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'dipanggil', 'diperiksa', 'selesai', 'batal'])->default('menunggu')->after('jam_antrian');
        });
    }
};
