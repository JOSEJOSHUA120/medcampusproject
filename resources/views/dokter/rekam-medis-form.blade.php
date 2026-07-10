@extends('layouts.dokter')

@section('title', isset($rekamMedis) ? 'Edit Rekam Medis' : 'Buat Rekam Medis')

@section('content')
<div class="page-header">
    <h4>{{ isset($rekamMedis) ? 'Edit Rekam Medis' : 'Buat Rekam Medis' }}</h4>
    <p>Isi data rekam medis pasien dengan lengkap dan teliti.</p>
</div>

@if(isset($antrian))
<div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl border border-primary-100 p-4 mb-5 flex items-center gap-4">
    <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden border-2 border-white shadow-sm">
        <img src="{{ $antrian->pasien->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($antrian->pasien->user->email ?? '') }}" alt="foto" class="w-full h-full object-cover">
    </div>
    <div class="text-sm">
        <span class="text-gray-500">Pasien:</span>
        <span class="font-semibold text-gray-800 ml-1">{{ $antrian->pasien->user->name ?? '-' }}</span>
        <span class="mx-2 text-gray-300">|</span>
        <span class="text-gray-500">No. Antrian:</span>
        <span class="font-semibold text-primary-700 ml-1">{{ $antrian->nomor_antrian }}</span>
        @if($antrian->complaint)
        <span class="mx-2 text-gray-300">|</span>
        <span class="text-gray-500">Keluhan:</span>
        <span class="text-gray-700 ml-1">{{ \Str::limit($antrian->complaint, 60) }}</span>
        @endif
    </div>
</div>
@elseif(isset($rekamMedis))
<div class="bg-gradient-to-r from-primary-50 to-blue-50 rounded-xl border border-primary-100 p-4 mb-5 flex items-center gap-4">
    <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden border-2 border-white shadow-sm">
        <img src="{{ $rekamMedis->pasien->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($rekamMedis->pasien->user->email ?? '') }}" alt="foto" class="w-full h-full object-cover">
    </div>
    <div class="text-sm">
        <span class="text-gray-500">Pasien:</span>
        <span class="font-semibold text-gray-800 ml-1">{{ $rekamMedis->pasien->user->name ?? '-' }}</span>
        @if($rekamMedis->created_at)
        <span class="mx-2 text-gray-300">|</span>
        <span class="text-gray-500">Tanggal:</span>
        <span class="text-gray-700 ml-1">{{ $rekamMedis->created_at->format('d M Y H:i') }}</span>
        @endif
    </div>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
        <h5 class="font-semibold text-gray-800 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Form Rekam Medis
        </h5>
    </div>
    <div class="p-6">
        <form action="{{ isset($rekamMedis) ? route('dokter.rekam-medis.update', $rekamMedis->id) : route('dokter.rekam-medis.store', $antrian->id) }}" method="POST">
            @csrf
            @if(isset($rekamMedis)) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        <label class="form-label !mb-0">Diagnosa <span class="text-red-500">*</span></label>
                    </div>
                    <textarea name="diagnosa" class="form-input-custom @error('diagnosa') border-red-400 @enderror" rows="3" placeholder="Tulis diagnosa pasien..." required>{{ old('diagnosa', $rekamMedis->diagnosa ?? '') }}</textarea>
                    @error('diagnosa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        <label class="form-label !mb-0">Tindakan</label>
                    </div>
                    <textarea name="tindakan" class="form-input-custom" rows="3" placeholder="Tindakan yang dilakukan...">{{ old('tindakan', $rekamMedis->tindakan ?? '') }}</textarea>
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        <label class="form-label !mb-0">Catatan Dokter</label>
                    </div>
                    <textarea name="catatan_dokter" class="form-input-custom" rows="3" placeholder="Catatan tambahan...">{{ old('catatan_dokter', $rekamMedis->catatan_dokter ?? '') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        <label class="form-label !mb-0">Resep Obat</label>
                    </div>
                    <textarea name="resep_obat" class="form-input-custom" rows="3" placeholder="Tulis resep obat&#10;Contoh:&#10;Paracetamol 500mg 3x1&#10;Amoxicillin 250mg 2x1&#10;Vitamin C 1x1">{{ old('resep_obat', $rekamMedis->resep_obat ?? '') }}</textarea>
                </div>
            </div>

            <hr class="my-6 border-gray-100">

            <div class="flex flex-wrap items-center justify-between gap-3">
                <button type="submit" name="action" value="simpan_selesai" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan &amp; Selesai
                </button>
                <a href="{{ route('dokter.rekam-medis') }}" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 text-sm font-medium text-gray-600 rounded-lg hover:bg-gray-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
