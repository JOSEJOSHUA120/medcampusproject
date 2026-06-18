<?php

namespace App\Models;

// Inheritance: RekamMedis mewarisi Model (Eloquent ORM)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';
    protected $fillable = ['pasien_id', 'dokter_id', 'antrian_id', 'diagnosa', 'tindakan', 'catatan_dokter', 'resep_obat'];

    // Association: Rekam Medis dimiliki oleh satu Pasien
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    // Association: Rekam Medis dimiliki oleh satu Dokter
    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    // Association: Rekam Medis berasal dari satu Antrian
    public function antrian()
    {
        return $this->belongsTo(Antrian::class);
    }

    // Association: Rekam Medis memiliki satu Pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}
