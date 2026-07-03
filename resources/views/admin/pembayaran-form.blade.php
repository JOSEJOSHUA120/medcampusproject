@extends('layouts.admin')

@section('title', isset($pembayaran) ? 'Edit Pembayaran' : 'Tambah Pembayaran')

@section('content')
<div class="page-header">
    <h4>{{ isset($pembayaran) ? 'Edit Pembayaran' : 'Tambah Pembayaran' }}</h4>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ isset($pembayaran) ? route('admin.pembayaran.update', $pembayaran->id) : route('admin.pembayaran.store') }}" method="POST">
        @csrf
        @if(isset($pembayaran)) @method('PUT') @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Rekam Medis <span class="text-red-500">*</span></label>
                <select name="rekam_medis_id" class="form-select-custom @error('rekam_medis_id') border-red-500 @enderror" required>
                    <option value="">-- Pilih --</option>
                    @foreach($rekamMedisList as $rm)
                    <option value="{{ $rm->id }}" @selected(old('rekam_medis_id', $pembayaran->rekam_medis_id ?? '')==$rm->id)>
                        {{ $rm->pasien->user->name ?? '-' }} - {{ $rm->created_at->format('Y-m-d') }}
                        @if($rm->resepObat->count()) ({{ $rm->resepObat->count() }} obat) @endif
                    </option>
                    @endforeach
                </select>
                @error('rekam_medis_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Total Biaya <span class="text-red-500">*</span></label>
                <input type="number" name="total_biaya" class="form-input-custom @error('total_biaya') border-red-500 @enderror" value="{{ old('total_biaya', $pembayaran->total_biaya ?? '') }}" required>
                @error('total_biaya')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Metode Bayar</label>
                <select name="metode_bayar" class="form-select-custom">
                    <option value="">-- Pilih --</option>
                    <option value="Tunai" @selected(old('metode_bayar', $pembayaran->metode_bayar ?? '')=='Tunai')>Tunai</option>
                    <option value="Transfer" @selected(old('metode_bayar', $pembayaran->metode_bayar ?? '')=='Transfer')>Transfer</option>
                    <option value="QRIS" @selected(old('metode_bayar', $pembayaran->metode_bayar ?? '')=='QRIS')>QRIS</option>
                </select>
            </div>
            <div>
                <label class="form-label">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" class="form-input-custom" value="{{ old('tanggal_bayar', $pembayaran->tanggal_bayar ?? '') }}">
            </div>
            <div>
                <label class="form-label">Bank</label>
                <input type="text" name="bank" class="form-input-custom" value="{{ old('bank', $pembayaran->bank ?? '') }}" placeholder="Nama bank">
            </div>
            <div>
                <label class="form-label">Nomor Referensi</label>
                <input type="text" name="nomor_referensi" class="form-input-custom" value="{{ old('nomor_referensi', $pembayaran->nomor_referensi ?? '') }}" placeholder="Nomor referensi">
            </div>
            <div>
                <label class="form-label">Status Bayar <span class="text-red-500">*</span></label>
                <select name="status_bayar" class="form-select-custom" required>
                    <option value="belum_bayar" @selected(old('status_bayar', $pembayaran->status_bayar ?? '')=='belum_bayar')>Belum Bayar</option>
                    <option value="lunas" @selected(old('status_bayar', $pembayaran->status_bayar ?? '')=='lunas')>Lunas</option>
                </select>
            </div>
        </div>
        <div class="mt-6 flex gap-2">
            <button type="submit" class="btn-primary">{{ isset($pembayaran) ? 'Update' : 'Simpan' }}</button>
            <a href="{{ route('admin.pembayaran') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
