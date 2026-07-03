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
    protected $fillable = ['user_id', 'nama_dokter', 'spesialisasi', 'no_telp'];

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

    public function isAvailable()
    {
        return true;
    }

    public function getJadwalText()
    {
        return 'Tersedia';
    }
}
