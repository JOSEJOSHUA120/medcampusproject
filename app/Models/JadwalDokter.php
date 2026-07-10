<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    use HasFactory;

    protected $table = 'jadwal_dokter';

    protected $fillable = [
        'user_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'durasi_slot',
        'kuota',
        'status',
        'tanggal_terakhir',
    ];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'jadwal_dokter_id');
    }

    public function generateSlots($tanggal)
    {
        $slots = [];
        $mulai = \Carbon\Carbon::parse($this->jam_mulai);
        $selesai = \Carbon\Carbon::parse($this->jam_selesai);
        $existingBookings = $this->bookings()
            ->where('tanggal_booking', $tanggal)
            ->whereNotIn('status', ['dibatalkan','ditolak','kadaluarsa'])
            ->pluck('jam_booking')
            ->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i'))
            ->toArray();

        while ($mulai->lt($selesai)) {
            $slotStart = $mulai->format('H:i');
            $tersedia = !in_array($slotStart, $existingBookings);
            $slots[] = [
                'jam' => $slotStart,
                'tersedia' => $tersedia,
            ];
            $mulai->addMinutes($this->durasi_slot);
        }

        return $slots;
    }
}
