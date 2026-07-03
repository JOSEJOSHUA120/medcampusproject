@extends('layouts.pasien')

@section('title', 'Ambil Antrian')

@section('content')
<div class="page-header">
    <h4>Ambil Antrian</h4>
    <p>Pilih dokter untuk mengambil nomor antrian.</p>
</div>

<div class="card-dashboard p-6 mb-6">
    <form action="{{ route('pasien.antrian.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label">Pilih Dokter <span class="text-red-500">*</span></label>
            <select name="dokter_id" class="form-select-custom @error('dokter_id') border-red-400 @enderror" required>
                <option value="">-- Pilih Dokter --</option>
                @foreach($dokter as $d)
                <option value="{{ $d->id }}">
                    dr. {{ $d->user->name ?? $d->nama_dokter }} ({{ $d->spesialisasi }})
                </option>
                @endforeach
            </select>
            @error('dokter_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">Ambil Antrian</button>
            <a href="{{ route('pasien.dashboard') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

@if($dokter->count())
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($dokter as $d)
    <div class="card-dashboard p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                {{ substr($d->user->name ?? $d->nama_dokter, 0, 1) }}
            </div>
            <div>
                <h6 class="font-bold text-gray-800 text-sm">dr. {{ $d->user->name ?? $d->nama_dokter }}</h6>
                <span class="text-xs text-gray-400">{{ $d->spesialisasi }}</span>
            </div>
        </div>
        <div class="mt-2 inline-block px-2 py-0.5 bg-green-100 text-green-600 text-xs rounded-full font-medium">
            Tersedia
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
