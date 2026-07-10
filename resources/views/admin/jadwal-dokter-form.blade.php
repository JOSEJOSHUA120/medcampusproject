@extends('layouts.admin')

@section('title', 'Edit Jadwal Dokter')

@section('content')
<div class="page-header">
    <h4>Edit Jadwal Dokter</h4>
    <p>Ubah jadwal praktik dokter.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <h5 class="font-bold text-gray-800 mb-5">
        <svg class="w-5 h-5 inline text-blue-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Jadwal
    </h5>
    <form action="{{ route('admin.jadwal-dokter.update', $jadwal->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
                <label class="form-label">Dokter <span class="text-red-500">*</span></label>
                <select name="user_id" class="form-input-custom" required>
                    @foreach($dokters as $d)
                    <option value="{{ $d->id }}" {{ $jadwal->user_id == $d->id ? 'selected' : '' }}>{{ $d->dokter->nama_dokter ?? $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Hari <span class="text-red-500">*</span></label>
                <select name="hari" class="form-input-custom" required>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                    <option value="{{ $hari }}" {{ $jadwal->hari == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jam Mulai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_mulai" class="form-input-custom" value="{{ substr($jadwal->jam_mulai, 0, 5) }}" required>
            </div>
            <div>
                <label class="form-label">Jam Selesai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_selesai" class="form-input-custom" value="{{ substr($jadwal->jam_selesai, 0, 5) }}" required>
            </div>
            <div>
                <label class="form-label">Durasi Slot (menit) <span class="text-red-500">*</span></label>
                <input type="number" name="durasi_slot" class="form-input-custom" value="{{ $jadwal->durasi_slot }}" min="15" step="5" required>
            </div>
            <div>
                <label class="form-label">Kuota <span class="text-red-500">*</span></label>
                <input type="number" name="kuota" class="form-input-custom" value="{{ $jadwal->kuota }}" min="0" required>
            </div>
            <div>
                <label class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" class="form-input-custom" required>
                    <option value="aktif" {{ $jadwal->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $jadwal->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="btn-primary">
                <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.jadwal-dokter') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition">Batal</a>
        </div>
    </form>
</div>
@endsection
