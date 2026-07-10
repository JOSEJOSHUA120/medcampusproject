<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Booking;
use App\Models\JadwalDokter;
use App\Models\Pasien;
use App\Models\User;
use App\Notifications\BookingNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    private function expirePastBookings()
    {
        Booking::whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($q) {
                $q->where('tanggal_booking', '<', today())
                  ->orWhere(function ($q2) {
                      $q2->where('tanggal_booking', '=', today())
                         ->where('jam_booking', '<', now()->format('H:i:s'));
                  });
            })
            ->update(['status' => 'kadaluarsa']);
    }
    // ===================== ADMIN: JADWAL DOKTER =====================

    public function adminJadwalDokter()
    {
        $data = JadwalDokter::with('dokter')->orderBy('hari')->orderBy('jam_mulai')->get();
        $dokters = User::where('role', 'dokter')->with('dokter')->get();
        return view('admin.jadwal-dokter', compact('data', 'dokters'));
    }

    public function adminJadwalDokterStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'durasi_slot' => 'required|integer|min:15',
            'kuota' => 'required|integer|min:0',
        ]);

        $jadwal = JadwalDokter::create($request->all());

        $dokter = User::find($request->user_id);
        if ($dokter) {
            $dokter->notify(new JadwalNotification($jadwal, 'jadwal_created'));
        }

        return redirect()->route('admin.jadwal-dokter')->with('success', 'Jadwal dokter berhasil ditambahkan.');
    }

    public function adminJadwalDokterEdit($id)
    {
        $jadwal = JadwalDokter::with('dokter')->findOrFail($id);
        $dokters = User::where('role', 'dokter')->with('dokter')->get();
        return view('admin.jadwal-dokter-form', compact('jadwal', 'dokters'));
    }

    public function adminJadwalDokterUpdate(Request $request, $id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        $oldMulai = substr($jadwal->jam_mulai, 0, 5);
        $oldSelesai = substr($jadwal->jam_selesai, 0, 5);
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'durasi_slot' => 'required|integer|min:15',
            'kuota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $jadwal->update($request->all());

        $dokter = User::find($request->user_id);
        if ($dokter) {
            $dokter->notify(new JadwalNotification($jadwal, 'jadwal_updated', [
                'jam_mulai' => $oldMulai,
                'jam_selesai' => $oldSelesai,
            ]));
        }

        return redirect()->route('admin.jadwal-dokter')->with('success', 'Jadwal dokter berhasil diupdate.');
    }

    public function adminJadwalDokterDestroy($id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        $dokter = User::find($jadwal->user_id);
        if ($dokter) {
            $dokter->notify(new JadwalNotification($jadwal, 'jadwal_deleted'));
        }
        $jadwal->delete();
        return redirect()->route('admin.jadwal-dokter')->with('success', 'Jadwal dokter berhasil dihapus.');
    }

    // ===================== ADMIN: KELOLA BOOKING =====================

    public function adminBooking()
    {
        $bookings = Booking::with(['pasien', 'dokter', 'jadwalDokter'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalBooking = $bookings->count();
        $menunggu = $bookings->where('status', 'menunggu')->count();
        $disetujui = $bookings->whereIn('status', ['disetujui', 'dipanggil', 'check_in'])->count();
        $selesai = $bookings->where('status', 'selesai')->count();
        $ditolak = $bookings->whereIn('status', ['ditolak', 'dibatalkan', 'kadaluarsa'])->count();

        return view('admin.booking', compact('bookings', 'totalBooking', 'menunggu', 'disetujui', 'selesai', 'ditolak'));
    }

    private function buatAntrianDariBooking($booking)
    {
        $pasien = Pasien::where('user_id', $booking->pasien_id)->first();
        $dokter = User::find($booking->dokter_id)?->dokter;
        if (!$pasien || !$dokter) return;

        $tanggal = $booking->tanggal_booking;
        $lastAntrian = Antrian::where('dokter_id', $dokter->id)
            ->whereDate('tanggal_antrian', $tanggal)
            ->orderBy('nomor_antrian', 'desc')
            ->first();
        $nomor = $lastAntrian ? (int)$lastAntrian->nomor_antrian + 1 : 1;

        Antrian::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'nomor_antrian' => str_pad($nomor, 3, '0', STR_PAD_LEFT),
            'tanggal_antrian' => $tanggal,
            'jam_antrian' => $booking->jam_booking,
            'status' => 'menunggu',
            'complaint' => $booking->keluhan_awal,
        ]);
    }

    public function adminBookingApprove($id)
    {
        $booking = Booking::with(['pasien', 'dokter'])->findOrFail($id);
        $booking->update(['status' => 'disetujui']);

        $this->buatAntrianDariBooking($booking);

        $booking->pasien->notify(new BookingNotification($booking, 'booking_approved'));

        if ($booking->dokter) {
            $booking->dokter->notify(new BookingNotification($booking, 'booking_created'));
        }

        return redirect()->back()->with('success', 'Booking berhasil disetujui.');
    }

    public function adminBookingReject(Request $request, $id)
    {
        $booking = Booking::with(['pasien', 'dokter'])->findOrFail($id);
        $booking->update([
            'status' => 'ditolak',
            'catatan_dokter' => $request->catatan,
        ]);

        $booking->pasien->notify(new BookingNotification($booking, 'booking_rejected', $request->catatan));

        if ($booking->dokter) {
            $booking->dokter->notify(new BookingNotification($booking, 'booking_cancelled_by_patient'));
        }

        return redirect()->back()->with('success', 'Booking ditolak.');
    }

    public function adminBookingSelesai($id)
    {
        $booking = Booking::with(['pasien', 'dokter'])->findOrFail($id);
        $booking->update(['status' => 'selesai']);

        $antrian = Antrian::where('pasien_id', $booking->pasien->pasien?->id)
            ->where('dokter_id', $booking->dokter?->dokter?->id)
            ->whereDate('tanggal_antrian', $booking->tanggal_booking)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();
        if ($antrian) {
            $antrian->update(['status' => 'selesai']);
        }

        $booking->pasien->notify(new BookingNotification($booking, 'booking_completed'));

        if ($booking->dokter) {
            $booking->dokter->notify(new BookingNotification($booking, 'booking_completed'));
        }

        return redirect()->back()->with('success', 'Booking selesai.');
    }

    // ===================== PASIEN: BOOKING =====================

    public function pasienIndex()
    {
        $this->expirePastBookings();
        $dokters = User::where('role', 'dokter')->with('dokter', 'jadwalDokter')->get();
        return view('pasien.booking', compact('dokters'));
    }

    public function pasienGetSlots(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:users,id',
            'tanggal' => 'required|date|after_or_equal:today',
        ]);

        $hari = \Carbon\Carbon::parse($request->tanggal)->locale('id')->dayName;
        $hariMapping = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];

        $jadwal = JadwalDokter::where('user_id', $request->dokter_id)
            ->where('hari', $hariMapping[$hari] ?? $hari)
            ->where('status', 'aktif')
            ->first();

        if (!$jadwal) {
            return response()->json(['slots' => [], 'jadwal_id' => null]);
        }

        $slots = $jadwal->generateSlots($request->tanggal);

        return response()->json(['slots' => $slots, 'jadwal_id' => $jadwal->id]);
    }

    public function pasienStore(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:users,id',
            'jadwal_dokter_id' => 'required|exists:jadwal_dokter,id',
            'tanggal_booking' => 'required|date|after_or_equal:today',
            'jam_booking' => 'required|date_format:H:i',
            'keluhan_awal' => 'required|string|max:500',
        ]);

        $sudahBooking = Booking::where('pasien_id', Auth::id())
            ->where('dokter_id', $request->dokter_id)
            ->where('tanggal_booking', $request->tanggal_booking)
            ->whereNotIn('status', ['dibatalkan', 'ditolak', 'kadaluarsa'])
            ->exists();

        if ($sudahBooking) {
            return redirect()->back()->with('error', 'Anda sudah booking dokter ini hari ini. Hanya 1 booking per dokter per hari.')->withInput();
        }

        $existing = Booking::where('jadwal_dokter_id', $request->jadwal_dokter_id)
            ->where('tanggal_booking', $request->tanggal_booking)
            ->where('jam_booking', $request->jam_booking)
            ->whereNotIn('status', ['dibatalkan', 'ditolak', 'kadaluarsa'])
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Slot tersebut sudah dibooking. Silakan pilih jam lain.')->withInput();
        }

        $booking = Booking::create([
            'pasien_id' => Auth::id(),
            'dokter_id' => $request->dokter_id,
            'jadwal_dokter_id' => $request->jadwal_dokter_id,
            'tanggal_booking' => $request->tanggal_booking,
            'jam_booking' => $request->jam_booking,
            'keluhan_awal' => $request->keluhan_awal,
            'status' => 'menunggu',
        ]);

        return redirect()->route('pasien.riwayat-booking')->with('success', 'Booking berhasil dibuat. Silakan tunggu konfirmasi admin.');
    }

    public function pasienRiwayat()
    {
        $this->expirePastBookings();
        $data = Booking::with(['dokter', 'jadwalDokter'])
            ->where('pasien_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pasien.riwayat-booking', compact('data'));
    }

    public function pasienBatal($id)
    {
        $booking = Booking::with(['pasien', 'dokter'])->where('pasien_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'dibatalkan']);

        if ($booking->dokter) {
            $booking->dokter->notify(new BookingNotification($booking, 'booking_cancelled_by_patient'));
        }

        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new BookingNotification($booking, 'booking_cancelled_by_patient'));
        }

        return redirect()->back()->with('success', 'Booking berhasil dibatalkan.');
    }

    // ===================== DOKTER: BOOKING =====================

    public function dokterIndex()
    {
        $bookings = Booking::with('pasien.pasien')
            ->where('dokter_id', Auth::id())
            ->whereNotIn('status', ['dibatalkan', 'ditolak', 'tidak_hadir', 'selesai', 'kadaluarsa'])
            ->orderBy('tanggal_booking')
            ->orderBy('jam_booking')
            ->get();

        return view('dokter.booking', compact('bookings'));
    }

    public function dokterApprove($id)
    {
        $booking = Booking::with(['pasien', 'dokter'])->where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'disetujui']);

        if ($booking->tanggal_booking == today()->toDateString()) {
            $this->buatAntrianDariBooking($booking);
        }

        $booking->pasien->notify(new BookingNotification($booking, 'booking_approved'));

        $adminUsers = User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new BookingNotification($booking, 'booking_created'));
        }

        return redirect()->back()->with('success', 'Booking disetujui.');
    }

    public function dokterPanggil($id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'dipanggil']);

        $pasien = Pasien::where('user_id', $booking->pasien_id)->first();
        $dokter = User::find($booking->dokter_id)?->dokter;

        if ($pasien && $dokter) {
            $antrian = Antrian::where('pasien_id', $pasien->id)
                ->where('dokter_id', $dokter->id)
                ->whereDate('tanggal_antrian', today())
                ->first();

            if ($antrian) {
                $antrian->update(['status' => 'dipanggil']);
            }
        }

        return redirect()->back()->with('success', 'Pasien dipanggil.');
    }

    public function dokterSelesai($id)
    {
        $booking = Booking::with(['pasien', 'dokter'])->where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'selesai']);

        $dokter = Auth::user()->dokter;
        $antrian = Antrian::where('pasien_id', $booking->pasien->pasien?->id)
            ->where('dokter_id', $dokter?->id)
            ->whereDate('tanggal_antrian', $booking->tanggal_booking)
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->first();
        if ($antrian) {
            $antrian->update(['status' => 'selesai']);
        }

        $booking->pasien->notify(new BookingNotification($booking, 'booking_completed'));

        return redirect()->back()->with('success', 'Booking selesai.');
    }

    // ===================== DOKTER: ALL BOOKINGS =====================

    public function dokterMulaiPeriksa($id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);

        $pasien = Pasien::where('user_id', $booking->pasien_id)->firstOrFail();
        $dokter = auth()->user()->dokter;

        $antrian = Antrian::where('pasien_id', $pasien->id)
            ->where('dokter_id', $dokter->id)
            ->whereDate('tanggal_antrian', today())
            ->first();

        if (!$antrian) {
            $lastAntrian = Antrian::where('dokter_id', $dokter->id)
                ->whereDate('tanggal_antrian', today())
                ->orderBy('nomor_antrian', 'desc')->first();
            $nomor = $lastAntrian ? (int)$lastAntrian->nomor_antrian + 1 : 1;

            $antrian = Antrian::create([
                'pasien_id' => $pasien->id,
                'dokter_id' => $dokter->id,
                'nomor_antrian' => str_pad($nomor, 3, '0', STR_PAD_LEFT),
                'tanggal_antrian' => today(),
                'jam_antrian' => $booking->jam_booking,
                'status' => 'sedang_dilayani',
                'complaint' => $booking->keluhan_awal,
            ]);
        } else {
            $antrian->update(['status' => 'sedang_dilayani']);
        }

        $booking->update(['status' => 'check_in']);

        return redirect()->route('dokter.rekam-medis.create', $antrian->id)
            ->with('success', 'Pasien siap diperiksa. Silakan buat rekam medis.');
    }

}
