@extends('layouts.admin')

@section('title', 'Rekam Medis')

@section('content')
<div class="page-header">
    <h4>Rekam Medis</h4>
    <p>Data rekam medis pasien yang telah ditulis oleh dokter. <span class="badge-status bg-gray-100 text-gray-600 text-xs">Read Only</span></p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <h5 class="font-bold text-gray-800 dark:text-white">Riwayat Rekam Medis</h5>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tindakan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $r)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $r->pasien->user->name ?? '-' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $r->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300 max-w-[200px]">{{ \Str::limit($r->diagnosa, 50) }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300 max-w-[200px]">{{ \Str::limit($r->tindakan, 50) }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300 text-xs max-w-[200px]">
                        @if($r->resepObat->count())
                            @foreach($r->resepObat as $ro)
                            <div class="truncate">{{ $ro->obat->nama_obat ?? '-' }} x{{ $ro->jumlah }}</div>
                            @endforeach
                        @else
                            {{ \Str::limit($r->resep_obat, 50) ?: '-' }}
                        @endif
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $r->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>$(document).ready(function(){$('#dataTable').DataTable();});</script>
@endpush
@endsection
