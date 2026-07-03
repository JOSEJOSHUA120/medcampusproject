<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_dokter_id')->constrained('jadwal_dokter')->onDelete('cascade');
            $table->date('tanggal_booking');
            $table->time('jam_booking');
            $table->text('keluhan_awal')->nullable();
            $table->enum('status', ['menunggu','disetujui','ditolak','check_in','tidak_hadir','selesai','dibatalkan'])->default('menunggu');
            $table->text('catatan_dokter')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
