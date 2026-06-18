<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    # Method: Menampilkan dashboard admin dengan ringkasan data klinik
    public function dashboard()
    {
        $totalPasien = Pasien::count();
        $pasienHariIni = Antrian::whereDate('tanggal_antrian', today())->count();
        $totalDokter = Dokter::count();
        $antrianHariIni = Antrian::whereDate('tanggal_antrian', today())->count();
        $pendapatanBulanIni = Pembayaran::where('status_bayar', 'lunas')->whereMonth('tanggal_bayar', now()->month)->sum('total_biaya');

        $dataAntrian = Antrian::whereDate('tanggal_antrian', today())
            ->with(['pasien.user', 'dokter.user'])
            ->orderBy('nomor_antrian')
            ->get();

        return view('admin.dashboard', compact(
            'totalPasien', 'pasienHariIni', 'totalDokter', 'antrianHariIni', 'pendapatanBulanIni', 'dataAntrian'
        ));
    }

    # Method: Menampilkan daftar pasien dengan fitur pencarian berdasarkan nama
    public function pasien(Request $request)
    {
        $query = Pasien::with('user');
        # Fitur pencarian pasien berdasarkan nama
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
            'alamat' => 'nullable', 'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);
        $user = User::create([
            'name' => $request->nama, 'email' => $request->email,
            'password' => Hash::make($request->password), 'role' => 'pasien',
        ]);
        Pasien::create([
            'user_id' => $user->id, 'no_telp' => $request->no_telp,
            'alamat' => $request->alamat, 'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
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
            'tanggal_lahir' => 'nullable|date', 'jenis_kelamin' => 'nullable|in:L,P',
        ]);
        $pasien->user->update(['name' => $request->nama, 'email' => $request->email]);
        if ($request->password) {
            $pasien->user->update(['password' => Hash::make($request->password)]);
        }
        $pasien->update($request->only(['no_telp', 'alamat', 'tanggal_lahir', 'jenis_kelamin']));
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
        ]);
        Dokter::create([
            'user_id' => $user->id, 'nama_dokter' => $request->nama_dokter,
            'spesialisasi' => $request->spesialisasi, 'no_telp' => $request->no_telp,
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
            'jam_praktek_mulai' => 'nullable', 'jam_praktek_selesai' => 'nullable', 'hari_praktek' => 'nullable',
        ]);
        $dokter->user->update(['name' => $request->nama_dokter, 'email' => $request->email]);
        if ($request->password) {
            $dokter->user->update(['password' => Hash::make($request->password)]);
        }
        $dokter->update($request->only(['nama_dokter', 'spesialisasi', 'no_telp', 'jam_praktek_mulai', 'jam_praktek_selesai', 'hari_praktek']));
        return redirect()->route('admin.dokter')->with('success', 'Dokter berhasil diupdate.');
    }

    public function antrian()
    {
        $data = Antrian::with(['pasien.user', 'dokter.user'])->orderBy('tanggal_antrian', 'desc')->orderBy('nomor_antrian')->get();
        return view('admin.antrian', compact('data'));
    }

    # Method: Mengupdate status antrian (menunggu, dipanggil, diperiksa, selesai, batal)
    # Fitur: Admin dapat mengontrol alur antrian pasien
    public function antrianUpdateStatus(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);
        $request->validate(['status' => 'required|in:menunggu,dipanggil,diperiksa,selesai,batal']);
        $antrian->update(['status' => $request->status]);
        return redirect()->route('admin.antrian')->with('success', 'Status antrian diupdate.');
    }

    public function rekamMedis()
    {
        $data = RekamMedis::with(['pasien.user', 'dokter.user', 'antrian'])->orderBy('created_at', 'desc')->get();
        return view('admin.rekam-medis', compact('data'));
    }

    public function pembayaran()
    {
        $data = Pembayaran::with('rekamMedis.pasien.user')->orderBy('created_at', 'desc')->get();
        return view('admin.pembayaran', compact('data'));
    }

    public function pembayaranCreate()
    {
        $rekamMedisList = RekamMedis::with('pasien.user')->whereDoesntHave('pembayaran')->orWhereHas('pembayaran', function ($q) {
            $q->where('status_bayar', 'belum_bayar');
        })->get();
        return view('admin.pembayaran-form', compact('rekamMedisList'));
    }

    public function pembayaranStore(Request $request)
    {
        $request->validate([
            'rekam_medis_id' => 'required|exists:rekam_medis,id',
            'total_biaya' => 'required|numeric|min:0',
            'metode_bayar' => 'nullable', 'tanggal_bayar' => 'nullable|date',
            'status_bayar' => 'required|in:belum_bayar,lunas',
        ]);
        Pembayaran::create($request->all());
        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function pembayaranEdit($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $rekamMedisList = RekamMedis::with('pasien.user')->get();
        return view('admin.pembayaran-form', compact('pembayaran', 'rekamMedisList'));
    }

    public function pembayaranUpdate(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $request->validate([
            'rekam_medis_id' => 'required|exists:rekam_medis,id',
            'total_biaya' => 'required|numeric|min:0',
            'metode_bayar' => 'nullable', 'tanggal_bayar' => 'nullable|date',
            'status_bayar' => 'required|in:belum_bayar,lunas',
        ]);
        $pembayaran->update($request->all());
        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil diupdate.');
    }

    public function pembayaranDestroy($id)
    {
        Pembayaran::findOrFail($id)->delete();
        return redirect()->route('admin.pembayaran')->with('success', 'Pembayaran berhasil dihapus.');
    }

    # Method: Verifikasi pembayaran oleh admin — pasien input nominal sesuai tagihan
    # Fitur: Memverifikasi jumlah bayar pasien, jika cocok update status menjadi lunas
    public function pembayaranBayar(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'jumlah_bayar' => 'required|numeric|min:0',
            'nomor_referensi' => 'nullable|string|max:100',
            'bank' => 'nullable|string|max:50',
        ]);

        # Verifikasi: jumlah bayar harus >= total biaya
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

        return redirect()->route('admin.pembayaran')->with('success', 'Terima kasih! Pembayaran berhasil diverifikasi dan dicatat.');
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
}
