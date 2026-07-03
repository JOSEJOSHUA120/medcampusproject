<?php

namespace App\Models;

# Inheritance: Pembayaran mewarisi Model (Eloquent ORM)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';
    # fillable: field yang boleh diisi massal
    protected $fillable = ['rekam_medis_id', 'tanggal_bayar', 'metode_bayar', 'status_bayar', 'total_biaya', 'nomor_referensi', 'bank'];

    # Association: Pembayaran dimiliki oleh satu Rekam Medis
    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class);
    }
}
