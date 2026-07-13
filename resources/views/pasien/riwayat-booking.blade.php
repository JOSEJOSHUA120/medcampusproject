@extends('layouts.pasien')

@section('title', 'Riwayat Booking')

@section('content')
<div class="page-header">
    <h4>Riwayat Booking</h4>
    <p>Daftar booking janji temu Anda dengan dokter.</p>
</div>

@php
    $user = auth()->user();
    $profil = $user->pasien;
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
    <h5 class="font-bold text-gray-800 dark:text-white mb-4">Data Diri Anda</h5>
    <div class="flex items-center gap-4 mb-4">
        <img src="{{ $profil->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($user->email) }}" alt="foto" class="w-16 h-16 rounded-full object-cover border-2 border-primary-200">
        <div>
            <p class="font-bold text-gray-800 dark:text-white text-lg">{{ $user->name }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</span>
            <p class="font-medium text-gray-800 dark:text-white">{{ $user->name }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</span>
            <p class="font-medium text-gray-800 dark:text-white">{{ $user->email }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Telepon</span>
            <p class="font-medium text-gray-800 dark:text-white">{{ $profil->no_telp ?? '-' }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tempat Lahir</span>
            <p class="font-medium text-gray-800 dark:text-white">{{ $profil->tempat_lahir ?? '-' }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal Lahir</span>
            <p class="font-medium text-gray-800 dark:text-white">{{ $profil->tanggal_lahir ? \Carbon\Carbon::parse($profil->tanggal_lahir)->format('d/m/Y') : '-' }}</p>
        </div>
        <div>
            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis Kelamin</span>
            <p class="font-medium text-gray-800 dark:text-white">
                @if($profil && $profil->jenis_kelamin == 'L') Laki-Laki
                @elseif($profil && $profil->jenis_kelamin == 'P') Perempuan
                @else -
                @endif
            </p>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    @if($data->isEmpty())
    <div class="text-center py-8 text-gray-400 dark:text-gray-500">
        <p>Belum ada booking.</p>
        <a href="{{ route('pasien.booking') }}" class="btn-primary mt-4 inline-block">Booking Sekarang</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Keluhan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $b)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm font-medium dark:text-white">{{ $b->dokter->name }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm max-w-[150px] truncate dark:text-gray-300" title="{{ $b->keluhan_awal }}">{{ $b->keluhan_awal ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm">
                        @php
                            $badgeClass = match($b->status) {
                                'menunggu' => 'badge-menunggu',
                                'disetujui' => 'badge-dipanggil',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'check_in' => 'bg-indigo-100 text-indigo-800',
                                'tidak_hadir' => 'bg-gray-100 text-gray-800',
                                'selesai' => 'badge-selesai',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                                'kadaluarsa' => 'bg-orange-100 text-orange-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="badge-status {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        @if($b->status == 'menunggu')
                        <form action="{{ route('pasien.booking.batal', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan booking ini?')">
                            @csrf @method('PUT')
                            <button class="btn-sm btn-danger">Batalkan</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>
@endpush
@endsection
