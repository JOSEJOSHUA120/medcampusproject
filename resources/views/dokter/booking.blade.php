@extends('layouts.dokter')

@section('title', 'Daftar Booking')

@section('content')
<div class="page-header">
    <h4>Daftar Booking Pasien</h4>
    <p>Semua janji temu pasien yang aktif (hari ini & mendatang).</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    @if($data->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <p>Belum ada booking aktif.</p>
    </div>
    @else
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Keluhan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-medium">{{ $b->pasien->name }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm max-w-[200px]">{{ $b->keluhan_awal ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        @php
                            $badgeClass = match($b->status) {
                                'menunggu' => 'badge-menunggu',
                                'disetujui' => 'badge-dipanggil',
                                'ditolak' => 'bg-red-100 text-red-800',
                                'check_in' => 'bg-indigo-100 text-indigo-800',
                                'tidak_hadir' => 'bg-gray-100 text-gray-800',
                                'selesai' => 'badge-selesai',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="badge-status {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        <div class="flex gap-1 flex-wrap">
                            @if($b->status == 'menunggu')
                            <form action="{{ route('dokter.booking.approve', $b->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="btn-sm btn-success">Setujui</button>
                            </form>
                            <button onclick="openReject({{ $b->id }})" class="btn-sm btn-danger">Tolak</button>
                            @elseif($b->status == 'disetujui')
                            <form action="{{ route('dokter.booking.check-in', $b->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="btn-sm btn-primary">Check In</button>
                            </form>
                            <button onclick="openReject({{ $b->id }})" class="btn-sm btn-danger">Tolak</button>
                            @elseif($b->status == 'check_in')
                            <form action="{{ route('dokter.booking.selesai', $b->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="btn-sm btn-success">Selesai</button>
                            </form>
                            <form action="{{ route('dokter.booking.tidak-hadir', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Tandai tidak hadir?')">
                                @csrf @method('PUT')
                                <button class="btn-sm btn-danger">Tidak Hadir</button>
                            </form>
                            @elseif($b->status == 'disetujui' || $b->status == 'menunggu')
                            <form action="{{ route('dokter.booking.tidak-hadir', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Tandai tidak hadir?')">
                                @csrf @method('PUT')
                                <button class="btn-sm btn-danger">Tidak Hadir</button>
                            </form>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<div id="modalReject" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 text-lg">Tolak Booking</h5>
            <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg text-2xl">&times;</button>
        </div>
        <form id="formReject" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="form-label">Alasan Penolakan</label>
                <textarea name="catatan" class="form-input-custom" rows="3" placeholder="Opsional"></textarea>
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Konfirmasi Tolak</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openReject(id) {
    document.getElementById('formReject').action = "{{ url('dokter/booking') }}/" + id + "/reject";
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
    $('#dataTable').DataTable();
});
</script>
@endpush
@endsection
