@extends('layouts.pasien')

@section('title', 'Dashboard Pasien')

@section('content')
<div class="page-header">
    <h4>Dashboard Pasien</h4>
    <p>Selamat datang, {{ auth()->user()->name }}!</p>
</div>

@if($bookingKadaluarsa->count())
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-xl p-4 mb-6 flex items-start gap-3">
    <svg class="w-5 h-5 text-red-500 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <div class="text-sm text-red-800 dark:text-red-200">
        <p class="font-semibold mb-1">Jadwal booking Anda telah berakhir.</p>
        @foreach($bookingKadaluarsa as $bk)
        <p>Booking dengan dr. {{ $bk->dokter->name }} pada {{ \Carbon\Carbon::parse($bk->tanggal_booking)->format('d/m/Y') }} jam {{ \Carbon\Carbon::parse($bk->jam_booking)->format('H:i') }} telah kadaluarsa.</p>
        @endforeach
        <a href="{{ route('pasien.booking') }}" class="inline-block mt-2 text-red-700 dark:text-red-300 underline font-medium">Silakan lakukan booking ulang untuk membuat janji baru.</a>
    </div>
    <button onclick="this.closest('div').remove()" class="text-red-400 dark:text-red-500 hover:text-red-600 flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
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
    <h5 class="font-bold text-gray-800 dark:text-white mb-3">Pembayaran Terakhir</h5>
    @if($pembayaranTerakhir)
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
            <span class="badge-status badge-{{ $pembayaranTerakhir->status_bayar }}">
                {{ $pembayaranTerakhir->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}
            </span>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
            <p class="font-bold text-gray-800 dark:text-white">Rp {{ number_format($pembayaranTerakhir->total_biaya, 0, ',', '.') }}</p>
        </div>
    </div>
    @else
    <p class="text-gray-400 dark:text-gray-500 text-sm">Belum ada pembayaran.</p>
    @endif
</div>
@endsection
