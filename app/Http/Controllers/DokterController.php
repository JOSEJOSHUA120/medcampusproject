<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    # Method: Dashboard dokter — ringkasan antrian dan pemeriksaan
    public function dashboard()
    {
        $dokter = auth()->user()->dokter;
        $pasienHariIni = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->count();
        $pemeriksaanHariIni = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->whereIn('status', ['diperiksa', 'selesai'])->count();
        $antrianMenunggu = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'menunggu')->count();
        $antrianDipanggil = Antrian::where('dokter_id', $dokter->id)->whereDate('tanggal_antrian', today())->where('status', 'dipanggil')->count();
        $dataAntrian = Antrian::where('dokter_id', $dokter->id)
            ->with('pasien.user')
            ->orderBy('tanggal_antrian', 'desc')
            ->orderBy('nomor_antrian')
            ->get();

        return view('dokter.dashboard', compact('pasienHariIni', 'pemeriksaanHariIni', 'antrianMenunggu', 'antrianDipanggil', 'dataAntrian'));
    }

    # Method: Menampilkan daftar antrian untuk dokter
    public function antrian()
    {
        $dokter = auth()->user()->dokter;
        $data = Antrian::where('dokter_id', $dokter->id)->with('pasien.user')
            ->orderBy('tanggal_antrian', 'desc')->orderBy('nomor_antrian')->get();
        return view('dokter.antrian', compact('data'));
    }

    # Method: Memanggil pasien (mengubah status antrian menjadi "dipanggil")
    public function panggil($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'dipanggil']);
        return redirect()->back()->with('success', 'Pasien dipanggil.');
    }

    # Method: Memulai pemeriksaan (mengubah status antrian menjadi "diperiksa")
    public function mulaiPeriksa($id)
    {
        $antrian = Antrian::findOrFail($id);
        $antrian->update(['status' => 'diperiksa']);
        return redirect()->back()->with('success', 'Pemeriksaan dimulai.');
    }

    # Method: Menampilkan rekam medis yang dibuat dokter
    public function rekamMedis()
    {
        $dokter = auth()->user()->dokter;
        $data = RekamMedis::where('dokter_id', $dokter->id)->with(['pasien.user', 'antrian'])->orderBy('created_at', 'desc')->get();
        return view('dokter.rekam-medis', compact('data'));
    }

    # Method: Form buat rekam medis baru
    public function rekamMedisCreate($antrianId)
    {
        $antrian = Antrian::with('pasien.user')->findOrFail($antrianId);
        return view('dokter.rekam-medis-form', compact('antrian'));
    }

    # Method: Menyimpan rekam medis dan membuat pembayaran otomatis
    # Fitur: Setelah rekam medis dibuat, status antrian jadi "selesai" dan pembayaran dibuat
    public function rekamMedisStore(Request $request, $antrianId)
    {
        $antrian = Antrian::findOrFail($antrianId);
        $request->validate(['diagnosa' => 'required', 'tindakan' => 'nullable', 'catatan_dokter' => 'nullable', 'resep_obat' => 'nullable']);
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
            'total_biaya' => $request->total_biaya ?? 0,
        ]);
        return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil dibuat.');
    }

    # Method: Form edit rekam medis
    public function rekamMedisEdit($id)
    {
        $rekamMedis = RekamMedis::with(['pasien.user', 'antrian'])->findOrFail($id);
        return view('dokter.rekam-medis-form', compact('rekamMedis'));
    }

    # Method: Update rekam medis
    public function rekamMedisUpdate(Request $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $request->validate(['diagnosa' => 'required', 'tindakan' => 'nullable', 'catatan_dokter' => 'nullable', 'resep_obat' => 'nullable']);
        $rekamMedis->update($request->only(['diagnosa', 'tindakan', 'catatan_dokter', 'resep_obat']));
        return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil diupdate.');
    }

    # Method: Riwayat pasien yang pernah diperiksa oleh dokter
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
}
