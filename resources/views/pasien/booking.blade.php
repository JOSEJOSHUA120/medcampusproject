@extends('layouts.pasien')

@section('title', 'Booking Dokter')

@section('content')
<div class="page-header">
    <h4>Booking Janji Temu Dokter</h4>
    <p>Pilih dokter, tanggal, dan jam yang tersedia untuk membuat janji temu.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('pasien.booking.store') }}" method="POST" id="formBooking">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="form-label">Pilih Dokter <span class="text-red-500">*</span></label>
                <select name="dokter_id" id="dokter_id" class="form-select-custom @error('dokter_id') border-red-400 @enderror" required>
                    <option value="">-- Pilih Dokter --</option>
                    @foreach($dokters as $d)
                    <option value="{{ $d->id }}" data-spesialisasi="{{ $d->dokter->spesialisasi ?? '-' }}">
                        dr. {{ $d->dokter->nama_dokter ?? $d->name }} ({{ $d->dokter->spesialisasi ?? '-' }})
                    </option>
                    @endforeach
                </select>
                @error('dokter_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_booking" id="tanggal_booking" class="form-input-custom @error('tanggal_booking') border-red-400 @enderror"
                    min="{{ date('Y-m-d') }}" value="{{ old('tanggal_booking') }}" required>
                @error('tanggal_booking')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Pilih Jam <span class="text-red-500">*</span></label>
                <select name="jam_booking" id="jam_booking" class="form-select-custom @error('jam_booking') border-red-400 @enderror" required>
                    <option value="">-- Pilih Dokter & Tanggal dulu --</option>
                </select>
                @error('jam_booking')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Keluhan <span class="text-red-500">*</span></label>
                <textarea name="keluhan_awal" class="form-input-custom @error('keluhan_awal') border-red-400 @enderror" rows="1" required>{{ old('keluhan_awal') }}</textarea>
                @error('keluhan_awal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <input type="hidden" name="jadwal_dokter_id" id="jadwal_dokter_id" value="">
        <div class="flex gap-3">
            <button type="submit" class="btn-primary">Buat Booking</button>
            <a href="{{ route('pasien.dashboard') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($dokters as $d)
    <div class="card-dashboard p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                {{ substr($d->dokter->nama_dokter ?? $d->name, 0, 1) }}
            </div>
            <div>
                <h6 class="font-bold text-gray-800 text-sm">dr. {{ $d->dokter->nama_dokter ?? $d->name }}</h6>
                <span class="text-xs text-gray-400">{{ $d->dokter->spesialisasi ?? 'Dokter Umum' }}</span>
            </div>
        </div>
        @if($d->jadwalDokter->where('status', 'aktif')->count())
        <div class="text-xs text-gray-500">
            @foreach($d->jadwalDokter->where('status', 'aktif') as $j)
            <span class="inline-block mr-1 mb-1 bg-gray-100 px-2 py-0.5 rounded">{{ $j->hari }} {{ substr($j->jam_mulai, 0, 5) }}-{{ substr($j->jam_selesai, 0, 5) }}</span>
            @endforeach
        </div>
        @else
        <span class="text-xs text-red-400">Belum ada jadwal tersedia</span>
        @endif
    </div>
    @endforeach
</div>

@push('scripts')
<script>
let csrfToken = '{{ csrf_token() }}';

document.getElementById('dokter_id').addEventListener('change', loadSlots);
document.getElementById('tanggal_booking').addEventListener('change', loadSlots);

function loadSlots() {
    let dokterId = document.getElementById('dokter_id').value;
    let tanggal = document.getElementById('tanggal_booking').value;
    let jamSelect = document.getElementById('jam_booking');
    let jadwalIdInput = document.getElementById('jadwal_dokter_id');

    if (!dokterId || !tanggal) {
        jamSelect.innerHTML = '<option value="">-- Pilih Dokter & Tanggal dulu --</option>';
        jadwalIdInput.value = '';
        return;
    }

    jamSelect.innerHTML = '<option value="">-- Memuat slot... --</option>';

    fetch('{{ route("pasien.booking.slots") }}?dokter_id=' + dokterId + '&tanggal=' + tanggal, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(res => res.json())
    .then(data => {
        jamSelect.innerHTML = '<option value="">-- Memuat slot... --</option>';
        let slots = data.slots;
        jadwalIdInput.value = data.jadwal_id || '';
        if (!slots || slots.length === 0) {
            jamSelect.innerHTML = '<option value="">-- Dokter tidak praktik di tanggal ini --</option>';
            jadwalIdInput.value = '';
            return;
        }
        let html = '<option value="">-- Pilih Jam --</option>';
        let tersedia = false;
        slots.forEach(function(slot) {
            if (slot.tersedia) {
                html += '<option value="' + slot.jam + '">' + slot.jam + '</option>';
                tersedia = true;
            } else {
                html += '<option value="' + slot.jam + '" disabled>' + slot.jam + ' (Sudah dibooking)</option>';
            }
        });
        jamSelect.innerHTML = html;
        if (!tersedia) {
            jamSelect.innerHTML = '<option value="">-- Semua slot sudah dibooking --</option>';
        }
    })
    .catch(() => {
        jamSelect.innerHTML = '<option value="">-- Gagal memuat slot --</option>';
    });
}
</script>
@endpush
@endsection
