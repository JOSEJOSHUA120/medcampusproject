<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Booking;
use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\Room;
use App\Models\User;
use App\Notifications\AppointmentNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalPasien = Pasien::count();
        $pasienHariIni = Antrian::whereDate('tanggal_antrian', today())->count();
        $totalDokter = Dokter::count();
        $totalObat = Obat::count();
        $antrianHariIni = Antrian::whereDate('tanggal_antrian', today())->count();
        $pendapatanBulanIni = Pembayaran::where('status_bayar', 'lunas')->whereMonth('tanggal_bayar', now()->month)->sum('total_biaya');

        $dataAntrian = Antrian::whereDate('tanggal_antrian', today())
            ->with(['pasien.user', 'dokter.user'])
            ->orderBy('nomor_antrian')
            ->get();

        $totalBooking = Booking::count();
        $bookingMenunggu = Booking::where('status', 'menunggu')->count();
        $bookingHariIni = Booking::whereDate('tanggal_booking', today())->count();

        return view('admin.dashboard', compact(
            'totalPasien', 'pasienHariIni', 'totalDokter', 'totalObat', 'antrianHariIni', 'pendapatanBulanIni', 'dataAntrian',
            'totalBooking', 'bookingMenunggu', 'bookingHariIni'
        ));
    }

    public function pasien(Request $request)
    {
        $query = Pasien::with('user');
        if ($request->filled('search')) {
            $query->searchByName($request->search);
        }
        $data = $query->orderBy('created_at', 'desc')->get();
        return view('admin.pasien', compact('data'));
    }

    public function pasienCreate()
    {
        return view('admin.pasien-form');
    }

    public function pasienStore(Request $request)
    {
        $request->validate([
            'nama' => 'required', 'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6', 'no_telp' => 'nullable',
            'alamat' => 'nullable', 'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);
        $user = User::create([
            'name' => $request->nama, 'email' => $request->email,
            'password' => Hash::make($request->password), 'role' => 'pasien',
            'foto' => 'https://i.pravatar.cc/300?u=' . urlencode($request->email),
        ]);
        Pasien::create([
            'user_id' => $user->id, 'no_telp' => $request->no_telp,
            'alamat' => $request->alamat, 'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'foto' => 'https://i.pravatar.cc/300?u=' . urlencode($request->email),
        ]);
        return redirect()->route('admin.pasien')->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function pasienEdit($id)
    {
        $pasien = Pasien::with('user')->findOrFail($id);
        return view('admin.pasien-form', compact('pasien'));
    }

    public function pasienUpdate(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);
        $request->validate([
            'nama' => 'required', 'email' => 'required|email|unique:users,email,' . $pasien->user_id,
            'no_telp' => 'nullable', 'alamat' => 'nullable',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);
        $pasien->user->update(['name' => $request->nama, 'email' => $request->email]);
        if ($request->password) {
            $pasien->user->update(['password' => Hash::make($request->password)]);
        }
        $pasien->update($request->only(['no_telp', 'alamat', 'tanggal_lahir', 'tempat_lahir', 'jenis_kelamin']));
        return redirect()->route('admin.pasien')->with('success', 'Pasien berhasil diupdate.');
    }

    public function pasienDestroy($id)
    {
        $pasien = Pasien::with('user')->findOrFail($id);
        $pasien->user->delete();
        $pasien->delete();
        return redirect()->route('admin.pasien')->with('success', 'Pasien berhasil dihapus.');
    }

    public function dokter()
    {
        $data = Dokter::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.dokter', compact('data'));
    }

    public function dokterCreate()
    {
        return view('admin.dokter-form');
    }

    public function dokterStore(Request $request)
    {
        $request->validate([
            'nama_dokter' => 'required', 'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6', 'spesialisasi' => 'required', 'no_telp' => 'nullable',
        ]);
        $user = User::create([
            'name' => $request->nama_dokter, 'email' => $request->email,
            'password' => Hash::make($request->password), 'role' => 'dokter',
            'foto' => 'https://i.pravatar.cc/300?u=' . urlencode($request->email),
        ]);
        Dokter::create([
            'user_id' => $user->id, 'nama_dokter' => $request->nama_dokter,
            'spesialisasi' => $request->spesialisasi, 'no_telp' => $request->no_telp,
            'foto' => 'https://i.pravatar.cc/300?u=' . urlencode($request->email),
        ]);
        return redirect()->route('admin.dokter')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function dokterEdit($id)
    {
        $dokter = Dokter::with('user')->findOrFail($id);
        return view('admin.dokter-form', compact('dokter'));
    }

    public function dokterUpdate(Request $request, $id)
    {
        $dokter = Dokter::findOrFail($id);
        $request->validate([
            'nama_dokter' => 'required', 'email' => 'required|email|unique:users,email,' . $dokter->user_id,
            'spesialisasi' => 'required', 'no_telp' => 'nullable',
        ]);
        $dokter->user->update(['name' => $request->nama_dokter, 'email' => $request->email]);
        if ($request->password) {
            $dokter->user->update(['password' => Hash::make($request->password)]);
        }
        $dokter->update($request->only(['nama_dokter', 'spesialisasi', 'no_telp']));
        return redirect()->route('admin.dokter')->with('success', 'Dokter berhasil diupdate.');
    }

    public function dokterDestroy($id)
    {
        $dokter = Dokter::with('user')->findOrFail($id);
        $dokter->user->delete();
        $dokter->delete();
        return redirect()->route('admin.dokter')->with('success', 'Dokter berhasil dihapus.');
    }

    public function rekamMedis()
    {
        $data = RekamMedis::with(['pasien.user', 'dokter.user', 'antrian', 'resepObat.obat'])->orderBy('created_at', 'desc')->get();
        return view('admin.rekam-medis', compact('data'));
    }

    public function obat()
    {
        $data = Obat::orderBy('nama_obat')->get();
        return view('admin.obat', compact('data'));
    }

    public function obatCreate()
    {
        return view('admin.obat-form');
    }

    public function obatStore(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required',
            'keterangan' => 'nullable',
        ]);
        Obat::create($request->all());
        return redirect()->route('admin.obat')->with('success', 'Obat berhasil ditambahkan.');
    }

    public function obatEdit($id)
    {
        $obat = Obat::findOrFail($id);
        return view('admin.obat-form', compact('obat'));
    }

    public function obatUpdate(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);
        $request->validate([
            'nama_obat' => 'required',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required',
            'keterangan' => 'nullable',
        ]);
        $obat->update($request->all());
        return redirect()->route('admin.obat')->with('success', 'Obat berhasil diupdate.');
    }

    public function obatDestroy($id)
    {
        Obat::findOrFail($id)->delete();
        return redirect()->route('admin.obat')->with('success', 'Obat berhasil dihapus.');
    }

    public function pembayaran()
    {
        $data = Pembayaran::with('rekamMedis.pasien.user', 'rekamMedis.resepObat.obat')->orderBy('created_at', 'desc')->get();
        return view('admin.pembayaran', compact('data'));
    }

    public function pembayaranCreate()
    {
        $rekamMedisList = RekamMedis::with('pasien.user', 'resepObat.obat')->whereDoesntHave('pembayaran')->orWhereHas('pembayaran', function ($q) {
            $q->where('status_bayar', 'belum_bayar');
        })->get();
        return view('admin.pembayaran-form', compact('rekamMedisList'));
    }

    public function pembayaranStore(Request $request)
    {
        $request->validate([
            'rekam_medis_id' => 'required|exists:rekam_medis,id',
            'total_biaya' => 'required|numeric|min:0',
            'metode_bayar' => 'nullable',
            'tanggal_bayar' => 'nullable|date',
            'status_bayar' => 'required|in:belum_bayar,lunas',
            'bank' => 'nullable|string|max:50',
            'nomor_referensi' => 'nullable|string|max:100',
        ]);
        Pembayaran::create($request->all());
        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function pembayaranEdit($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $rekamMedisList = RekamMedis::with('pasien.user', 'resepObat.obat')->get();
        return view('admin.pembayaran-form', compact('pembayaran', 'rekamMedisList'));
    }

    public function pembayaranUpdate(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $request->validate([
            'rekam_medis_id' => 'required|exists:rekam_medis,id',
            'total_biaya' => 'required|numeric|min:0',
            'metode_bayar' => 'nullable',
            'tanggal_bayar' => 'nullable|date',
            'status_bayar' => 'required|in:belum_bayar,lunas',
            'bank' => 'nullable|string|max:50',
            'nomor_referensi' => 'nullable|string|max:100',
        ]);
        $pembayaran->update($request->all());
        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil diupdate.');
    }

    public function pembayaranDestroy($id)
    {
        Pembayaran::findOrFail($id)->delete();
        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil dihapus.');
    }

    public function pembayaranBayar(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'jumlah_bayar' => 'required|numeric|min:0',
            'nomor_referensi' => 'nullable|string|max:100',
            'bank' => 'nullable|string|max:50',
        ]);

        if ($request->jumlah_bayar < $pembayaran->total_biaya) {
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total tagihan.')->withInput();
        }

        $pembayaran->update([
            'status_bayar' => 'lunas',
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => $request->metode_bayar,
            'nomor_referensi' => $request->nomor_referensi,
            'bank' => $request->bank,
        ]);

        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil diverifikasi dan dicatat.');
    }

    public function pembayaranGenerate($id)
    {
        $pembayaran = Pembayaran::with('rekamMedis.pasien.user', 'rekamMedis.resepObat.obat')->findOrFail($id);
        return view('admin.pembayaran-generate', compact('pembayaran'));
    }

    public function pembayaranGenerateStore(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $request->validate([
            'metode_bayar' => 'required|in:qris,transfer',
            'bank' => 'nullable|string|max:50',
            'nomor_referensi' => 'required|string|max:100',
        ]);

        $pembayaran->update([
            'metode_bayar' => $request->metode_bayar,
            'bank' => $request->bank,
            'nomor_referensi' => $request->nomor_referensi,
        ]);

        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil digenerate dengan metode ' . strtoupper($request->metode_bayar) . '.');
    }

    public function laporan(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggalAkhir = $request->tanggal_akhir ?? now()->endOfMonth()->toDateString();
        $data = Pembayaran::whereBetween('tanggal_bayar', [$tanggalAwal, $tanggalAkhir])
            ->with('rekamMedis.pasien.user')->orderBy('tanggal_bayar')->get();
        $total = $data->sum('total_biaya');
        return view('admin.laporan', compact('data', 'total', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function laporanPdf(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal ?? now()->startOfMonth()->toDateString();
        $tanggalAkhir = $request->tanggal_akhir ?? now()->endOfMonth()->toDateString();
        $data = Pembayaran::whereBetween('tanggal_bayar', [$tanggalAwal, $tanggalAkhir])
            ->with('rekamMedis.pasien.user')->orderBy('tanggal_bayar')->get();
        $total = $data->sum('total_biaya');
        $pdf = Pdf::loadView('admin.laporan-pdf', compact('data', 'total', 'tanggalAwal', 'tanggalAkhir'));
        return $pdf->download("laporan-pembayaran-$tanggalAwal-$tanggalAkhir.pdf");
    }

    public function kelolaAntrian()
    {
        $data = Antrian::with(['pasien.user', 'dokter.user', 'room'])
            ->orderBy('created_at', 'desc')
            ->get();
        $rooms = Room::where('status', 'free')->get();
        return view('admin.kelola-antrian', compact('data', 'rooms'));
    }

    public function antrianPanggil(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'dipanggil']);

        $room = Room::findOrFail($request->input('room_id'));
        $room->update(['status' => 'occupied']);

        $antrian->pasien->user->notify(new AppointmentNotification($antrian, 'called', $room->room_number));

        return redirect()->back()->with('success', 'Pasien berhasil dipanggil.');
    }

    public function antrianUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dipanggil,dikonfirmasi,sedang_dilayani,selesai,dibatalkan',
        ]);

        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => $request->status]);

        if ($request->status === 'selesai' && $antrian->room) {
            $antrian->room->update(['status' => 'free']);
        }

        if ($request->status === 'dibatalkan') {
            $antrian->pasien->user->notify(new AppointmentNotification($antrian, 'cancelled'));
        }

        if ($request->status === 'selesai') {
            $antrian->pasien->user->notify(new AppointmentNotification($antrian, 'completed'));
        }

        if ($request->status === 'dikonfirmasi') {
            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new AppointmentNotification($antrian, 'patient_confirmed'));
            }
            $antrian->dokter->user->notify(new AppointmentNotification($antrian, 'patient_confirmed'));
        }

        return redirect()->back()->with('success', 'Status antrian berhasil diupdate.');
    }

    public function rooms()
    {
        $data = Room::orderBy('room_number')->get();
        return view('admin.rooms', compact('data'));
    }

    public function roomsCreate()
    {
        return view('admin.rooms-form');
    }

    public function roomsStore(Request $request)
    {
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number',
            'description' => 'nullable',
        ]);
        Room::create($request->only(['room_number', 'description']));
        return redirect()->route('admin.rooms')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function roomsEdit($id)
    {
        $room = Room::findOrFail($id);
        return view('admin.rooms-form', compact('room'));
    }

    public function roomsUpdate(Request $request, $id)
    {
        $room = Room::findOrFail($id);
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $id,
            'description' => 'nullable',
        ]);
        $room->update($request->only(['room_number', 'description']));
        return redirect()->route('admin.rooms')->with('success', 'Ruangan berhasil diupdate.');
    }

    public function roomsDestroy($id)
    {
        Room::findOrFail($id)->delete();
        return redirect()->route('admin.rooms')->with('success', 'Ruangan berhasil dihapus.');
    }
}
