@extends('layouts.admin')

@section('title', isset($obat) ? 'Edit Obat' : 'Tambah Obat')

@section('content')
<div class="page-header">
    <h4>{{ isset($obat) ? 'Edit Obat' : 'Tambah Obat' }}</h4>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <form action="{{ isset($obat) ? route('admin.obat.update', $obat->id) : route('admin.obat.store') }}" method="POST">
        @csrf
        @if(isset($obat)) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Nama Obat <span class="text-red-500">*</span></label>
                <input type="text" name="nama_obat" class="form-input-custom @error('nama_obat') border-red-500 @enderror" value="{{ old('nama_obat', $obat->nama_obat ?? '') }}" required>
                @error('nama_obat')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Harga (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="harga" class="form-input-custom @error('harga') border-red-500 @enderror" value="{{ old('harga', $obat->harga ?? '') }}" required min="0">
                @error('harga')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                <select name="satuan" class="form-select-custom" required>
                    <option value="tablet" @selected(old('satuan', $obat->satuan ?? '')=='tablet')>Tablet</option>
                    <option value="kapsul" @selected(old('satuan', $obat->satuan ?? '')=='kapsul')>Kapsul</option>
                    <option value="botol" @selected(old('satuan', $obat->satuan ?? '')=='botol')>Botol</option>
                    <option value="strip" @selected(old('satuan', $obat->satuan ?? '')=='strip')>Strip</option>
                    <option value="vial" @selected(old('satuan', $obat->satuan ?? '')=='vial')>Vial</option>
                    <option value="tube" @selected(old('satuan', $obat->satuan ?? '')=='tube')>Tube</option>
                    <option value="buah" @selected(old('satuan', $obat->satuan ?? '')=='buah')>Buah</option>
                </select>
            </div>
            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" class="form-input-custom" value="{{ old('keterangan', $obat->keterangan ?? '') }}" placeholder="Opsional">
            </div>
        </div>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">{{ isset($obat) ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('admin.obat') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
