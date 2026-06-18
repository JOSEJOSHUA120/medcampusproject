<?php

namespace App\Models;

// Inheritance: Antrian mewarisi Model (Eloquent ORM Base Class)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian';
    protected $fillable = ['pasien_id', 'dokter_id', 'nomor_antrian', 'tanggal_antrian', 'jam_antrian', 'status'];

    // Association: Many-to-One — Antrian milik satu Pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    // Association: Many-to-One — Antrian milik satu Dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    // Association: One-to-One — Antrian memiliki satu Rekam Medis (bisa null jika belum diperiksa)
    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class);
    }
}
