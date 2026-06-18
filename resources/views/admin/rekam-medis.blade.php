@extends('layouts.admin')

@section('title', 'Rekam Medis')

@section('content')
<div class="page-header">
    <h4>Rekam Medis</h4>
    <p>Lihat data rekam medis pasien (read-only).</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Resep Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $r->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $r->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Str::limit($r->diagnosa, 50) }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Str::limit($r->tindakan, 50) }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Str::limit($r->resep_obat, 50) ?: '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $r->created_at->format('Y-m-d H:i') }}</td>
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
