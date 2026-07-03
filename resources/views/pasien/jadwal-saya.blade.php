@extends('layouts.pasien')

@section('title', 'Jadwal Saya')

@section('content')
<div class="page-header">
    <h4>Jadwal Saya</h4>
    <p>Jadwal antrian dan kunjungan Anda.</p>
</div>

<div class="card-dashboard p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor Antrian</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor Ruangan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $d)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($d->tanggal_antrian)->format('Y-m-d') }}</td>
                    <td class="px-4 py-3 text-sm">dr. {{ $d->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($d->jam_antrian)->format('H:i') }}</td>
                    <td class="px-4 py-3 text-sm font-semibold">{{ $d->nomor_antrian }}</td>
                    <td class="px-4 py-3 text-sm">{{ $d->room->room_number ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm"><span class="badge-status badge-{{ $d->status }}">{{ ucfirst($d->status) }}</span></td>
                    <td class="px-4 py-3 text-sm">
                        @if($d->status == 'menunggu')
                        <div class="flex items-center gap-2">
                            <button onclick="openModal({{ $d->id }})" class="btn-primary btn-sm">Konfirmasi Kehadiran</button>
                            <form action="{{ route('pasien.antrian.batal', $d->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan antrian?')">
                                @csrf @method('PUT')
                                <button class="btn-danger btn-sm">Batalkan</button>
                            </form>
                        </div>
                        @elseif($d->status == 'dipanggil')
                        <span class="text-amber-600 text-xs font-medium">Anda telah dipanggil</span>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada jadwal.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="konfirmasiModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
        <h5 class="font-bold text-gray-800 mb-2">Konfirmasi Kehadiran</h5>
        <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin akan hadir pada jadwal ini?</p>
        <form id="konfirmasiForm" method="POST">
            @csrf
            @method('PUT')
            <div class="flex items-center gap-2 justify-end">
                <button type="button" onclick="closeModal()" class="btn-secondary btn-sm">Tutup</button>
                <button type="submit" class="btn-primary btn-sm">Ya, Konfirmasi</button>
            </div>
        </form>
    </div>
</div>

@if(auth()->user()->unreadNotifications->count())
<div class="card-dashboard p-4 mt-6">
    <h5 class="font-bold text-gray-800 mb-3">Notifikasi</h5>
    <div class="space-y-2">
        @foreach(auth()->user()->unreadNotifications as $notification)
        <div class="notification-item bg-sky-50 border border-sky-100 rounded-lg px-4 py-3 text-sm text-sky-800">
            {{ $notification->data['message'] }}
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function openModal(id) {
    document.getElementById('konfirmasiForm').action = '{{ route("pasien.antrian.konfirmasi", "") }}/' + id;
    document.getElementById('konfirmasiModal').classList.remove('hidden');
    document.getElementById('konfirmasiModal').classList.add('flex');
}

function closeModal() {
    document.getElementById('konfirmasiModal').classList.add('hidden');
    document.getElementById('konfirmasiModal').classList.remove('flex');
}
</script>
@endpush
