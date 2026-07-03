<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'jadwal_dokter_id',
        'tanggal_booking',
        'jam_booking',
        'keluhan_awal',
        'status',
        'catatan_dokter',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function jadwalDokter()
    {
        return $this->belongsTo(JadwalDokter::class, 'jadwal_dokter_id');
    }
}
