@extends('layouts.admin')

@section('title', 'Data Obat')

@section('content')
<div class="page-header">
    <h4>Data Obat</h4>
    <p>Kelola daftar obat beserta harga untuk pembayaran pasien.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800 dark:text-white">Daftar Obat</h5>
        <a href="{{ route('admin.obat.create') }}" class="btn-sm btn-primary">+ Tambah Obat</a>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Harga</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Satuan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Keterangan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $o)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-200 font-semibold">{{ $o->nama_obat }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">Rp {{ number_format($o->harga, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $o->satuan }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300 text-xs">{{ $o->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm flex gap-1">
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
