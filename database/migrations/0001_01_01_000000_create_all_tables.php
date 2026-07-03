<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'dokter', 'pasien'])->default('pasien');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('no_telp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->timestamps();
        });

        Schema::create('dokter', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_dokter');
            $table->string('spesialisasi');
            $table->string('no_telp', 20)->nullable();
            # jam_praktek_mulai: jam mulai praktik dokter (format HH:MM)
            $table->time('jam_praktek_mulai')->nullable();
            # jam_praktek_selesai: jam selesai praktik dokter (format HH:MM)
            $table->time('jam_praktek_selesai')->nullable();
            # hari_praktek: hari praktik dokter (contoh: "Sen,Sel,Rab,Kam,Jum")
            $table->string('hari_praktek', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('dokter')->onDelete('cascade');
            $table->string('nomor_antrian');
            $table->date('tanggal_antrian');
            $table->time('jam_antrian');
            $table->enum('status', ['menunggu', 'dipanggil', 'diperiksa', 'selesai', 'batal'])->default('menunggu');
            $table->timestamps();
        });

        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('dokter')->onDelete('cascade');
            $table->foreignId('antrian_id')->constrained('antrian')->onDelete('cascade');
            $table->text('diagnosa')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('catatan_dokter')->nullable();
            $table->text('resep_obat')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');
            $table->date('tanggal_bayar')->nullable();
            $table->string('metode_bayar', 50)->nullable();
            $table->enum('status_bayar', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->decimal('total_biaya', 12, 2)->default(0);
            # nomor_referensi: nomor referensi pembayaran (untuk transfer bank / QRIS)
            $table->string('nomor_referensi', 100)->nullable();
            # bank: nama bank untuk pembayaran transfer (BCA, Mandiri, BNI, BRI)
            $table->string('bank', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('rekam_medis');
        Schema::dropIfExists('antrian');
        Schema::dropIfExists('dokter');
        Schema::dropIfExists('pasien');
        Schema::dropIfExists('users');
    }
};
