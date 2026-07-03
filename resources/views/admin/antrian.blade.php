@extends('layouts.admin')

@section('title', 'Antrian')

@section('content')
<div class="page-header">
    <h4>Data Antrian</h4>
    <p>Pantau status antrian pasien secara real-time. Admin dapat mengubah status antrian (Menunggu &rarr; Dipanggil &rarr; Diperiksa &rarr; Selesai / Batal).</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No. Antrian</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-semibold">{{ $a->nomor_antrian }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($a->tanggal_antrian)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($a->jam_antrian)->format('H:i') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm"><span class="badge-status badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        <form action="{{ route('admin.antrian.status', $a->id) }}" method="POST" class="flex gap-1">
                            @csrf @method('PUT')
                            <select name="status" class="form-select-custom text-xs py-1 px-2 w-auto">
                                <option value="menunggu" @selected($a->status=='menunggu')>Menunggu</option>
                                <option value="dipanggil" @selected($a->status=='dipanggil')>Dipanggil</option>
                                <option value="diperiksa" @selected($a->status=='diperiksa')>Diperiksa</option>
                                <option value="selesai" @selected($a->status=='selesai')>Selesai</option>
                                <option value="batal" @selected($a->status=='batal')>Batal</option>
                            </select>
                            <button class="btn-sm btn-primary">Update</button>
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
