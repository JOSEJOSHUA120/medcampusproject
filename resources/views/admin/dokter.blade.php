@extends('layouts.admin')

@section('title', 'Data Dokter')

@section('content')
<div class="page-header">
    <h4>Data Dokter</h4>
    <p>Kelola data dokter yang bertugas di klinik beserta jadwal praktik.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800">Daftar Dokter</h5>
        <a href="{{ route('admin.dokter.create') }}" class="btn-sm btn-primary">+ Tambah Dokter</a>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Spesialisasi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jadwal Praktik</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Telp</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $d)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $d->nama_dokter }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $d->user->email }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $d->spesialisasi }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm text-xs">{{ $d->getJadwalText() }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $d->no_telp ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm flex gap-1">
                        <a href="{{ route('admin.dokter.edit', $d->id) }}" class="btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.dokter.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus dokter ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
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
