@extends('layouts.dokter')

@section('title', 'Dashboard Dokter')

@section('content')
<div class="page-header">
    <h4>Dashboard Dokter</h4>
    <p>Selamat datang, {{ auth()->user()->name }}! Pantau pasien dan jadwal praktik Anda.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="stat-card border-l-primary-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Booking Hari Ini</p>
                <p class="stat-value">{{ $bookingHariIni }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>
    <div class="stat-card border-l-yellow-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Pasien Menunggu</p>
                <p class="stat-value">{{ $antrianMenunggu }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="stat-card border-l-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Dalam Periksa</p>
                <p class="stat-value">{{ $antrianDipanggil + $antrianMenunggu }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>
    <div class="stat-card border-l-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Selesai Diperiksa</p>
                <p class="stat-value">{{ $selesaiDiperiksa }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

@if($bookingAktif->count() > 0)
<div class="card-dashboard mb-8">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <h5 class="font-bold text-gray-800 dark:text-white">Booking Aktif</h5>
        <a href="{{ route('dokter.booking') }}" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700">Lihat Semua</a>
    </div>
    <div class="divide-y divide-gray-50 dark:divide-gray-700">
        @foreach($bookingAktif as $b)
        <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
            <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($b->pasien->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $b->pasien->name ?? '-' }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $b->keluhan_awal ? \Illuminate\Support\Str::limit($b->keluhan_awal, 40) : 'Tanpa keluhan' }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="badge-status badge-{{ $b->status }} text-xs">{{ ucfirst($b->status) }}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="card-dashboard">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
        <h5 class="font-bold text-gray-800 dark:text-white">Semua Antrian</h5>
        <a href="{{ route('dokter.antrian') }}" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700">Kelola Antrian</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Antrian</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pasien</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Keluhan</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($dataAntrian as $a)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $a->nomor_antrian }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 flex items-center justify-center text-xs font-bold flex-shrink-0 overflow-hidden">
                                @if($a->pasien->foto)
                                <img src="{{ $a->pasien->foto }}" alt="" class="w-full h-full object-cover">
                                @else
                                {{ strtoupper(substr($a->pasien->user->name ?? '?', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $a->pasien->user->name ?? '-' }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $a->pasien->no_telp ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-sm text-gray-600 dark:text-gray-400 max-w-[200px] truncate">{{ $a->complaint ?? '-' }}</p>
                    </td>
                    <td class="px-5 py-3">
                        <span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @if($a->status == 'menunggu')
                        <form action="{{ route('dokter.antrian.panggil', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PUT')
                            <button class="btn-warning btn-sm">Panggil</button>
                        </form>
                        @elseif($a->status == 'dipanggil')
                        <form action="{{ route('dokter.antrian.mulai-periksa', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PUT')
                            <button class="btn-primary btn-sm">Mulai Periksa</button>
                        </form>
                        @elseif($a->status == 'sedang_dilayani')
                        <a href="{{ route('dokter.rekam-medis.create', $a->id) }}" class="btn-success btn-sm inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Selesai
                        </a>
                        @else
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $a->status == 'selesai' ? 'Selesai' : ($a->status == 'dibatalkan' ? 'Batal' : '-') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center">
                        <p class="text-gray-400 dark:text-gray-500 text-sm">Belum ada antrian hari ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
