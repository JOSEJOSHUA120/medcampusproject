<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Booking;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\RekamMedis;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DokterController extends Controller
{
    public function dashboard()
    {
        $dokter = auth()->user()->dokter;
        $pasienHariIni = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->count();
        $selesaiDiperiksa = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'selesai')->count();
        $antrianMenunggu = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'menunggu')->count();
        $antrianDipanggil = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'dipanggil')->count();
        $dataAntrian = Antrian::where('dokter_id', $dokter->id)
            ->with('pasien.user')
            ->orderBy('tanggal_antrian', 'desc')
            ->orderBy('nomor_antrian')
            ->get();

        $statusAktif = ['menunggu', 'disetujui', 'dipanggil', 'check_in'];

        $userId = auth()->id();

        $bookingAktif = Booking::with(['pasien', 'jadwalDokter'])
            ->where('dokter_id', $userId)
            ->where('tanggal_booking', '>=', today())
            ->whereIn('status', $statusAktif)
            ->orderBy('tanggal_booking')
            ->orderBy('jam_booking')
            ->get();
        $bookingMenunggu = $bookingAktif->where('status', 'menunggu')->count();
        $bookingDisetujui = $bookingAktif->where('status', 'disetujui')->count();
        $bookingHariIni = Booking::where('dokter_id', $userId)
            ->where('tanggal_booking', today()->format('Y-m-d'))
            ->whereIn('status', $statusAktif)
            ->count();

        return view('dokter.dashboard', compact(
            'pasienHariIni', 'selesaiDiperiksa', 'antrianMenunggu', 'antrianDipanggil', 'dataAntrian',
            'bookingAktif', 'bookingMenunggu', 'bookingDisetujui', 'bookingHariIni'
        ));
    }

    public function antrian()
    {
        $dokter = auth()->user()->dokter;
        $data = Antrian::where('dokter_id', $dokter->id)->with(['pasien.user', 'room', 'rekamMedis'])
            ->orderBy('created_at', 'desc')->get();
        return view('dokter.antrian', compact('data'));
    }

    public function panggil($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'dipanggil']);
        return redirect()->back()->with('success', 'Pasien dipanggil.');
    }

    public function mulaiPeriksa($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'sedang_dilayani']);
        return redirect()->route('dokter.rekam-medis.create', $antrian->id)
            ->with('success', 'Pemeriksaan dimulai. Silakan isi rekam medis.');
    }

    public function rekamMedis()
    {
        $dokter = auth()->user()->dokter;
        $data = RekamMedis::where('dokter_id', $dokter->id)->with(['pasien.user', 'antrian', 'resepObat.obat'])->orderBy('created_at', 'desc')->paginate(10);
        $pasien = Pasien::with('user')
            ->whereHas('antrian', function ($q) use ($dokter) {
                $q->where('dokter_id', $dokter->id);
            })
            ->orWhereDoesntHave('antrian')
            ->orderBy('id', 'desc')
            ->get();
        return view('dokter.rekam-medis', compact('data', 'pasien'));
    }

    public function rekamMedisPilihPasien()
    {
        $dokter = auth()->user()->dokter;
        $pasien = Pasien::with('user')
            ->whereHas('antrian', function ($q) use ($dokter) {
                $q->where('dokter_id', $dokter->id);
            })
            ->orWhereDoesntHave('antrian')
            ->orderBy('id', 'desc')
            ->get();
        return view('dokter.rekam-medis-pilih-pasien', compact('pasien'));
    }

    public function rekamMedisPilihPasienStore(Request $request)
    {
        $request->validate(['pasien_id' => 'required|exists:pasien,id']);

        $dokter = auth()->user()->dokter;
        $pasien = Pasien::findOrFail($request->pasien_id);

        $lastAntrian = Antrian::where('dokter_id', $dokter->id)
            ->whereDate('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'desc')
            ->first();
        $nomor = $lastAntrian ? (int)$lastAntrian->nomor_antrian + 1 : 1;

        $antrian = Antrian::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'nomor_antrian' => str_pad($nomor, 3, '0', STR_PAD_LEFT),
            'tanggal_antrian' => today(),
            'jam_antrian' => now()->format('H:i:s'),
            'status' => 'sedang_dilayani',
        ]);

        return redirect()->route('dokter.rekam-medis.create', $antrian->id)
            ->with('success', 'Silakan isi rekam medis untuk ' . ($pasien->user->name ?? 'Pasien'));
    }

    public function rekamMedisTambahPasienBaru(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'jenis_kelamin' => 'nullable|in:L,P',
        ]);

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'pasien',
        ]);

        $pasien = Pasien::create([
            'user_id' => $user->id,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir' => $request->tempat_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
        ]);

        $dokter = auth()->user()->dokter;
        $lastAntrian = Antrian::where('dokter_id', $dokter->id)
            ->whereDate('tanggal_antrian', today())
            ->orderBy('nomor_antrian', 'desc')
            ->first();
        $nomor = $lastAntrian ? (int)$lastAntrian->nomor_antrian + 1 : 1;

        $antrian = Antrian::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $dokter->id,
            'nomor_antrian' => str_pad($nomor, 3, '0', STR_PAD_LEFT),
            'tanggal_antrian' => today(),
            'jam_antrian' => now()->format('H:i:s'),
            'status' => 'sedang_dilayani',
        ]);

        return redirect()->route('dokter.rekam-medis.create', $antrian->id)
            ->with('success', 'Pasien baru berhasil ditambahkan. Silakan isi rekam medis.');
    }

    public function rekamMedisCreate($antrianId)
    {
        $antrian = Antrian::with('pasien.user')->findOrFail($antrianId);
        return view('dokter.rekam-medis-form', compact('antrian'));
    }

    public function rekamMedisStore(Request $request, $antrianId)
    {
        $antrian = Antrian::findOrFail($antrianId);
        $request->validate([
            'diagnosa' => 'required',
            'tindakan' => 'nullable',
            'catatan_dokter' => 'nullable',
            'resep_obat' => 'nullable|string',
        ]);

        if ($antrian->rekamMedis) {
            $rm = $antrian->rekamMedis;
            $rm->update($request->only(['diagnosa', 'tindakan', 'catatan_dokter', 'resep_obat']));
        } else {
            $rm = RekamMedis::create([
                'pasien_id' => $antrian->pasien_id,
                'dokter_id' => $antrian->dokter_id,
                'antrian_id' => $antrian->id,
                'diagnosa' => $request->diagnosa,
                'tindakan' => $request->tindakan,
                'catatan_dokter' => $request->catatan_dokter,
                'resep_obat' => $request->resep_obat,
            ]);
        }

        if ($request->input('action') === 'simpan_selesai') {
            $antrian->update(['status' => 'selesai']);
            if (!$rm->pembayaran()->exists()) {
                Pembayaran::create([
                    'rekam_medis_id' => $rm->id,
                    'status_bayar' => 'belum_bayar',
                    'total_biaya' => 0,
                ]);
            }
            return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil dibuat.');
        }

        if ($request->input('action') === 'simpan') {
            return redirect()->route('dokter.riwayat-pasien')->with('success', 'Rekam medis berhasil disimpan.');
        }

        return redirect()->route('dokter.antrian')->with('success', 'Rekam medis berhasil diisi.');
    }

    public function rekamMedisEdit($id)
    {
        $rekamMedis = RekamMedis::with(['pasien.user', 'antrian', 'resepObat.obat'])->findOrFail($id);
        return view('dokter.rekam-medis-form', compact('rekamMedis'));
    }

    public function rekamMedisUpdate(Request $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $request->validate([
            'diagnosa' => 'required',
            'tindakan' => 'nullable',
            'catatan_dokter' => 'nullable',
            'resep_obat' => 'nullable|string',
        ]);
        $rekamMedis->update($request->only(['diagnosa', 'tindakan', 'catatan_dokter', 'resep_obat']));

        if ($request->input('action') === 'simpan_selesai') {
            $antrian = $rekamMedis->antrian;
            if ($antrian && $antrian->status !== 'selesai') {
                $antrian->update(['status' => 'selesai']);
            }
            if (!$rekamMedis->pembayaran()->exists()) {
                Pembayaran::create([
                    'rekam_medis_id' => $rekamMedis->id,
                    'status_bayar' => 'belum_bayar',
                    'total_biaya' => 0,
                ]);
            }
            return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil diselesaikan.');
        }

        if ($request->input('action') === 'simpan') {
            return redirect()->route('dokter.riwayat-pasien')->with('success', 'Rekam medis berhasil diupdate.');
        }

        return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil diupdate.');
    }

    public function riwayatPasien()
    {
        $dokter = auth()->user()->dokter;
        $pasien = Pasien::whereHas('rekamMedis', function ($q) use ($dokter) {
            $q->where('dokter_id', $dokter->id);
        })->with(['user', 'rekamMedis' => function ($q) use ($dokter) {
            $q->where('dokter_id', $dokter->id)->orderBy('created_at', 'desc');
        }])->get();
        return view('dokter.riwayat-pasien', compact('pasien'));
    }

    public function antrianDownloadPdf($id)
    {
        $dokter = auth()->user()->dokter;
        $antrian = Antrian::where('dokter_id', $dokter->id)->findOrFail($id);
        $pdf = Pdf::loadView('dokter.antrian-pdf', compact('antrian'));
        return $pdf->download("data-pasien-{$antrian->nomor_antrian}.pdf");
    }
}
