@extends('layouts.admin')

@section('title', isset($pasien) ? 'Edit Pasien' : 'Tambah Pasien')

@section('content')
<div class="page-header">
    <h4>{{ isset($pasien) ? 'Edit Pasien' : 'Tambah Pasien' }}</h4>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <form action="{{ isset($pasien) ? route('admin.pasien.update', $pasien->id) : route('admin.pasien.store') }}" method="POST">
        @csrf
        @if(isset($pasien)) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="nama" class="form-input-custom @error('nama') border-red-500 @enderror" value="{{ old('nama', $pasien->user->name ?? '') }}" required>
                @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" class="form-input-custom @error('email') border-red-500 @enderror" value="{{ old('email', $pasien->user->email ?? '') }}" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Password @if(!isset($pasien))<span class="text-red-500">*</span>@endif</label>
                <input type="password" name="password" class="form-input-custom" {{ !isset($pasien) ? 'required' : '' }}>
                @if(isset($pasien))<p class="text-gray-400 text-xs mt-1">Kosongkan jika tidak ingin mengubah password.</p>@endif
            </div>
            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="no_telp" class="form-input-custom" value="{{ old('no_telp', $pasien->no_telp ?? '') }}">
            </div>
            <div>
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-input-custom" value="{{ old('tempat_lahir', $pasien->tempat_lahir ?? '') }}" placeholder="Kota lahir">
            </div>
            <div>
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" max="{{ date('Y-m-d') }}" class="form-input-custom" value="{{ old('tanggal_lahir', $pasien->tanggal_lahir ?? '') }}">
            </div>
            <div>
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select-custom">
                    <option value="">-- Pilih --</option>
                    <option value="L" @selected(old('jenis_kelamin', $pasien->jenis_kelamin ?? '')=='L')>Laki-Laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $pasien->jenis_kelamin ?? '')=='P')>Perempuan</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-input-custom" rows="2">{{ old('alamat', $pasien->alamat ?? '') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">{{ isset($pasien) ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('admin.pasien') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
