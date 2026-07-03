<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\ResepObat;
use Illuminate\Http\Request;

class DokterController extends Controller
{
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

    public function antrian()
    {
        $dokter = auth()->user()->dokter;
        $data = Antrian::where('dokter_id', $dokter->id)->with('pasien.user')
            ->orderBy('tanggal_antrian', 'desc')->orderBy('nomor_antrian')->get();
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
        $antrian->update(['status' => 'diperiksa']);
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
        $daftarObat = Obat::orderBy('nama_obat')->get();
        return view('dokter.rekam-medis-form', compact('antrian', 'daftarObat'));
    }

    public function rekamMedisStore(Request $request, $antrianId)
    {
        $antrian = Antrian::findOrFail($antrianId);
        $request->validate([
            'diagnosa' => 'required',
            'tindakan' => 'nullable',
            'catatan_dokter' => 'nullable',
            'obat_id' => 'nullable|array',
            'obat_id.*' => 'exists:obat,id',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'integer|min:1',
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

        $totalBiaya = 0;
        if ($request->obat_id) {
            foreach ($request->obat_id as $i => $obatId) {
                $obat = Obat::findOrFail($obatId);
                $jumlah = $request->jumlah[$i] ?? 1;
                $subtotal = $obat->harga * $jumlah;
                ResepObat::create([
                    'rekam_medis_id' => $rm->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $obat->harga,
                    'subtotal' => $subtotal,
                ]);
                $totalBiaya += $subtotal;
            }
        }

        $antrian->update(['status' => 'selesai']);
        Pembayaran::create([
            'rekam_medis_id' => $rm->id,
            'status_bayar' => 'belum_bayar',
            'total_biaya' => $totalBiaya,
        ]);
        return redirect()->route('dokter.rekam-medis')->with('success', 'Rekam medis berhasil dibuat.');
    }

    public function rekamMedisEdit($id)
    {
        $rekamMedis = RekamMedis::with(['pasien.user', 'antrian', 'resepObat.obat'])->findOrFail($id);
        $daftarObat = Obat::orderBy('nama_obat')->get();
        return view('dokter.rekam-medis-form', compact('rekamMedis', 'daftarObat'));
    }

    public function rekamMedisUpdate(Request $request, $id)
    {
        $rekamMedis = RekamMedis::findOrFail($id);
        $request->validate([
            'diagnosa' => 'required',
            'tindakan' => 'nullable',
            'catatan_dokter' => 'nullable',
            'obat_id' => 'nullable|array',
            'obat_id.*' => 'exists:obat,id',
            'jumlah' => 'nullable|array',
            'jumlah.*' => 'integer|min:1',
        ]);
        $rekamMedis->update($request->only(['diagnosa', 'tindakan', 'catatan_dokter', 'resep_obat']));

        $rekamMedis->resepObat()->delete();
        $totalBiaya = 0;
        if ($request->obat_id) {
            foreach ($request->obat_id as $i => $obatId) {
                $obat = Obat::findOrFail($obatId);
                $jumlah = $request->jumlah[$i] ?? 1;
                $subtotal = $obat->harga * $jumlah;
                ResepObat::create([
                    'rekam_medis_id' => $rekamMedis->id,
                    'obat_id' => $obat->id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $obat->harga,
                    'subtotal' => $subtotal,
                ]);
                $totalBiaya += $subtotal;
            }
        }

        if ($rekamMedis->pembayaran) {
            $rekamMedis->pembayaran->update(['total_biaya' => $totalBiaya]);
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
}
