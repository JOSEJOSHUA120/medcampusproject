@extends('layouts.admin')

@section('title', 'Data Pasien')

@section('content')
<div class="page-header">
    <h4>Data Pasien</h4>
    <p>Kelola data pasien yang terdaftar di klinik. Gunakan fitur pencarian untuk mencari pasien berdasarkan nama.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
        <h5 class="font-bold text-gray-800 dark:text-white">Daftar Pasien</h5>
        <div class="flex items-center gap-3">
            {{-- Fitur Pencarian: Mencari pasien berdasarkan nama --}}
            <form method="GET" action="{{ route('admin.pasien') }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama pasien..." class="form-input-custom text-sm py-1.5 px-3 w-48">
                <button type="submit" class="btn-sm btn-primary">Cari</button>
                @if(request('search'))
                <a href="{{ route('admin.pasien') }}" class="btn-sm btn-secondary">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.pasien.create') }}" class="btn-sm btn-primary">+ Tambah Pasien</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No. Telp</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $i => $d)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-200">{{ $d->user->name }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $d->user->email }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $d->no_telp ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $d->jenis_kelamin == 'L' ? 'Laki-Laki' : ($d->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm flex gap-1">
                        <a href="{{ route('admin.pasien.edit', $d->id) }}" class="btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.pasien.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus pasien ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 dark:text-gray-500 text-sm">
                        @if(request('search'))
                        Pasien dengan nama "{{ request('search') }}" tidak ditemukan.
                        @else
                        Belum ada data pasien.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>$(document).ready(function(){$('#dataTable').DataTable();});</script>
@endpush
@endsection
