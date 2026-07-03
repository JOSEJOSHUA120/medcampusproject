@extends('layouts.dokter')

@section('title', 'Riwayat Pasien')

@section('content')
<div class="page-header">
    <h4>Riwayat Pasien</h4>
    <p>Riwayat pemeriksaan pasien yang pernah Anda tangani.</p>
</div>

@forelse($pasien as $p)
<div class="card-dashboard mb-4">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h5 class="font-bold text-gray-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $p->user->name ?? '-' }}
        </h5>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Resep Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($p->rekamMedis as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->diagnosa, 50) }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->tindakan, 50) }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->resep_obat, 50) ?: '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->catatan_dokter, 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@empty
<div class="card-dashboard p-8 text-center text-gray-400 text-sm">Belum ada riwayat pasien.</div>
@endforelse
@endsection
