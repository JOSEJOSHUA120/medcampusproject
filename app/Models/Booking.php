<?php

/*
| =============================================================================
| NAMESPACE
| Model-model ditempatkan di App\Models untuk organisasi yang rapi.
| =============================================================================
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/*
| =============================================================================
| ELOQUENT MODEL (Active Record Pattern)
| ----------------------------------------
| "class Booking extends Model" — Setiap tabel database punya 1 Model class
| yang mewakilinya. Ini adalah implementasi ACTIVE RECORD pattern:
| - Model mewakili baris data (instance = 1 record)
| - Model juga mewakili tabel (static method = query)
| - Model otomatis punya CRUD: create(), update(), delete(), find(), dll
|
| TRAIT HasFactory
| Memberikan kemampuan factory untuk seeding data testing.
| =============================================================================
*/
class Booking extends Model
{
    use HasFactory;

    /*
    | MASS ASSIGNMENT PROTECTION
    | $fillable — daftar kolom yang boleh diisi via create()/update()
    | secara massal. Ini adalah fitur KEAMANAN untuk mencegah Mass
    | Assignment Vulnerability.
    */
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

    /*
    | =========================================================================
    | RELATIONSHIP METHODS (Object Composition)
    | ------------------------------------------
    | Setiap method mengembalikan object relasi (belongsTo, hasMany, dll).
    | Ini adalah Object Composition: Booking "memiliki" relasi ke User
    | (pasien & dokter) dan JadwalDokter.
    |
    | "belongsTo" = Many-to-One (child ke parent).
    | Parameter: (class tujuan, foreign_key, owner_key).
    |
    | Ketika kita panggil $booking->pasien, Laravel mengeksekusi relasi
    | dan mengembalikan object User yang terkait (lazy loading).
    | Atau bisa di-load sekaligus dengan ->with('pasien') (eager loading).
    | =========================================================================
    */
    public function pasien()
    {
        /*
        | Booking.pasien_id → Users.id
        | Satu booking dimiliki oleh satu user (pasien).
        | Foreign key 'pasien_id' di tabel bookings merujuk ke 'id' di users.
        */
        return $this->belongsTo(User::class, 'pasien_id');
    }

    public function dokter()
    {
        /*
        | Booking.dokter_id → Users.id
        | Satu booking dimiliki oleh satu user (dokter).
        */
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function jadwalDokter()
    {
        /*
        | Booking.jadwal_dokter_id → JadwalDokter.id
        | Satu booking terkait dengan satu jadwal dokter.
        */
        return $this->belongsTo(JadwalDokter::class, 'jadwal_dokter_id');
    }
}
