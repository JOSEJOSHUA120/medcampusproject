@extends('layouts.admin')

@section('title', isset($room) ? 'Edit Ruangan' : 'Tambah Ruangan')

@section('content')
<div class="page-header">
    <h4>{{ isset($room) ? 'Edit Ruangan' : 'Tambah Ruangan' }}</h4>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ isset($room) ? route('admin.rooms.update', $room->id) : route('admin.rooms.store') }}" method="POST">
        @csrf
        @if(isset($room)) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nomor Ruangan <span class="text-red-500">*</span></label>
                <input type="text" name="room_number" class="form-input-custom @error('room_number') border-red-500 @enderror" value="{{ old('room_number', $room->room_number ?? '') }}" required placeholder="Contoh: R.101">
                @error('room_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" class="form-input-custom" rows="3" placeholder="Deskripsi ruangan (opsional)">{{ old('description', $room->description ?? '') }}</textarea>
            </div>
        </div>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">{{ isset($room) ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('admin.rooms') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
