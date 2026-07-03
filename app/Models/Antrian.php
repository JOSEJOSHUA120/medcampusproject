<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $table = 'antrian';
    protected $fillable = [
        'pasien_id', 'dokter_id', 'nomor_antrian', 'tanggal_antrian', 'jam_antrian',
        'status', 'complaint', 'duration', 'pain_level', 'notes', 'room_id'
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function rekamMedis()
    {
        return $this->hasOne(RekamMedis::class);
    }
}
