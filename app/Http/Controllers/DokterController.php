<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Booking;
use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\ResepObat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function dashboard()
    {
        $dokter = auth()->user()->dokter;
        $pasienHariIni = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->count();
        $pemeriksaanHariIni = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->whereIn('status', ['sedang_dilayani', 'selesai'])->count();
        $antrianMenunggu = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'menunggu')->count();
        $antrianDipanggil = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'dipanggil')->count();
        $dataAntrian = Antrian::where('dokter_id', $dokter->id)
            ->with('pasien.user')
            ->orderBy('tanggal_antrian', 'desc')
            ->orderBy('nomor_antrian')
            ->get();

        $bookingAktif = Booking::with(['pasien', 'jadwalDokter'])
            ->where('dokter_id', auth()->id())
            ->where('tanggal_booking', '>=', today())
            ->whereNotIn('status', ['dibatalkan', 'ditolak', 'tidak_hadir', 'selesai'])
            ->orderBy('tanggal_booking')
            ->orderBy('jam_booking')
            ->get();
        $bookingMenunggu = $bookingAktif->where('status', 'menunggu')->count();
        $bookingDisetujui = $bookingAktif->where('status', 'disetujui')->count();

        return view('dokter.dashboard', compact(
            'pasienHariIni', 'pemeriksaanHariIni', 'antrianMenunggu', 'antrianDipanggil', 'dataAntrian',
            'bookingAktif', 'bookingMenunggu', 'bookingDisetujui'
        ));
    }

    public function antrian()
    {
        $dokter = auth()->user()->dokter;
        $data = Antrian::where('dokter_id', $dokter->id)->with(['pasien.user', 'room'])
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
        return redirect()->back()->with('success', 'Pemeriksaan dimulai.');
    }

    public function rekamMedis()
    {
        $dokter = auth()->user()->dokter;
        $data = RekamMedis::where('dokter_id', $dokter->id)->with(['pasien.user', 'antrian', 'resepObat.obat'])->orderBy('created_at', 'desc')->get();
        return view('dokter.rekam-medis', compact('data'));
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

        $rm = RekamMedis::create([
            'pasien_id' => $antrian->pasien_id,
            'dokter_id' => $antrian->dokter_id,
            'antrian_id' => $antrian->id,
            'diagnosa' => $request->diagnosa,
            'tindakan' => $request->tindakan,
            'catatan_dokter' => $request->catatan_dokter,
            'resep_obat' => $request->resep_obat,
        ]);

        $antrian->update(['status' => 'selesai']);
        Pembayaran::create([
            'rekam_medis_id' => $rm->id,
            'status_bayar' => 'belum_bayar',
            'total_biaya' => 0,
        ]);
        return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil dibuat.');
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
