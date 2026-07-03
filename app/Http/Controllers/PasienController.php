<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\User;
use App\Notifications\AppointmentNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function dashboard()
    {
        $pasien = auth()->user()->pasien;
        $antrianSekarang = Antrian::where('pasien_id', $pasien->id)->whereDate('tanggal_antrian', today())->whereNotIn('status', ['dibatalkan', 'selesai'])->orderBy('created_at', 'desc')->first();
        $jumlahKunjungan = Antrian::where('pasien_id', $pasien->id)->count();
        $jumlahRekamMedis = RekamMedis::where('pasien_id', $pasien->id)->count();
        $pembayaranTerakhir = Pembayaran::whereHas('rekamMedis', function ($q) use ($pasien) {
            $q->where('pasien_id', $pasien->id);
        })->latest()->first();

        return view('pasien.dashboard', compact('antrianSekarang', 'jumlahKunjungan', 'jumlahRekamMedis', 'pembayaranTerakhir'));
    }

    public function ambilAntrian()
    {
        $dokter = Dokter::with('user')->get();
        return view('pasien.ambil-antrian', compact('dokter'));
    }

    public function storeAntrian(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:dokter,id',
            'complaint' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $pasien = auth()->user()->pasien;

        $today = today()->toDateString();
        $lastAntrian = Antrian::where('dokter_id', $request->dokter_id)->whereDate('tanggal_antrian', $today)->orderBy('nomor_antrian', 'desc')->first();
        $nomor = $lastAntrian ? (int)$lastAntrian->nomor_antrian + 1 : 1;

        $antrian = Antrian::create([
            'pasien_id' => $pasien->id,
            'dokter_id' => $request->dokter_id,
            'nomor_antrian' => str_pad($nomor, 3, '0', STR_PAD_LEFT),
            'tanggal_antrian' => $today,
            'jam_antrian' => now()->format('H:i:s'),
            'status' => 'menunggu',
            'complaint' => $request->complaint,
            'notes' => $request->notes,
        ]);

        $pasien->user->notify(new AppointmentNotification($antrian, 'created'));
        $antrian->dokter->user->notify(new AppointmentNotification($antrian, 'new_patient'));

        return redirect()->route('pasien.dashboard')->with('success', 'Antrian berhasil diambil. Nomor antrian: ' . str_pad($nomor, 3, '0', STR_PAD_LEFT));
    }

    public function batalkanAntrian($id)
    {
        $antrian = Antrian::where('pasien_id', auth()->user()->pasien->id)->findOrFail($id);
        if ($antrian->status === 'menunggu') {
            $antrian->update(['status' => 'dibatalkan']);
            return redirect()->back()->with('success', 'Antrian dibatalkan.');
        }
        return redirect()->back()->with('error', 'Antrian tidak dapat dibatalkan.');
    }

    public function jadwalSaya()
    {
        $pasien = auth()->user()->pasien;
        $data = Antrian::where('pasien_id', $pasien->id)->with(['dokter.user', 'room'])->orderBy('tanggal_antrian', 'desc')->orderBy('jam_antrian', 'desc')->get();
        return view('pasien.jadwal-saya', compact('data'));
    }

    public function konfirmasiHadir($id)
    {
        $pasien = auth()->user()->pasien;
        $antrian = Antrian::where('pasien_id', $pasien->id)->findOrFail($id);

        if ($antrian->status === 'menunggu') {
            $antrian->update(['status' => 'dikonfirmasi']);

            $antrian->dokter->user->notify(new AppointmentNotification($antrian, 'patient_confirmed'));

            $adminUsers = User::where('role', 'admin')->get();
            foreach ($adminUsers as $admin) {
                $admin->notify(new AppointmentNotification($antrian, 'patient_confirmed'));
            }

            return redirect()->back()->with('success', 'Konfirmasi kehadiran berhasil.');
        }

        return redirect()->back()->with('error', 'Antrian tidak dapat dikonfirmasi. Pastikan status antrian masih menunggu.');
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

        $request->validate([
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'jumlah_bayar' => 'required|numeric|min:0',
            'nomor_referensi' => 'nullable|string|max:100',
            'bank' => 'nullable|string|max:50',
        ]);

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
