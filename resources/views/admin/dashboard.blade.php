@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h4>Dashboard Admin</h4>
    <p>Selamat datang, {{ auth()->user()->name }}! Ringkasan sistem klinik.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="stat-card"><div class="stat-value">{{ $totalPasien }}</div><div class="stat-label">Total Pasien</div></div>
    <div class="stat-card"><div class="stat-value">{{ $pasienHariIni }}</div><div class="stat-label">Pasien Hari Ini</div></div>
    <div class="stat-card"><div class="stat-value">{{ $totalDokter }}</div><div class="stat-label">Total Dokter</div></div>
    <div class="stat-card"><div class="stat-value">{{ $antrianHariIni }}</div><div class="stat-label">Antrian Hari Ini</div></div>
    <div class="stat-card"><div class="stat-value">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div><div class="stat-label">Pendapatan Bulan Ini</div></div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800">Antrian Hari Ini</h5>
        <a href="{{ route('admin.antrian') }}" class="btn-sm btn-primary">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No.</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataAntrian as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-semibold">{{ $a->nomor_antrian }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($a->jam_antrian)->format('H:i') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm"><span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 border-b border-gray-100 text-sm text-center text-gray-400">Tidak ada antrian hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
