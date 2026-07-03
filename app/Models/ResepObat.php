<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepObat extends Model
{
    use HasFactory;

    protected $table = 'resep_obat';
    protected $fillable = ['rekam_medis_id', 'obat_id', 'jumlah', 'harga_satuan', 'subtotal'];

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
