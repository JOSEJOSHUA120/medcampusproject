@extends('layouts.dokter')

@section('title', 'Daftar Booking')

@section('content')
<div class="page-header">
    <h4>Daftar Booking Pasien</h4>
    <p>Informasi lengkap pasien yang telah melakukan booking.</p>
</div>

@if($data->isEmpty())
<div class="text-center py-12 text-gray-400">
    <p>Belum ada booking aktif.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @foreach($data as $b)
    @php
        $u = $b->pasien;
        $p = $u?->pasien;
        $foto = $p->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($u->email ?? '');
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
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-5 py-4 flex items-center gap-4">
            <img src="{{ $foto }}" alt="foto" class="w-14 h-14 rounded-full object-cover border-2 border-white/60 shadow">
            <div class="text-white">
                <h5 class="font-bold text-base">{{ $u->name ?? '-' }}</h5>
                <span class="badge-status {{ $badgeClass }} text-xs">{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span>
            </div>
        </div>
        <div class="p-5 space-y-3">
            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Email</span>
                    <p class="font-medium text-gray-800 truncate">{{ $u->email ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">No. Telepon</span>
                    <p class="font-medium text-gray-800">{{ $p->no_telp ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Tempat Lahir</span>
                    <p class="font-medium text-gray-800">{{ $p->tempat_lahir ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Tanggal Lahir</span>
                    <p class="font-medium text-gray-800">{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Jenis Kelamin</span>
                    <p class="font-medium text-gray-800">
                        @if($p && $p->jenis_kelamin == 'L') Laki-Laki
                        @elseif($p && $p->jenis_kelamin == 'P') Perempuan
                        @else -
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">No. Booking</span>
                    <p class="font-medium text-gray-800">#{{ $b->id }}</p>
                </div>
            </div>
            <hr class="border-gray-100">
            <div class="text-sm">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Alamat</span>
                <p class="font-medium text-gray-800">{{ $p->alamat ?? '-' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Tanggal Booking</span>
                    <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Jam</span>
                    <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</p>
                </div>
            </div>
            <div class="text-sm">
                <span class="text-xs text-gray-400 uppercase tracking-wider">Keluhan</span>
                <p class="font-medium text-gray-800 bg-gray-50 rounded-lg p-2 mt-1">{{ $b->keluhan_awal ?? '-' }}</p>
            </div>
            <hr class="border-gray-100">
            <div class="flex gap-2 flex-wrap pt-1">
                @if($b->status == 'menunggu')
                <form action="{{ route('dokter.booking.approve', $b->id) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-success w-full">Setujui</button>
                </form>
                <button onclick="openReject({{ $b->id }})" class="btn-sm btn-danger flex-1">Tolak</button>
                @elseif($b->status == 'disetujui')
                <form action="{{ route('dokter.booking.mulai-periksa', $b->id) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-primary w-full">Mulai Periksa</button>
                </form>
                <form action="{{ route('dokter.booking.check-in', $b->id) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-info w-full">Check In</button>
                </form>
                <button onclick="openReject({{ $b->id }})" class="btn-sm btn-danger flex-1">Tolak</button>
                @elseif($b->status == 'check_in')
                <form action="{{ route('dokter.booking.mulai-periksa', $b->id) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-primary w-full">Mulai Periksa</button>
                </form>
                <form action="{{ route('dokter.booking.selesai', $b->id) }}" method="POST" class="flex-1">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-success w-full">Selesai</button>
                </form>
                <form action="{{ route('dokter.booking.tidak-hadir', $b->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Tandai tidak hadir?')">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-danger w-full">Tidak Hadir</button>
                </form>
                @else
                <span class="text-gray-400 text-xs w-full text-center py-2">-</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

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
});
</script>
@endpush
@endsection
