@extends('layouts.admin')

@section('title', 'Kelola Ruangan')

@section('content')
<div class="page-header">
    <h4>Kelola Ruangan</h4>
    <p>Kelola data ruangan praktik dokter. Tentukan ketersediaan ruangan dan atur jadwal.</p>
</div>

<div class="flex items-center justify-between mb-4">
    <h5 class="font-bold text-gray-800">Daftar Ruangan</h5>
    <a href="{{ route('admin.rooms.create') }}" class="btn-sm btn-primary">+ Tambah Ruangan</a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
    @forelse($data as $room)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-all duration-200">
        <div class="flex items-start justify-between mb-3">
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $room->room_number }}</div>
                <div class="text-xs text-gray-500 mt-1">Ruangan</div>
            </div>
            @if($room->status == 'free')
            <span class="badge-status bg-green-100 text-green-800">Tersedia</span>
            @else
            <span class="badge-status bg-red-100 text-red-800">Terpakai</span>
            @endif
        </div>
        <p class="text-sm text-gray-600 mb-4 min-h-[40px]">{{ $room->description ?? 'Tidak ada deskripsi.' }}</p>
        <div class="flex gap-2">
            <a href="{{ route('admin.rooms.edit', $room->id) }}" class="btn-sm btn-warning">Edit</a>
            <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini?')">
                @csrf @method('DELETE')
                <button class="btn-sm btn-danger">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-12 text-gray-400 bg-white rounded-2xl border border-gray-100">
        <p class="text-lg font-semibold">Belum ada ruangan</p>
        <p class="text-sm mt-1">Tambahkan ruangan baru untuk mulai mengelola.</p>
    </div>
    @endforelse
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h5 class="font-bold text-gray-800 mb-4">Tabel Ruangan</h5>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Room Number</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $room)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-semibold">{{ $room->room_number }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        @if($room->status == 'free')
                        <span class="badge-status bg-green-100 text-green-800">Tersedia</span>
                        @else
                        <span class="badge-status bg-red-100 text-red-800">Terpakai</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm text-xs">{{ $room->description ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm flex gap-1">
                        <a href="{{ route('admin.rooms.edit', $room->id) }}" class="btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini?')">
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
