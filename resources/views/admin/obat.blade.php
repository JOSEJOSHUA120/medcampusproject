@extends('layouts.admin')

@section('title', 'Data Obat')

@section('content')
<div class="page-header">
    <h4>Data Obat</h4>
    <p>Kelola daftar obat beserta harga untuk pembayaran pasien.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800">Daftar Obat</h5>
        <a href="{{ route('admin.obat.create') }}" class="btn-sm btn-primary">+ Tambah Obat</a>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Satuan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $o)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-semibold">{{ $o->nama_obat }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">Rp {{ number_format($o->harga, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $o->satuan }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm text-xs">{{ $o->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm flex gap-1">
                        <a href="{{ route('admin.obat.edit', $o->id) }}" class="btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.obat.destroy', $o->id) }}" method="POST" onsubmit="return confirm('Hapus obat ini?')">
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
