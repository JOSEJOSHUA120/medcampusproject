@extends('layouts.admin')

@section('title', 'Jadwal Dokter')

@section('content')
<div class="page-header">
    <h4>Jadwal Dokter</h4>
    <p>Atur jadwal praktik dokter. Setiap jadwal akan menghasilkan slot booking berdasarkan durasi dan kuota.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('admin.jadwal-dokter.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="form-label">Dokter <span class="text-red-500">*</span></label>
                <select name="user_id" class="form-select-custom" required>
                    <option value="">-- Pilih --</option>
                    @foreach($dokters as $d)
                    <option value="{{ $d->id }}">dr. {{ $d->dokter->nama_dokter ?? $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Hari <span class="text-red-500">*</span></label>
                <select name="hari" class="form-select-custom" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                    <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jam Mulai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_mulai" class="form-input-custom" required>
            </div>
            <div>
                <label class="form-label">Jam Selesai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_selesai" class="form-input-custom" required>
            </div>
            <div>
                <label class="form-label">Durasi (menit) <span class="text-red-500">*</span></label>
                <input type="number" name="durasi_slot" class="form-input-custom" value="30" min="15" step="5" required>
            </div>
            <div>
                <label class="form-label">Kuota <span class="text-red-500">*</span></label>
                <input type="number" name="kuota" class="form-input-custom" value="10" min="0" required>
            </div>
        </div>
        <button type="submit" class="btn-primary mt-4">Tambah Jadwal</button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Hari</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam Praktik</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Durasi Slot</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kuota</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $j)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-medium">dr. {{ $j->dokter->dokter->nama_dokter ?? $j->dokter->name }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $j->hari }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $j->durasi_slot }} menit</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $j->kuota }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        <span class="badge-status {{ $j->status == 'aktif' ? 'badge-selesai' : 'bg-red-100 text-red-800' }}">{{ ucfirst($j->status) }}</span>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        <div class="flex gap-1">
                            <a href="{{ route('admin.jadwal-dokter.edit', $j->id) }}" class="btn-sm btn-primary">Edit</a>
                            <form action="{{ route('admin.jadwal-dokter.destroy', $j->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button class="btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>
@endpush
@endsection
