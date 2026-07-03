@extends('layouts.dokter')

@section('title', isset($rekamMedis) ? 'Edit Rekam Medis' : 'Buat Rekam Medis')

@section('content')
<div class="page-header">
    <h4>{{ isset($rekamMedis) ? 'Edit Rekam Medis' : 'Buat Rekam Medis' }}</h4>
    @if(isset($antrian))
    <p>Pasien: <strong>{{ $antrian->pasien->user->name ?? '-' }}</strong> | No. Antrian: {{ $antrian->nomor_antrian }}</p>
    @endif
</div>

<div class="card-dashboard p-6">
    <form action="{{ isset($rekamMedis) ? route('dokter.rekam-medis.update', $rekamMedis->id) : route('dokter.rekam-medis.store', $antrian->id) }}" method="POST">
        @csrf
        @if(isset($rekamMedis)) @method('PUT') @endif

        <div class="mb-4">
            <label class="form-label">Diagnosa <span class="text-red-500">*</span></label>
            <textarea name="diagnosa" class="form-input-custom @error('diagnosa') border-red-400 @enderror" rows="3" required>{{ old('diagnosa', $rekamMedis->diagnosa ?? '') }}</textarea>
            @error('diagnosa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Tindakan</label>
            <textarea name="tindakan" class="form-input-custom" rows="3">{{ old('tindakan', $rekamMedis->tindakan ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label">Catatan Dokter</label>
            <textarea name="catatan_dokter" class="form-input-custom" rows="3">{{ old('catatan_dokter', $rekamMedis->catatan_dokter ?? '') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label">Resep Obat (Catatan Tambahan)</label>
            <textarea name="resep_obat" class="form-input-custom" rows="2" placeholder="Paracetamol 500mg 3x1&#10;Amoxicillin 250mg 2x1">{{ old('resep_obat', $rekamMedis->resep_obat ?? '') }}</textarea>
        </div>


        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">{{ isset($rekamMedis) ? 'Update' : 'Simpan & Selesai' }}</button>
            <a href="{{ route('dokter.rekam-medis') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
