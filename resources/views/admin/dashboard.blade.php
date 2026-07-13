@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h4>Dashboard Admin</h4>
    <p>Selamat datang, {{ auth()->user()->name }}! Ringkasan aktivitas klinik hari ini.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <a href="{{ route('admin.booking') }}" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500 transition block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-3xl font-bold tracking-tight text-gray-800 dark:text-white">{{ $bookingHariIni }}</span>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Booking Hari Ini</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Jadwal pasien hari ini</p>
    </a>


    <a href="{{ route('admin.pasien') }}" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-green-200 dark:hover:border-green-500 transition block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <span class="text-3xl font-bold tracking-tight text-gray-800 dark:text-white">{{ $pasienBulanIni }}</span>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pasien Bulan Ini</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Pasien berobat bulan {{ now()->format('F Y') }}</p>
    </a>

    <a href="{{ route('admin.pembayaran') }}" class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-500 transition block">
        <div class="mb-3">
            <span class="text-3xl font-bold tracking-tight text-gray-800 dark:text-white">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</span>
        </div>
        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Pendapatan Bulan Ini</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Pembayaran lunas {{ now()->format('F Y') }}</p>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 dark:text-white">Booking Hari Ini</h5>
            <a href="{{ route('admin.booking') }}" class="btn-sm btn-primary">Kelola</a>
        </div>
        <div class="overflow-x-auto">
            @if($bookingHariIniList->count() > 0)
            <table class="dataTable">
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Jam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookingHariIniList as $b)
                    <tr>
                        <td class="dark:text-gray-300">{{ $b->pasien->user->name ?? '-' }}</td>
                        <td class="dark:text-gray-300">{{ $b->dokter->user->name ?? '-' }}</td>
                        <td class="dark:text-gray-300">{{ $b->waktu_booking ? \Carbon\Carbon::parse($b->waktu_booking)->format('H:i') : '-' }}</td>
                        <td>{!! $b->status_badge !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p class="text-gray-400 dark:text-gray-500 text-sm text-center py-8">Tidak ada booking hari ini</p>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 dark:text-white">Aktivitas Terbaru</h5>
        </div>
        <div class="space-y-3">
            @forelse($aktivitasTerbaru as $log)
            <div class="flex items-start gap-3 pb-3 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $log->deskripsi ?? 'Aktivitas' }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-400 dark:text-gray-500 text-sm text-center py-4">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
