@extends('layouts.dokter')

@section('title', 'Kelola Pasien')

@section('content')
<div class="page-header">
    <h4>Kelola Pasien</h4>
    <p>Panggil dan kelola pasien Anda.</p>
</div>

<div class="card-dashboard p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Antrian</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ruangan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($data as $i => $a)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $a->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm max-w-xs dark:text-gray-300">{{ $a->complaint ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm max-w-xs dark:text-gray-300">{{ $a->notes ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm dark:text-gray-300">{{ \Carbon\Carbon::parse($a->jam_antrian)->format('H:i') }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-primary-600 dark:text-primary-400">{{ $a->room->room_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm"><span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                    <td class="px-4 py-3 text-sm space-x-1">
                        @if(in_array($a->status, ['selesai', 'dibatalkan']))
                            @if(!$a->rekamMedis)
                            <a href="{{ route('dokter.rekam-medis.create', $a->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">Isi Rekam Medis</a>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        @else
                            @if($a->status == 'dipanggil')
                            <form action="{{ route('dokter.antrian.mulai-periksa', $a->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="btn-primary btn-sm">Mulai Periksa</button>
                            </form>
                            @endif
                            @if(in_array($a->status, ['menunggu', 'dikonfirmasi']))
                            <form action="{{ route('dokter.antrian.panggil', $a->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="btn-warning btn-sm">Panggil</button>
                            </form>
                            @endif
                            @if(!$a->rekamMedis)
                            <a href="{{ route('dokter.rekam-medis.create', $a->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">Isi Rekam Medis</a>
                            @endif
                            <a href="{{ route('dokter.antrian.download', $a->id) }}" class="btn-secondary btn-sm">Download</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
