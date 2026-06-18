@extends('layouts.pasien')

@section('title', 'Rekam Medis')

@section('content')
<div class="page-header">
    <h4>Rekam Medis Saya</h4>
    <p>Lihat dan download rekam medis Anda.</p>
</div>

<div class="card-dashboard p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Diagnosa</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Resep Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($data as $i => $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $r->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3 text-sm">dr. {{ $r->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->diagnosa, 50) }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Str::limit($r->resep_obat, 50) ?: '-' }}</td>
                    <td class="px-4 py-3 text-sm">
                        <a href="{{ route('pasien.rekam-medis.pdf', $r->id) }}" class="btn-primary btn-sm">Download PDF</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
