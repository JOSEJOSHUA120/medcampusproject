@extends('layouts.admin')

@section('title', 'Edit Jadwal Dokter')

@section('content')
<div class="page-header">
    <h4>Edit Jadwal Dokter</h4>
    <p>Ubah jadwal praktik dokter.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-3xl">
    <form action="{{ route('admin.jadwal-dokter.update', $jadwal->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="form-label">Dokter <span class="text-red-500">*</span></label>
                <select name="user_id" class="form-select-custom" required>
                    @foreach($dokters as $d)
                    <option value="{{ $d->id }}" {{ $jadwal->user_id == $d->id ? 'selected' : '' }}>dr. {{ $d->dokter->nama_dokter ?? $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Hari <span class="text-red-500">*</span></label>
                <select name="hari" class="form-select-custom" required>
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
                <select name="status" class="form-select-custom" required>
                    <option value="aktif" {{ $jadwal->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $jadwal->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Simpan</button>
            <a href="{{ route('admin.jadwal-dokter') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
