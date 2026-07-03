<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\JadwalDokter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
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

        return redirect()->route('admin.jadwal-dokter')->with('success', 'Jadwal dokter berhasil diupdate.');
    }

    public function adminJadwalDokterDestroy($id)
    {
        JadwalDokter::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal-dokter')->with('success', 'Jadwal dokter berhasil dihapus.');
    }

    // ===================== ADMIN: KELOLA BOOKING =====================

    public function adminBooking()
    {
        $data = Booking::with(['pasien', 'dokter', 'jadwalDokter'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.booking', compact('data'));
    }

    public function adminBookingApprove($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'disetujui']);
        return redirect()->back()->with('success', 'Booking berhasil disetujui.');
    }

    public function adminBookingReject(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'ditolak',
            'catatan_dokter' => $request->catatan,
        ]);
        return redirect()->back()->with('success', 'Booking ditolak.');
    }

    public function adminBookingSelesai($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'selesai']);
        return redirect()->back()->with('success', 'Booking selesai.');
    }

    // ===================== PASIEN: BOOKING =====================

    public function pasienIndex()
    {
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

        $existing = Booking::where('jadwal_dokter_id', $request->jadwal_dokter_id)
            ->where('tanggal_booking', $request->tanggal_booking)
            ->where('jam_booking', $request->jam_booking)
            ->whereNotIn('status', ['dibatalkan', 'ditolak'])
            ->exists();

        if ($existing) {
            return redirect()->back()->with('error', 'Slot tersebut sudah dibooking. Silakan pilih jam lain.')->withInput();
        }

        Booking::create([
            'pasien_id' => Auth::id(),
            'dokter_id' => $request->dokter_id,
            'jadwal_dokter_id' => $request->jadwal_dokter_id,
            'tanggal_booking' => $request->tanggal_booking,
            'jam_booking' => $request->jam_booking,
            'keluhan_awal' => $request->keluhan_awal,
            'status' => 'menunggu',
        ]);

        return redirect()->route('pasien.riwayat-booking')->with('success', 'Booking berhasil dibuat. Silakan tunggu konfirmasi.');
    }

    public function pasienRiwayat()
    {
        $data = Booking::with(['dokter', 'jadwalDokter'])
            ->where('pasien_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pasien.riwayat-booking', compact('data'));
    }

    public function pasienBatal($id)
    {
        $booking = Booking::where('pasien_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'dibatalkan']);
        return redirect()->back()->with('success', 'Booking berhasil dibatalkan.');
    }

    // ===================== DOKTER: BOOKING =====================

    public function dokterIndex()
    {
        $data = Booking::with(['pasien', 'jadwalDokter'])
            ->where('dokter_id', Auth::id())
            ->where('tanggal_booking', '>=', today())
            ->whereNotIn('status', ['dibatalkan', 'ditolak', 'tidak_hadir', 'selesai'])
            ->orderBy('tanggal_booking')
            ->orderBy('jam_booking')
            ->get();
        return view('dokter.booking', compact('data'));
    }

    public function dokterApprove($id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'disetujui']);
        return redirect()->back()->with('success', 'Booking disetujui.');
    }

    public function dokterReject(Request $request, $id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update([
            'status' => 'ditolak',
            'catatan_dokter' => $request->catatan,
        ]);
        return redirect()->back()->with('success', 'Booking ditolak.');
    }

    public function dokterCheckIn($id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'check_in']);
        return redirect()->back()->with('success', 'Pasien sudah check-in.');
    }

    public function dokterSelesai($id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'selesai']);
        return redirect()->back()->with('success', 'Booking selesai.');
    }

    public function dokterTidakHadir($id)
    {
        $booking = Booking::where('dokter_id', Auth::id())->findOrFail($id);
        $booking->update(['status' => 'tidak_hadir']);
        return redirect()->back()->with('success', 'Pasien ditandai tidak hadir.');
    }

    // ===================== DOKTER: ALL BOOKINGS =====================

    public function dokterSemuaBooking()
    {
        $data = Booking::with(['pasien', 'jadwalDokter'])
            ->where('dokter_id', Auth::id())
            ->orderBy('tanggal_booking', 'desc')
            ->orderBy('jam_booking')
            ->get();
        return view('dokter.semua-booking', compact('data'));
    }
}
