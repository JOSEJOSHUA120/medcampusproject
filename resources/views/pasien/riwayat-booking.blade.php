@extends('layouts.pasien')

@section('title', 'Riwayat Booking')

@section('content')
<div class="page-header">
    <h4>Riwayat Booking</h4>
    <p>Daftar booking janji temu Anda dengan dokter.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    @if($data->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <p>Belum ada booking.</p>
        <a href="{{ route('pasien.booking') }}" class="btn-primary mt-4 inline-block">Booking Sekarang</a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
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
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-medium">dr. {{ $b->dokter->name }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm max-w-[150px] truncate">{{ $b->keluhan_awal ?? '-' }}</td>
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
                        @if($b->status == 'menunggu')
                        <form action="{{ route('pasien.booking.batal', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan booking ini?')">
                            @csrf @method('PUT')
                            <button class="btn-sm btn-danger">Batalkan</button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>
@endpush
@endsection
