@extends('layouts.dokter')

@section('title', 'Semua Booking')

@section('content')
<div class="page-header">
    <h4>Semua Booking</h4>
    <p>Riwayat semua booking pasien.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    @if($data->isEmpty())
    <div class="text-center py-8 text-gray-400">
        <p>Belum ada booking.</p>
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
                            @if(in_array($b->status, ['disetujui', 'check_in']))
                            <form action="{{ route('dokter.booking.mulai-periksa', $b->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <button class="btn-sm btn-primary">Mulai Periksa</button>
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

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable();
});
</script>
@endpush
@endsection
