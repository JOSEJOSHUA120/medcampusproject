@extends('layouts.dokter')

@section('title', 'Dashboard Dokter')

@section('content')
<div class="page-header">
    <h4>Dashboard Dokter</h4>
    <p>Selamat datang, {{ auth()->user()->name }}! Kelola antrian dan pemeriksaan.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($dataAntrian as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $a->nomor_antrian }}</td>
                    <td class="px-4 py-3 text-sm">{{ $a->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($a->tanggal_antrian)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($a->jam_antrian)->format('H:i') }}</td>
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
                        @elseif($a->status == 'diperiksa')
                        <a href="{{ route('dokter.rekam-medis.create', $a->id) }}" class="btn-success btn-sm">Selesai & Buat Rekam Medis</a>
                        @else
                        <span class="text-gray-400 text-xs">{{ $a->status == 'selesai' ? 'Selesai' : ($a->status == 'batal' ? 'Batal' : '-') }}</span>
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
