<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    # Method: Dashboard pasien — menampilkan status antrian, kunjungan, pembayaran
    public function dashboard()
    {
        $pasien = auth()->user()->pasien;
        $antrianSekarang = Antrian::where('pasien_id', $pasien->id)->whereDate('tanggal_antrian', today())->whereNotIn('status', ['batal', 'selesai'])->orderBy('created_at', 'desc')->first();
        $jumlahKunjungan = Antrian::where('pasien_id', $pasien->id)->count();
        $jumlahRekamMedis = RekamMedis::where('pasien_id', $pasien->id)->count();
        $pembayaranTerakhir = Pembayaran::whereHas('rekamMedis', function ($q) use ($pasien) {
            $q->where('pasien_id', $pasien->id);
        })->latest()->first();

        return view('pasien.dashboard', compact('antrianSekarang', 'jumlahKunjungan', 'jumlahRekamMedis', 'pembayaranTerakhir'));
    }

    # Method: Halaman ambil antrian — menampilkan daftar dokter beserta jadwal dan ketersediaan
    # Fitur: Pasien dapat booking jadwal dengan memilih dokter
    # Fitur: Menampilkan status ketersediaan dokter berdasarkan waktu realtime
    public function ambilAntrian()
    {
        $dokter = Dokter::with('user')->get();
        return view('pasien.ambil-antrian', compact('dokter'));
    }

    # Method: Menyimpan antrian baru yang diambil pasien
    public function storeAntrian(Request $request)
    {
        $request->validate(['dokter_id' => 'required|exists:dokter,id']);
        $pasien = auth()->user()->pasien;

        $today = today()->toDateString();
        $lastAntrian = Antrian::where('dokter_id', $request->dokter_id)->whereDate('tanggal_antrian', $today)->orderBy('nomor_antrian', 'desc')->first();
        $nomor = $lastAntrian ? (int)$lastAntrian->nomor_antrian + 1 : 1;

        Antrian::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $request->dokter_id,
            'nomor_antrian' => str_pad($nomor, 3, '0', STR_PAD_LEFT),
            'tanggal_antrian' => $today,
            'jam_antrian' => now()->format('H:i:s'),
            'status' => 'menunggu',
        ]);

        return redirect()->route('pasien.dashboard')->with('success', 'Antrian berhasil diambil. Nomor antrian: ' . str_pad($nomor, 3, '0', STR_PAD_LEFT));
    }

    # Method: Membatalkan antrian (hanya bisa jika status masih menunggu)
    public function batalkanAntrian($id)
    {
        $antrian = Antrian::where('pasien_id', auth()->user()->pasien->id)->findOrFail($id);
        if ($antrian->status === 'menunggu') {
            $antrian->update(['status' => 'batal']);
            return redirect()->back()->with('success', 'Antrian dibatalkan.');
        }
        return redirect()->back()->with('error', 'Antrian tidak dapat dibatalkan.');
    }

    # Method: Riwayat kunjungan pasien
    public function riwayatKunjungan()
    {
        $pasien = auth()->user()->pasien;
        $data = Antrian::where('pasien_id', $pasien->id)->with('dokter.user')->orderBy('tanggal_antrian', 'desc')->orderBy('jam_antrian', 'desc')->get();
        return view('pasien.riwayat-kunjungan', compact('data'));
    }

    # Method: Menampilkan rekam medis pasien
    public function rekamMedis()
    {
        $pasien = auth()->user()->pasien;
        $data = RekamMedis::where('pasien_id', $pasien->id)->with(['dokter.user', 'antrian'])->orderBy('created_at', 'desc')->get();
        return view('pasien.rekam-medis', compact('data'));
    }

    # Method: Download PDF rekam medis
    public function rekamMedisPdf($id)
    {
        $pasien = auth()->user()->pasien;
        $rm = RekamMedis::where('pasien_id', $pasien->id)->with(['dokter.user', 'antrian'])->findOrFail($id);
        $pdf = Pdf::loadView('pasien.rekam-medis-pdf', compact('rm'));
        return $pdf->download("rekam-medis-{$rm->id}.pdf");
    }

    # Method: Riwayat pembayaran pasien
    public function pembayaran()
    {
        $pasien = auth()->user()->pasien;
        $data = Pembayaran::whereHas('rekamMedis', function ($q) use ($pasien) {
            $q->where('pasien_id', $pasien->id);
        })->with('rekamMedis.dokter.user')->orderBy('created_at', 'desc')->get();
        return view('pasien.pembayaran', compact('data'));
    }

    # Method: Proses pembayaran oleh pasien — verifikasi jumlah bayar
    # Fitur: Pasien input nominal sesuai tagihan, pilih metode (Tunai/QRIS/Transfer)
    # Fitur: QRIS menampilkan logo QRIS, Transfer memilih bank + nomor referensi
    public function bayar(Request $request, $id)
    {
        $pembayaran = Pembayaran::whereHas('rekamMedis', function ($q) {
            $q->where('pasien_id', auth()->user()->pasien->id);
        })->findOrFail($id);

        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'jumlah_bayar' => 'required|numeric|min:0',
            'nomor_referensi' => 'nullable|string|max:100',
            'bank' => 'nullable|string|max:50',
        ]);

        # Verifikasi: jumlah bayar minimal harus sama dengan total tagihan
        if ($request->jumlah_bayar < $pembayaran->total_biaya) {
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total tagihan. Silakan masukkan nominal yang sesuai.')->withInput();
        }

        $pembayaran->update([
            'status_bayar' => 'lunas',
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => $request->metode_bayar,
            'nomor_referensi' => $request->nomor_referensi,
            'bank' => $request->bank,
        ]);

        return redirect()->route('pasien.pembayaran')->with('success', 'Terima kasih! Pembayaran Anda berhasil.');
    }
}
