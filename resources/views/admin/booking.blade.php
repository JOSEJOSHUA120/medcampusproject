@extends('layouts.admin')

@section('title', 'Kelola Booking')

@section('content')
<div class="page-header">
    <div class="flex items-center justify-between">
        <div>
            <h4>Kelola Booking</h4>
            <p>Kelola semua booking pasien.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-white/70 text-xs">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-6">
    <div class="stat-card border-l-4 border-l-primary-500 cursor-pointer filter-tab active" data-filter="all" onclick="filterTable('all')">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Total</p>
                <p class="stat-value text-primary-600">{{ $totalBooking }}</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </span>
        </div>
    </div>
    <div class="stat-card border-l-4 border-l-amber-400 cursor-pointer filter-tab" data-filter="menunggu" onclick="filterTable('menunggu')">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Menunggu</p>
                <p class="stat-value text-amber-600">{{ $menunggu }}</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
    </div>
    <div class="stat-card border-l-4 border-l-blue-400 cursor-pointer filter-tab" data-filter="aktif" onclick="filterTable('aktif')">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Aktif</p>
                <p class="stat-value text-blue-600">{{ $disetujui }}</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
        </div>
    </div>
    <div class="stat-card border-l-4 border-l-green-400 cursor-pointer filter-tab" data-filter="selesai" onclick="filterTable('selesai')">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Selesai</p>
                <p class="stat-value text-green-600">{{ $selesai }}</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </span>
        </div>
    </div>
    <div class="stat-card border-l-4 border-l-red-400 cursor-pointer filter-tab" data-filter="ditolak" onclick="filterTable('ditolak')">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label">Ditolak</p>
                <p class="stat-value text-red-600">{{ $ditolak }}</p>
            </div>
            <span class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </span>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 lg:p-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-400 flex items-center justify-center">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <h5 class="font-bold text-gray-800 dark:text-white">Daftar Booking</h5>
                <p class="text-xs text-gray-400 dark:text-gray-500">Gunakan filter atau kolom pencarian</p>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table id="bookingTable" class="w-full text-left display" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Pasien</th>
                    <th>Dokter</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Keluhan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $i => $b)
                @php
                    $pasienUser = $b->pasien;
                    $profil = $pasienUser?->pasien;
                    $statusClass = match($b->status) {
                        'menunggu' => 'badge-menunggu',
                        'disetujui', 'dipanggil' => 'badge-dipanggil',
                        'ditolak', 'dibatalkan' => 'badge-batal',
                        'check_in' => 'bg-indigo-100 text-indigo-800',
                        'tidak_hadir' => 'bg-gray-100 text-gray-800',
                        'selesai' => 'badge-selesai',
                        'kadaluarsa' => 'bg-orange-100 text-orange-800',
                        default => 'bg-gray-100 text-gray-800',
                    };
                    $statusDot = match($b->status) {
                        'menunggu' => 'bg-amber-400',
                        'disetujui', 'dipanggil' => 'bg-blue-500',
                        'check_in' => 'bg-indigo-500',
                        'selesai' => 'bg-green-500',
                        'ditolak', 'dibatalkan' => 'bg-red-500',
                        'kadaluarsa' => 'bg-orange-500',
                        'tidak_hadir' => 'bg-gray-400',
                        default => 'bg-gray-400',
                    };
                    $filterGroup = in_array($b->status, ['disetujui', 'dipanggil', 'check_in']) ? 'aktif' : $b->status;
                @endphp
                <tr data-status="{{ $filterGroup }}">
                    <td class="font-medium text-gray-900 dark:text-white">{{ $i + 1 }}</td>
                    <td>
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $profil->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($pasienUser->email ?? '') }}" alt="foto" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-600 flex-shrink-0">
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm leading-tight">{{ $pasienUser->name ?? '-' }}</div>
                                <div class="text-[11px] text-gray-400 dark:text-gray-500">{{ $profil->no_telp ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-medium text-gray-700 dark:text-gray-300">{{ $b->dokter->name }}</td>
                    <td class="text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}</td>
                    <td class="font-semibold text-gray-800 dark:text-gray-200">@php try { echo \Carbon\Carbon::parse($b->jam_booking)->format('H:i'); } catch(\Exception $e) { echo '-'; } @endphp</td>
                    <td class="text-gray-600 dark:text-gray-400 max-w-[160px]">
                        <span class="truncate block" title="{{ $b->keluhan_awal }}">{{ $b->keluhan_awal ?? '-' }}</span>
                    </td>
                    <td>
                        <span class="badge-status {{ $statusClass }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }} mr-1.5"></span>
                            {{ ucfirst(str_replace('_', ' ', $b->status)) }}
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-1.5">
                            @if($b->status == 'menunggu')
                            <form action="{{ route('admin.booking.approve', $b->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Setujui
                                </button>
                            </form>
                            <button onclick="openReject({{ $b->id }})" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 text-sm font-semibold rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak
                            </button>
                            @elseif($b->status == 'check_in')
                            <form action="{{ route('admin.booking.selesai', $b->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </button>
                            </form>
                            @else
                            <span class="text-gray-300 dark:text-gray-600 text-xs px-2 italic">Tidak ada aksi</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="modalReject" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-md p-6 animate-fade-in">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                </div>
                <div>
                    <h5 class="font-bold text-gray-800 dark:text-white">Tolak Booking</h5>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Booking akan ditandai sebagai ditolak</p>
                </div>
            </div>
            <button onclick="closeModal()" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="formReject" method="POST">
            @csrf @method('PUT')
            <div class="mb-5">
                <label class="form-label">Alasan Penolakan</label>
                <textarea name="catatan" class="form-input-custom" rows="3" placeholder="Tulis alasan penolakan (opsional)"></textarea>
            </div>
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Konfirmasi Tolak
            </button>
        </form>
    </div>
</div>

@push('scripts')
<style>
#bookingTable_filter { margin-bottom: 16px; }
#bookingTable_filter label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6B7280; }
#bookingTable_filter input { border: 1px solid #E5E7EB; border-radius: 10px; padding: 8px 14px; font-size: 13px; width: 260px; outline: none; transition: all 0.2s; background: #F9FAFB; }
#bookingTable_filter input:focus { border-color: #6366F1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); background: #fff; }
.dataTables_wrapper .dataTables_length select { padding-top: 6px; padding-bottom: 6px; }
.filter-tab { transition: all 0.2s; }
.filter-tab.active { transform: scale(1.02); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
.filter-tab:not(.active) { opacity: 0.75; }
.filter-tab:not(.active):hover { opacity: 1; }
@keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
.animate-fade-in { animation: fadeIn 0.2s ease-out; }
.dataTables_wrapper .dataTables_paginate .paginate_button { border-radius: 8px; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #6366F1; border-color: #6366F1; }
</style>
<script>
function openReject(id) {
    document.getElementById('formReject').action = "{{ url('admin/booking') }}/" + id + "/reject";
    document.getElementById('modalReject').classList.remove('hidden');
    document.getElementById('modalReject').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalReject').classList.add('hidden');
    document.getElementById('modalReject').classList.remove('flex');
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalReject').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    window.table = $('#bookingTable').DataTable({
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data",
            infoFiltered: "(difilter dari _MAX_ total data)",
            zeroRecords: "Tidak ada data yang cocok",
            paginate: { first: "Pertama", last: "Terakhir", next: "&raquo;", previous: "&laquo;" }
        }
    });
});

function filterTable(filter) {
    document.querySelectorAll('.filter-tab').forEach(el => el.classList.remove('active'));
    document.querySelector(`.filter-tab[data-filter="${filter}"]`).classList.add('active');
    if (filter === 'all') {
        window.table.search('').columns().search('').draw();
    } else if (filter === 'aktif') {
        window.table.search('').columns().search('').draw();
        window.table.column(6).search('Disetujui|Dipanggil|Check in', true, false).draw();
    } else if (filter === 'ditolak') {
        window.table.search('').columns().search('').draw();
        window.table.column(6).search('Ditolak|Dibatalkan|Kadaluarsa', true, false).draw();
    } else {
        window.table.search('').columns().search('').draw();
        window.table.column(6).search(filter, true, false).draw();
    }
}
</script>
@endpush
@endsection
