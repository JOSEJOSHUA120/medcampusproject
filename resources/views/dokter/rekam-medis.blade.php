@extends('layouts.dokter')

@section('title', 'Rekam Medis')

@section('content')
<div class="page-header">
    <h4>Rekam Medis</h4>
    <p>Data rekam medis yang telah Anda buat.</p>
</div>

<div class="card-dashboard p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tindakan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Resep Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($data as $i => $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->diagnosa, 50) }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->tindakan, 50) }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->resep_obat, 50) ?: '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('dokter.rekam-medis.edit', $r->id) }}" class="btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
