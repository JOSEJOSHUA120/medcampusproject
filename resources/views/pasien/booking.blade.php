@extends('layouts.pasien')

@section('title', 'Booking Dokter')

@section('content')
<div class="page-header">
    <h4>Booking Janji Temu Dokter</h4>
    <p>Pilih dokter, tanggal, dan jam yang tersedia.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
    <form action="{{ route('pasien.booking.store') }}" method="POST" id="formBooking">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="form-label">Pilih Dokter <span class="text-red-500">*</span></label>
                <select name="dokter_id" id="dokter_id" class="form-select-custom @error('dokter_id') border-red-400 @enderror" required>
                    <option value="">-- Pilih Dokter --</option>
                    @foreach($dokters as $d)
                    <option value="{{ $d->id }}" data-spesialisasi="{{ $d->dokter->spesialisasi ?? '-' }}">
                        {{ $d->dokter->nama_dokter ?? $d->name }} ({{ $d->dokter->spesialisasi ?? '-' }})
                    </option>
                    @endforeach
                </select>
                @error('dokter_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

                <div id="jadwalInfo" class="mt-2 hidden">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Jadwal praktik:</p>
                    <div id="jadwalList" class="flex flex-wrap gap-1"></div>
                </div>
            </div>
            <div>
                <label class="form-label">Keluhan <span class="text-red-500">*</span></label>
                <textarea name="keluhan_awal" id="keluhan_awal" class="form-input-custom @error('keluhan_awal') border-red-400 @enderror" rows="1" placeholder="Tulis keluhan Anda..." required>{{ old('keluhan_awal') }}</textarea>
                @error('keluhan_awal')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Pilih Tanggal <span class="text-red-500">*</span></label>
            <div class="flex items-center gap-1">
                <button type="button" id="prevPage" class="w-9 h-[58px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition flex items-center justify-center flex-shrink-0" title="Sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="flex flex-wrap gap-1.5 flex-1" id="datePages"></div>
                <button type="button" id="nextPage" class="w-9 h-[58px] rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-700 dark:hover:text-gray-200 transition flex items-center justify-center flex-shrink-0" title="Selanjutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <input type="hidden" name="tanggal_booking" id="tanggal_booking" value="{{ now()->format('Y-m-d') }}">
        </div>

        <div class="mb-4" id="slotSection" style="display:none">
            <label class="form-label">Pilih Jam <span class="text-red-500">*</span></label>
            <div class="flex flex-wrap gap-2" id="slotGrid">
                <span class="text-gray-400 text-sm">Pilih dokter dan tanggal terlebih dahulu.</span>
            </div>
            <input type="hidden" name="jam_booking" id="jam_booking" value="">
            <input type="hidden" name="jadwal_dokter_id" id="jadwal_dokter_id" value="">
            <p id="slotError" class="text-red-500 text-xs mt-1 hidden">Silakan pilih jam tersedia.</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="btn-primary" id="submitBtn">Buat Booking</button>
            <a href="{{ route('pasien.dashboard') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let csrfToken = '{{ csrf_token() }}';
let selectedJam = '';
let currentPage = 0;
const PER_PAGE = 10;

const dayMap = { 'Minggu':0,'Senin':1,'Selasa':2,'Rabu':3,'Kamis':4,'Jumat':5,'Sabtu':6 };
const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
const dayNamesFull = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

let dokterSelect = document.getElementById('dokter_id');
let tanggalInput = document.getElementById('tanggal_booking');
let jamInput = document.getElementById('jam_booking');
let jadwalIdInput = document.getElementById('jadwal_dokter_id');
let slotGrid = document.getElementById('slotGrid');
let slotSection = document.getElementById('slotSection');
let datePages = document.getElementById('datePages');
let prevBtn = document.getElementById('prevPage');
let nextBtn = document.getElementById('nextPage');
let jadwalInfo = document.getElementById('jadwalInfo');
let jadwalList = document.getElementById('jadwalList');

let doctorSchedules = {};
@foreach($dokters as $d)
doctorSchedules[{{ $d->id }}] = [
    @foreach($d->jadwalDokter->where('status', 'aktif') as $j)
    { hari: '{{ $j->hari }}', jam: '{{ substr($j->jam_mulai,0,5) }}-{{ substr($j->jam_selesai,0,5) }}' },
    @endforeach
];
@endforeach

function getDayIndex(hari) {
    return dayMap[hari] !== undefined ? dayMap[hari] : -1;
}

function renderDates() {
    let dokterId = dokterSelect.value;
    let activeDays = [];
    if (dokterId && doctorSchedules[dokterId]) {
        activeDays = doctorSchedules[dokterId].map(s => getDayIndex(s.hari)).filter(d => d >= 0);
    }

    let start = currentPage * PER_PAGE;
    let html = '';
    for (let i = 0; i < PER_PAGE; i++) {
        let d = new Date();
        d.setDate(d.getDate() + start + i);
        let y = d.getFullYear();
        let m = String(d.getMonth() + 1).padStart(2, '0');
        let day = String(d.getDate()).padStart(2, '0');
        let dateStr = y + '-' + m + '-' + day;
        let isActive = tanggalInput.value === dateStr;
        let label = (start + i === 0) ? 'Hari ini' : dayNames[d.getDay()];
        let hasJadwal = activeDays.length === 0 || activeDays.includes(d.getDay());

        let baseClass = 'px-3 py-2 rounded-lg border text-xs font-medium transition-all duration-150 ';
        if (isActive) {
            baseClass += 'bg-primary-600 text-white border-primary-600 shadow-sm';
        } else if (!hasJadwal) {
            baseClass += 'bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed';
        } else {
            baseClass += 'bg-white text-gray-700 border-gray-200 hover:border-primary-300 hover:bg-primary-50';
        }

        html += '<button type="button" class="date-btn ' + baseClass + '" data-date="' + dateStr + '" ' +
            (!hasJadwal ? 'disabled' : '') + '>' +
            '<div class="font-bold text-sm">' + day + '</div>' +
            '<div class="text-[10px] opacity-75">' + label + '</div>' +
            '</button>';
    }
    datePages.innerHTML = html;

    prevBtn.disabled = currentPage === 0;
    prevBtn.classList.toggle('opacity-40', currentPage === 0);
    prevBtn.classList.toggle('cursor-not-allowed', currentPage === 0);

    document.querySelectorAll('.date-btn:not([disabled])').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.date-btn').forEach(b => {
                b.classList.remove('bg-primary-600', 'text-white', 'border-primary-600', 'shadow-sm');
                b.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
                if (b.disabled) {
                    b.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                    b.classList.add('bg-gray-50', 'text-gray-300', 'border-gray-100');
                }
            });
            this.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
            this.classList.add('bg-primary-600', 'text-white', 'border-primary-600', 'shadow-sm');
            tanggalInput.value = this.dataset.date;
            if (dokterSelect.value) loadSlots();
        });
    });
}

prevBtn.addEventListener('click', function() {
    if (currentPage > 0) {
        currentPage--;
        renderDates();
        if (dokterSelect.value) loadSlots();
    }
});

nextBtn.addEventListener('click', function() {
    currentPage++;
    renderDates();
    if (dokterSelect.value) loadSlots();
});

dokterSelect.addEventListener('change', function() {
    let val = this.value;
    if (val && doctorSchedules[val]) {
        let html = '';
        doctorSchedules[val].forEach(function(s) {
            html += '<span class="inline-block bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1 rounded-full border border-blue-100">' +
                s.hari + ' ' + s.jam + '</span>';
        });
        jadwalList.innerHTML = html;
        jadwalInfo.classList.remove('hidden');
    } else {
        jadwalInfo.classList.add('hidden');
    }
    renderDates();
    if (val && tanggalInput.value) loadSlots();
});

renderDates();

function loadSlots() {
    let dokterId = dokterSelect.value;
    let tanggal = tanggalInput.value;

    if (!dokterId || !tanggal) return;

    slotGrid.innerHTML = '<span class="text-gray-400 text-sm">Memuat slot...</span>';
    slotSection.style.display = 'none';
    selectedJam = '';
    jamInput.value = '';
    jadwalIdInput.value = '';

    fetch('{{ route("pasien.booking.slots") }}?dokter_id=' + dokterId + '&tanggal=' + tanggal, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
    })
    .then(res => res.json())
    .then(data => {
        let slots = data.slots;
        jadwalIdInput.value = data.jadwal_id || '';

        if (!slots || slots.length === 0 || !data.jadwal_id) {
            slotGrid.innerHTML = '<span class="text-gray-400 text-sm">Dokter tidak praktik di tanggal ini.</span>';
            return;
        }

        slotSection.style.display = 'block';

        let tersedia = false;
        let html = '';
        slots.forEach(function(slot) {
            let disabled = !slot.tersedia;
            html += '<button type="button" class="slot-btn px-4 py-2.5 rounded-lg border text-sm font-medium transition-all duration-150 ' +
                (disabled
                    ? 'bg-gray-100 text-gray-300 border-gray-100 cursor-not-allowed'
                    : 'bg-white text-gray-800 border-gray-200 hover:border-primary-400 hover:bg-primary-50 hover:text-primary-700') +
                '" data-jam="' + slot.jam + '" ' + (disabled ? 'disabled' : '') + '>' +
                slot.jam +
                (disabled ? '<br><span class="text-[10px]">Penuh</span>' : '') +
                '</button>';
            if (slot.tersedia) tersedia = true;
        });
        slotGrid.innerHTML = html;

        if (!tersedia) {
            slotGrid.innerHTML = '<span class="text-gray-400 text-sm">Semua slot sudah dibooking.</span>';
            return;
        }

        document.querySelectorAll('.slot-btn:not([disabled])').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.slot-btn').forEach(b => {
                    b.classList.remove('bg-primary-600', 'text-white', 'border-primary-600', 'shadow-sm');
                    if (!b.disabled) {
                        b.classList.add('bg-white', 'text-gray-800', 'border-gray-200');
                    }
                });
                this.classList.remove('bg-white', 'text-gray-800', 'border-gray-200');
                this.classList.add('bg-primary-600', 'text-white', 'border-primary-600', 'shadow-sm');
                selectedJam = this.dataset.jam;
                jamInput.value = selectedJam;
                document.getElementById('slotError').classList.add('hidden');
            });
        });
    })
    .catch(() => {
        slotGrid.innerHTML = '<span class="text-gray-400 text-sm">Gagal memuat slot.</span>';
    });
}

document.getElementById('formBooking').addEventListener('submit', function(e) {
    if (!jamInput.value) {
        e.preventDefault();
        document.getElementById('slotError').classList.remove('hidden');
    }
});
</script>
@endpush
@endsection