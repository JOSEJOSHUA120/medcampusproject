<?php

namespace App\Models;

# Inheritance: Dokter mewarisi Model — menggunakan OOP Eloquent ORM
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'dokter';
    # fillable: field yang boleh diisi massal (Mass Assignment Protection)
    protected $fillable = ['user_id', 'nama_dokter', 'spesialisasi', 'no_telp', 'jam_praktek_mulai', 'jam_praktek_selesai', 'hari_praktek'];

    # Association: Dokter dimiliki oleh satu User (Inverse One-to-One)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    # Association: Dokter memiliki banyak Antrian (One-to-Many)
    public function antrian()
    {
        return $this->hasMany(Antrian::class);
    }

    # Association: Dokter memiliki banyak Rekam Medis (One-to-Many)
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class);
    }

    # Method: Mengecek apakah dokter tersedia pada hari dan jam saat ini
    # Menggunakan Carbon now() untuk mendapatkan waktu realtime
    public function isAvailable()
    {
        $hariMap = ['Monday' => 'Sen', 'Tuesday' => 'Sel', 'Wednesday' => 'Rab',
            'Thursday' => 'Kam', 'Friday' => 'Jum', 'Saturday' => 'Sab', 'Sunday' => 'Min'];
        $hariIni = $hariMap[now()->format('l')] ?? '';
        $hariDokter = array_map('trim', explode(',', $this->hari_praktek ?? ''));
        if (!in_array($hariIni, $hariDokter)) {
            return false; # Hari ini bukan hari praktik dokter
        }
        $jamSekarang = now()->format('H:i');
        # Bandingkan jam sekarang dengan jam praktik dokter
        return $jamSekarang >= $this->jam_praktek_mulai && $jamSekarang <= $this->jam_praktek_selesai;
    }

    # Method: Mendapatkan teks jadwal praktik dokter untuk ditampilkan di view
    public function getJadwalText()
    {
        $hari = $this->hari_praktek ? str_replace(',', ' - ', $this->hari_praktek) : 'Sen - Jum';
        $mulai = $this->jam_praktek_mulai ? date('H:i', strtotime($this->jam_praktek_mulai)) : '08:00';
        $selesai = $this->jam_praktek_selesai ? date('H:i', strtotime($this->jam_praktek_selesai)) : '16:00';
        return "$hari, $mulai - $selesai";
    }
}
