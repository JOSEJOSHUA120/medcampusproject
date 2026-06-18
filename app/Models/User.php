<?php

namespace App\Models;

// Inheritance: User mewarisi (extends) dari Authenticatable
// Authenticatable mewarisi dari Model -> mewarisi OOP Laravel Eloquent ORM
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Trait: Reusable code (polymorphism) — HasApiTokens untuk Sanctum, HasFactory untuk seeding, Notifiable untuk email
    use HasApiTokens, HasFactory, Notifiable;

    // Encapsulation: Mass-assignment protection — hanya field ini yang bisa diisi via create()
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // Encapsulation: Field ini akan disembunyikan saat model di-serialize ke JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Encapsulation: Type casting — otomatis konversi tipe data
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Association (ORM): One-to-One — User memiliki satu data Pasien
    public function pasien()
    {
        return $this->hasOne(Pasien::class);
    }

    // Association (ORM): One-to-One — User memiliki satu data Dokter
    public function dokter()
    {
        return $this->hasOne(Dokter::class);
    }
}
