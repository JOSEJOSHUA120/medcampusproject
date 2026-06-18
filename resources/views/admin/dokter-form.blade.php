@extends('layouts.admin')

@section('title', isset($dokter) ? 'Edit Dokter' : 'Tambah Dokter')

@section('content')
<div class="page-header">
    <h4>{{ isset($dokter) ? 'Edit Dokter' : 'Tambah Dokter' }}</h4>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ isset($dokter) ? route('admin.dokter.update', $dokter->id) : route('admin.dokter.store') }}" method="POST">
        @csrf
        @if(isset($dokter)) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nama Dokter <span class="text-red-500">*</span></label>
                <input type="text" name="nama_dokter" class="form-input-custom @error('nama_dokter') border-red-500 @enderror" value="{{ old('nama_dokter', $dokter->nama_dokter ?? '') }}" required>
                @error('nama_dokter')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" class="form-input-custom @error('email') border-red-500 @enderror" value="{{ old('email', $dokter->user->email ?? '') }}" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Password @if(!isset($dokter))<span class="text-red-500">*</span>@endif</label>
                <input type="password" name="password" class="form-input-custom" {{ !isset($dokter) ? 'required' : '' }}>
                @if(isset($dokter))<p class="text-gray-400 text-xs mt-1">Kosongkan jika tidak ingin mengubah password.</p>@endif
            </div>
            <div>
                <label class="form-label">Spesialisasi <span class="text-red-500">*</span></label>
                <input type="text" name="spesialisasi" class="form-input-custom @error('spesialisasi') border-red-500 @enderror" value="{{ old('spesialisasi', $dokter->spesialisasi ?? '') }}" required>
                @error('spesialisasi')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">No. Telepon</label>
                <input type="text" name="no_telp" class="form-input-custom" value="{{ old('no_telp', $dokter->no_telp ?? '') }}">
            </div>
            {{-- Jam Praktik Mulai --}}
            <div>
                <label class="form-label">Jam Praktik Mulai</label>
                <input type="time" name="jam_praktek_mulai" class="form-input-custom" value="{{ old('jam_praktek_mulai', $dokter->jam_praktek_mulai ?? '') }}">
            </div>
            {{-- Jam Praktik Selesai --}}
            <div>
                <label class="form-label">Jam Praktik Selesai</label>
                <input type="time" name="jam_praktek_selesai" class="form-input-custom" value="{{ old('jam_praktek_selesai', $dokter->jam_praktek_selesai ?? '') }}">
            </div>
            {{-- Hari Praktik --}}
            <div class="md:col-span-2">
                <label class="form-label">Hari Praktik</label>
                <input type="text" name="hari_praktek" class="form-input-custom" value="{{ old('hari_praktek', $dokter->hari_praktek ?? '') }}" placeholder="Sen,Sel,Rab,Kam,Jum">
                <p class="text-gray-400 text-xs mt-1">Pisahkan dengan koma. Contoh: Sen,Sel,Rab,Kam,Jum</p>
            </div>
        </div>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">{{ isset($dokter) ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('admin.dokter') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
