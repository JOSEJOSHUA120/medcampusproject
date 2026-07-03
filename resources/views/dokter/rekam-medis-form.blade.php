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

        <div class="mb-4">
            <label class="form-label font-bold">Daftar Obat & Harga</label>
            <p class="text-xs text-gray-400 mb-2">Pilih obat dari katalog, harga otomatis dihitung.</p>
            <div id="obatContainer">
                @if(isset($rekamMedis) && $rekamMedis->resepObat->count())
                    @foreach($rekamMedis->resepObat as $ro)
                    <div class="obat-row grid grid-cols-12 gap-2 mb-2 items-end">
                        <div class="col-span-6">
                            <select name="obat_id[]" class="form-select-custom w-full obat-select">
                                <option value="">-- Pilih Obat --</option>
                                @foreach($daftarObat as $o)
                                <option value="{{ $o->id }}" data-harga="{{ $o->harga }}" @selected($ro->obat_id == $o->id)>{{ $o->nama_obat }} - Rp {{ number_format($o->harga, 0, ',', '.') }}/{{ $o->satuan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="jumlah[]" class="form-input-custom w-full obat-jumlah" value="{{ $ro->jumlah }}" min="1" placeholder="Jml">
                        </div>
                        <div class="col-span-3">
                            <input type="text" class="form-input-custom w-full bg-gray-50 obat-subtotal" value="Rp {{ number_format($ro->subtotal, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="col-span-1">
                            <button type="button" class="btn-sm btn-danger w-full remove-obat">&times;</button>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="obat-row grid grid-cols-12 gap-2 mb-2 items-end">
                    <div class="col-span-6">
                        <select name="obat_id[]" class="form-select-custom w-full obat-select">
                            <option value="">-- Pilih Obat --</option>
                            @foreach($daftarObat as $o)
                            <option value="{{ $o->id }}" data-harga="{{ $o->harga }}">{{ $o->nama_obat }} - Rp {{ number_format($o->harga, 0, ',', '.') }}/{{ $o->satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="jumlah[]" class="form-input-custom w-full obat-jumlah" value="1" min="1" placeholder="Jml">
                    </div>
                    <div class="col-span-3">
                        <input type="text" class="form-input-custom w-full bg-gray-50 obat-subtotal" value="Rp 0" readonly>
                    </div>
                    <div class="col-span-1">
                        <button type="button" class="btn-sm btn-danger w-full remove-obat">&times;</button>
                    </div>
                </div>
                @endif
            </div>
            <button type="button" id="addObat" class="btn-sm btn-primary mt-2">+ Tambah Obat</button>
        </div>

        <div class="mb-4 p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-600">Total Biaya Otomatis:</span>
                <span id="totalBiayaDisplay" class="text-xl font-bold text-primary-600">Rp 0</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary">{{ isset($rekamMedis) ? 'Update' : 'Simpan & Selesai' }}</button>
            <a href="{{ route('dokter.rekam-medis') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
const daftarObat = @json($daftarObat);

function hitungTotal() {
    let total = 0;
    document.querySelectorAll('.obat-row').forEach(function(row) {
        const select = row.querySelector('.obat-select');
        const jumlah = parseInt(row.querySelector('.obat-jumlah').value) || 0;
        const harga = select.selectedOptions.length > 0 && select.selectedOptions[0].dataset.harga ? parseFloat(select.selectedOptions[0].dataset.harga) : 0;
        const subtotal = harga * jumlah;
        row.querySelector('.obat-subtotal').value = 'Rp ' + subtotal.toLocaleString('id-ID');
        total += subtotal;
    });
    document.getElementById('totalBiayaDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
    hitungTotal();
    document.getElementById('addObat').addEventListener('click', function() {
        const container = document.getElementById('obatContainer');
        const firstRow = container.querySelector('.obat-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelectorAll('select, input').forEach(function(el) {
            if (el.classList.contains('obat-select')) el.selectedIndex = 0;
            if (el.classList.contains('obat-jumlah')) el.value = 1;
            if (el.classList.contains('obat-subtotal')) el.value = 'Rp 0';
        });
        container.appendChild(newRow);
        attachEvents(newRow);
        hitungTotal();
    });

    document.querySelectorAll('.remove-obat').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const rows = document.querySelectorAll('.obat-row');
            if (rows.length > 1) {
                this.closest('.obat-row').remove();
                hitungTotal();
            }
        });
    });

    function attachEvents(row) {
        row.querySelector('.obat-select').addEventListener('change', hitungTotal);
        row.querySelector('.obat-jumlah').addEventListener('input', hitungTotal);
        row.querySelector('.remove-obat').addEventListener('click', function() {
            const rows = document.querySelectorAll('.obat-row');
            if (rows.length > 1) {
                this.closest('.obat-row').remove();
                hitungTotal();
            }
        });
    }

    document.querySelectorAll('.obat-row').forEach(function(row) {
        attachEvents(row);
    });
});
</script>
@endpush
@endsection
