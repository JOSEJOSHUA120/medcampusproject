@extends('layouts.pasien')

@section('title', 'Dashboard Pasien')

@section('content')
<div class="page-header">
    <h4>Dashboard Pasien</h4>
    <p>Selamat datang, {{ auth()->user()->name }}!</p>
</div>

@if($antrianSekarang)
<div class="card-dashboard p-6 text-center mb-6">
    <h5 class="font-bold text-gray-800 mb-2">Status Antrian Anda</h5>
    <div class="text-5xl font-bold text-primary-600 my-4">{{ $antrianSekarang->nomor_antrian }}</div>
    <div class="mb-3">
        <span class="badge-status badge-{{ $antrianSekarang->status }} text-sm">{{ ucfirst($antrianSekarang->status) }}</span>
    </div>
    <p class="text-gray-400 text-sm">Dokter: {{ $antrianSekarang->dokter->user->name ?? '-' }}</p>
</div>
@else
<div class="card-dashboard p-6 text-center mb-6">
    <h5 class="font-bold text-gray-800 mb-3">Belum Ada Antrian Aktif</h5>
    <a href="{{ route('pasien.ambil-antrian') }}" class="btn-primary">Ambil Antrian Sekarang</a>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="stat-card border-l-sky-500">
        <div class="stat-value">{{ $jumlahKunjungan }}</div>
        <div class="stat-label">Total Kunjungan</div>
    </div>
    <div class="stat-card border-l-blue-500">
        <div class="stat-value">{{ $jumlahRekamMedis }}</div>
        <div class="stat-label">Rekam Medis</div>
    </div>
</div>

<div class="card-dashboard p-6">
    <h5 class="font-bold text-gray-800 mb-3">Pembayaran Terakhir</h5>
    @if($pembayaranTerakhir)
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500">Status</p>
            <span class="badge-status badge-{{ $pembayaranTerakhir->status_bayar }}">
                {{ $pembayaranTerakhir->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}
            </span>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Total</p>
            <p class="font-bold text-gray-800">Rp {{ number_format($pembayaranTerakhir->total_biaya, 0, ',', '.') }}</p>
        </div>
    </div>
    @else
    <p class="text-gray-400 text-sm">Belum ada pembayaran.</p>
    @endif
</div>

<div class="flex gap-3 mt-6">
    <a href="{{ route('pasien.ambil-antrian') }}" class="btn-primary">Ambil Antrian</a>
    <a href="{{ route('pasien.riwayat-kunjungan') }}" class="btn-secondary">Riwayat Kunjungan</a>
</div>
@endsection
