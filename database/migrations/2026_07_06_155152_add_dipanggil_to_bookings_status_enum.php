<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('menunggu','disetujui','ditolak','dipanggil','check_in','tidak_hadir','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu'");
        } elseif ($driver === 'sqlite') {
            Schema::create('bookings_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('dokter_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('jadwal_dokter_id')->constrained('jadwal_dokter')->onDelete('cascade');
                $table->date('tanggal_booking');
                $table->time('jam_booking');
                $table->text('keluhan_awal')->nullable();
                $table->string('status', 20)->default('menunggu');
                $table->text('catatan_dokter')->nullable();
                $table->timestamps();
            });

            DB::statement("INSERT INTO bookings_new SELECT * FROM bookings");
            Schema::drop('bookings');
            Schema::rename('bookings_new', 'bookings');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('menunggu','disetujui','ditolak','check_in','tidak_hadir','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu'");
        } elseif ($driver === 'sqlite') {
            Schema::create('bookings_old', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('dokter_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('jadwal_dokter_id')->constrained('jadwal_dokter')->onDelete('cascade');
                $table->date('tanggal_booking');
                $table->time('jam_booking');
                $table->text('keluhan_awal')->nullable();
                $table->string('status', 20)->default('menunggu');
                $table->text('catatan_dokter')->nullable();
                $table->timestamps();
            });

            DB::statement("INSERT INTO bookings_old SELECT * FROM bookings");
            Schema::drop('bookings');
            Schema::rename('bookings_old', 'bookings');
        }
    }
};
