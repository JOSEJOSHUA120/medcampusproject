<?php

namespace App\Models;

# Inheritance: Pasien mewarisi class Model (Eloquent ORM)
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    use HasFactory;

    # Encapsulation: Menentukan tabel yang digunakan (jika berbeda dari default plural)
    protected $table = 'pasien';

    # Encapsulation: Field yang boleh diisi massal (Mass Assignment Protection)
    protected $fillable = ['user_id', 'no_telp', 'alamat', 'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin'];

    # Association (ORM): Inverse One-to-One — Pasien dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    # Association (ORM): One-to-Many — Pasien memiliki banyak Antrian
    public function antrian()
    {
        return $this->hasMany(Antrian::class);
    }

    # Association (ORM): One-to-Many — Pasien memiliki banyak Rekam Medis
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class);
    }

    # Scope: Mencari pasien berdasarkan nama (digunakan di fitur pencarian)
    # Mencari melalui relasi User (tabel users.name)
    public function scopeSearchByName($query, $search)
    {
        return $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }
}
