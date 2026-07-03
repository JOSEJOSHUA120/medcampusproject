<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

# Route: Halaman utama (landing page)
Route::get('/', [HomeController::class, 'index'])->name('landing');

# Route: Dashboard routing berdasarkan role
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'admin') return redirect()->route('admin.dashboard');
        if ($role === 'dokter') return redirect()->route('dokter.dashboard');
        if ($role === 'pasien') return redirect()->route('pasien.dashboard');
        return redirect('/');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

# Route Group: Admin — membutuhkan middleware auth + admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/pasien', [AdminController::class, 'pasien'])->name('pasien');
    Route::get('/pasien/create', [AdminController::class, 'pasienCreate'])->name('pasien.create');
    Route::post('/pasien', [AdminController::class, 'pasienStore'])->name('pasien.store');
    Route::get('/pasien/{id}/edit', [AdminController::class, 'pasienEdit'])->name('pasien.edit');
    Route::put('/pasien/{id}', [AdminController::class, 'pasienUpdate'])->name('pasien.update');
    Route::delete('/pasien/{id}', [AdminController::class, 'pasienDestroy'])->name('pasien.destroy');
    Route::get('/dokter', [AdminController::class, 'dokter'])->name('dokter');
    Route::get('/dokter/create', [AdminController::class, 'dokterCreate'])->name('dokter.create');
    Route::post('/dokter', [AdminController::class, 'dokterStore'])->name('dokter.store');
    Route::get('/dokter/{id}/edit', [AdminController::class, 'dokterEdit'])->name('dokter.edit');
    Route::put('/dokter/{id}', [AdminController::class, 'dokterUpdate'])->name('dokter.update');
    Route::delete('/dokter/{id}', [AdminController::class, 'dokterDestroy'])->name('dokter.destroy');
    Route::get('/antrian', [AdminController::class, 'antrian'])->name('antrian');
    # Route: Update status antrian (menunggu → dipanggil → diperiksa → selesai / batal)
    Route::put('/antrian/{id}/status', [AdminController::class, 'antrianUpdateStatus'])->name('antrian.status');
    Route::get('/rekam-medis', [AdminController::class, 'rekamMedis'])->name('rekam-medis');
    Route::get('/obat', [AdminController::class, 'obat'])->name('obat');
    Route::get('/obat/create', [AdminController::class, 'obatCreate'])->name('obat.create');
    Route::post('/obat', [AdminController::class, 'obatStore'])->name('obat.store');
    Route::get('/obat/{id}/edit', [AdminController::class, 'obatEdit'])->name('obat.edit');
    Route::put('/obat/{id}', [AdminController::class, 'obatUpdate'])->name('obat.update');
    Route::delete('/obat/{id}', [AdminController::class, 'obatDestroy'])->name('obat.destroy');
    Route::get('/pembayaran', [AdminController::class, 'pembayaran'])->name('pembayaran');
    Route::get('/pembayaran/create', [AdminController::class, 'pembayaranCreate'])->name('pembayaran.create');
    Route::post('/pembayaran', [AdminController::class, 'pembayaranStore'])->name('pembayaran.store');
    Route::get('/pembayaran/{id}/edit', [AdminController::class, 'pembayaranEdit'])->name('pembayaran.edit');
    Route::put('/pembayaran/{id}', [AdminController::class, 'pembayaranUpdate'])->name('pembayaran.update');
    Route::delete('/pembayaran/{id}', [AdminController::class, 'pembayaranDestroy'])->name('pembayaran.destroy');
    # Route: Verifikasi pembayaran — pasien input nominal, divalidasi
    Route::put('/pembayaran/{id}/bayar', [AdminController::class, 'pembayaranBayar'])->name('pembayaran.bayar');
    # Route: Generate pembayaran (QRIS/Bank/Referensi)
    Route::get('/pembayaran/{id}/generate', [AdminController::class, 'pembayaranGenerate'])->name('pembayaran.generate');
    Route::put('/pembayaran/{id}/generate', [AdminController::class, 'pembayaranGenerateStore'])->name('pembayaran.generate.store');
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/pdf', [AdminController::class, 'laporanPdf'])->name('laporan.pdf');
});

# Route Group: Dokter — membutuhkan middleware auth + dokter
Route::middleware(['auth', 'dokter'])->prefix('dokter')->name('dokter.')->group(function () {
    Route::get('/dashboard', [DokterController::class, 'dashboard'])->name('dashboard');
    Route::get('/antrian', [DokterController::class, 'antrian'])->name('antrian');
    Route::put('/antrian/{id}/panggil', [DokterController::class, 'panggil'])->name('antrian.panggil');
    Route::put('/antrian/{id}/mulai-periksa', [DokterController::class, 'mulaiPeriksa'])->name('antrian.mulai-periksa');

    Route::get('/rekam-medis', [DokterController::class, 'rekamMedis'])->name('rekam-medis');
    Route::get('/rekam-medis/create/{antrianId}', [DokterController::class, 'rekamMedisCreate'])->name('rekam-medis.create');
    Route::post('/rekam-medis/store/{antrianId}', [DokterController::class, 'rekamMedisStore'])->name('rekam-medis.store');
    Route::get('/rekam-medis/{id}/edit', [DokterController::class, 'rekamMedisEdit'])->name('rekam-medis.edit');
    Route::put('/rekam-medis/{id}', [DokterController::class, 'rekamMedisUpdate'])->name('rekam-medis.update');
    Route::get('/riwayat-pasien', [DokterController::class, 'riwayatPasien'])->name('riwayat-pasien');
});

# Route Group: Pasien — membutuhkan middleware auth + pasien
Route::middleware(['auth', 'pasien'])->prefix('pasien')->name('pasien.')->group(function () {
    Route::get('/dashboard', [PasienController::class, 'dashboard'])->name('dashboard');
    Route::get('/ambil-antrian', [PasienController::class, 'ambilAntrian'])->name('ambil-antrian');
    Route::post('/antrian', [PasienController::class, 'storeAntrian'])->name('antrian.store');
    Route::put('/antrian/{id}/batal', [PasienController::class, 'batalkanAntrian'])->name('antrian.batal');
    Route::get('/riwayat-kunjungan', [PasienController::class, 'riwayatKunjungan'])->name('riwayat-kunjungan');
    Route::get('/rekam-medis', [PasienController::class, 'rekamMedis'])->name('rekam-medis');
    Route::get('/rekam-medis/{id}/pdf', [PasienController::class, 'rekamMedisPdf'])->name('rekam-medis.pdf');
    Route::get('/pembayaran', [PasienController::class, 'pembayaran'])->name('pembayaran');
    # Route: Proses pembayaran oleh pasien — verifikasi nominal, pilih metode bayar
    Route::put('/pembayaran/{id}/bayar', [PasienController::class, 'bayar'])->name('pembayaran.bayar');
});

require __DIR__ . '/auth.php';
