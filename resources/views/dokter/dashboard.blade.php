@extends('layouts.dokter')

@section('title', 'Dashboard Dokter')

@section('content')
<div class="page-header">
    <h4>Dashboard Dokter</h4>
    <p>Selamat datang, {{ auth()->user()->name }}! Kelola antrian dan pemeriksaan.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
    <div class="stat-card border-l-primary-500">
        <div class="stat-value">{{ $pasienHariIni }}</div>
        <div class="stat-label">Pasien Hari Ini</div>
    </div>
    <div class="stat-card border-l-primary-400">
        <div class="stat-value">{{ $pemeriksaanHariIni }}</div>
        <div class="stat-label">Pemeriksaan Hari Ini</div>
    </div>
    <div class="stat-card border-l-amber-400">
        <div class="stat-value">{{ $antrianMenunggu }}</div>
        <div class="stat-label">Antrian Menunggu</div>
    </div>
    <div class="stat-card border-l-green-400">
        <div class="stat-value">{{ $antrianDipanggil }}</div>
        <div class="stat-label">Antrian Dipanggil</div>
    </div>
    <div class="stat-card border-l-blue-400">
        <div class="stat-value">{{ $bookingMenunggu }}</div>
        <div class="stat-label">Booking Menunggu</div>
    </div>
    <div class="stat-card border-l-purple-400">
        <div class="stat-value">{{ $bookingDisetujui }}</div>
        <div class="stat-label">Booking Disetujui</div>
    </div>
</div>

<div class="card-dashboard p-4 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800">Booking Aktif</h5>
        <a href="{{ route('dokter.booking') }}" class="btn-primary btn-sm">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Telp</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluhan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookingAktif as $b)
                @php $bp = $b->pasien; $bp_profil = $bp?->pasien; @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <img src="{{ $bp_profil->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($bp->email ?? '') }}" alt="foto" class="w-8 h-8 rounded-full object-cover border">
                            <span class="font-medium">{{ $bp->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $bp_profil?->no_telp ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</td>
                    <td class="px-4 py-3 text-sm max-w-[200px] truncate">{{ $b->keluhan_awal ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        @php
                            $badgeClass = match($b->status) {
                                'menunggu' => 'badge-menunggu',
                                'disetujui' => 'badge-dipanggil',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'check_in' => 'bg-indigo-100 text-indigo-800',
                                'tidak_hadir' => 'bg-gray-100 text-gray-800',
                                'selesai' => 'badge-selesai',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="badge-status {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada booking aktif.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card-dashboard p-4">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800">Semua Antrian</h5>
        <a href="{{ route('dokter.antrian') }}" class="btn-primary btn-sm">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Antrian</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Telp</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluhan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($dataAntrian as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $a->nomor_antrian }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2">
                            <img src="{{ $a->pasien->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($a->pasien->user->email ?? '') }}" alt="foto" class="w-8 h-8 rounded-full object-cover border">
                            {{ $a->pasien->user->name ?? '-' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $a->pasien->no_telp ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm max-w-[200px] truncate">{{ $a->complaint ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm"><span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                    <td class="px-4 py-3 text-sm">
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
                        <a href="{{ route('dokter.rekam-medis.create', $a->id) }}" class="btn-success btn-sm">Selesai & Buat Rekam Medis</a>
                        @else
                        <span class="text-gray-400 text-xs">{{ $a->status == 'selesai' ? 'Selesai' : ($a->status == 'dibatalkan' ? 'Batal' : '-') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada antrian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
