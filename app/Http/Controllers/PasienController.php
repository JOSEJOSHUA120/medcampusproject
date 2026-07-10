<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Booking;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    public function dashboard()
    {
        Booking::where('pasien_id', Auth::id())
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->where(function ($q) {
                $q->where('tanggal_booking', '<', today())
                  ->orWhere(function ($q2) {
                      $q2->where('tanggal_booking', '=', today())
                         ->where('jam_booking', '<', now()->format('H:i:s'));
                  });
            })
            ->update(['status' => 'kadaluarsa']);

        $pasien = auth()->user()->pasien;
        $jumlahKunjungan = Antrian::where('pasien_id', $pasien->id)->count();
        $jumlahRekamMedis = RekamMedis::where('pasien_id', $pasien->id)->count();
        $pembayaranTerakhir = Pembayaran::whereHas('rekamMedis', function ($q) use ($pasien) {
            $q->where('pasien_id', $pasien->id);
        })->latest()->first();

        $bookingKadaluarsa = Booking::where('pasien_id', Auth::id())
            ->where('status', 'kadaluarsa')
            ->whereDate('updated_at', today())
            ->with('dokter')
            ->get();

        return view('pasien.dashboard', compact('jumlahKunjungan', 'jumlahRekamMedis', 'pembayaranTerakhir', 'bookingKadaluarsa'));
    }

    public function rekamMedis()
    {
        $pasien = auth()->user()->pasien;
        $data = RekamMedis::where('pasien_id', $pasien->id)->with(['dokter.user', 'antrian', 'resepObat.obat'])->orderBy('created_at', 'desc')->get();
        return view('pasien.rekam-medis', compact('data'));
    }

    public function rekamMedisPdf($id)
    {
        $pasien = auth()->user()->pasien;
        $rm = RekamMedis::where('pasien_id', $pasien->id)->with(['dokter.user', 'antrian', 'resepObat.obat'])->findOrFail($id);
        $pdf = Pdf::loadView('pasien.rekam-medis-pdf', compact('rm'));
        return $pdf->download("rekam-medis-{$rm->id}.pdf");
    }

    public function pembayaran()
    {
        $pasien = auth()->user()->pasien;
        $data = Pembayaran::whereHas('rekamMedis', function ($q) use ($pasien) {
            $q->where('pasien_id', $pasien->id);
        })->with('rekamMedis.dokter.user', 'rekamMedis.resepObat.obat')->orderBy('created_at', 'desc')->get();
        return view('pasien.pembayaran', compact('data'));
    }

    public function bayar(Request $request, $id)
    {
        $pembayaran = Pembayaran::whereHas('rekamMedis', function ($q) {
            $q->where('pasien_id', auth()->user()->pasien->id);
        })->findOrFail($id);

        if ($pembayaran->total_biaya <= 0) {
            return redirect()->back()->with('error', 'Tagihan belum diupdate oleh admin. Silakan tunggu konfirmasi admin.');
        }

        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris',
            'jumlah_bayar' => 'required|numeric|min:1',
        ]);

        if ($request->jumlah_bayar < $pembayaran->total_biaya) {
            return redirect()->back()->with('error', 'Jumlah bayar kurang dari total tagihan. Silakan masukkan nominal yang sesuai.')->withInput();
        }

        $pembayaran->update([
            'status_bayar' => 'lunas',
            'tanggal_bayar' => now()->toDateString(),
            'metode_bayar' => $request->metode_bayar,
        ]);

        return redirect()->route('pasien.pembayaran')->with('success', 'Terima kasih! Pembayaran Anda berhasil.');
    }
}
