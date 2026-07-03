@extends('layouts.dokter')

@section('title', 'Antrian')

@section('content')
<div class="page-header">
    <h4>Data Antrian</h4>
    <p>Panggil dan kelola antrian pasien Anda.</p>
</div>

<div class="card-dashboard p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Antrian</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($data as $i => $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm font-bold text-gray-900">{{ $a->nomor_antrian }}</td>
                    <td class="px-4 py-3 text-sm">{{ $a->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($a->tanggal_antrian)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($a->jam_antrian)->format('H:i') }}</td>
                    <td class="px-4 py-3 text-sm"><span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                    <td class="px-4 py-3 text-sm space-x-1">
                        @if($a->status == 'menunggu')
                        <form action="{{ route('dokter.antrian.panggil', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PUT')
                            <button class="btn-warning btn-sm">Panggil</button>
                        </form>
                        @endif
                        @if($a->status == 'dipanggil')
                        <form action="{{ route('dokter.antrian.mulai-periksa', $a->id) }}" method="POST" class="inline">
                            @csrf @method('PUT')
                            <button class="btn-primary btn-sm">Mulai Periksa</button>
                        </form>
                        @endif
                        @if($a->status == 'diperiksa')
                        <a href="{{ route('dokter.rekam-medis.create', $a->id) }}" class="btn-success btn-sm">Selesai & Buat Rekam Medis</a>
                        @endif
                        @if($a->status == 'selesai')
                        <span class="text-gray-400 text-xs">Selesai</span>
                        @endif
                        @if($a->status == 'batal')
                        <span class="text-gray-400 text-xs">Batal</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
