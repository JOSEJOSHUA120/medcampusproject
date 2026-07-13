@extends('layouts.dokter')

@section('title', 'Daftar Booking')

@section('content')
<div class="page-header">
    <h4>Daftar Booking</h4>
    <p>Pasien booking hari ini.</p>
</div>

@if($bookings->isEmpty())
<div class="text-center py-12 text-gray-400 dark:text-gray-500">
    <p>Belum ada pasien aktif.</p>
</div>
@else
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($bookings as $b)
    @php
        $u = $b->pasien;
        $p = $u?->pasien;
        $foto = $p?->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($u?->email ?? '');
        $badgeClass = match($b->status) {
            'menunggu' => 'badge-menunggu',
            'disetujui' => 'badge-dipanggil',
            'dipanggil' => 'badge-dipanggil',
            'sedang_dilayani' => 'bg-yellow-100 text-yellow-800',
            'ditolak' => 'bg-red-100 text-red-800',
            'check_in' => 'bg-indigo-100 text-indigo-800',
            'tidak_hadir' => 'bg-gray-100 text-gray-800',
            'selesai' => 'badge-selesai',
            'dibatalkan' => 'bg-red-100 text-red-800',
            'kadaluarsa' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    @endphp
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-all">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 px-4 py-3 flex items-center gap-3">
            <img src="{{ $foto }}" alt="foto" class="w-10 h-10 rounded-full object-cover border-2 border-white/60 shadow">
            <div class="text-white min-w-0 flex-1">
                <h6 class="font-bold text-sm truncate">{{ $u?->name ?? '-' }}</h6>
                <div class="flex gap-1 mt-0.5 flex-wrap">
                    <span class="badge-status bg-blue-100 text-blue-800 text-[10px] leading-none">Booking</span>
                    <span class="badge-status {{ $badgeClass }} text-[10px] leading-none">{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span>
                </div>
            </div>
        </div>
        <div class="p-4 space-y-2">
            <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-xs">
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px]">Email</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200 truncate text-xs">{{ $u?->email ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px]">No. Telp</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">{{ $p?->no_telp ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px]">Tgl Lahir</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">{{ $p?->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px]">JK</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">
                        @elseif($p && $p->jenis_kelamin == 'P') Perempuan
                        @else -
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px]">No. Booking</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">#{{ $b->id }}</p>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider text-[10px]">Jam</span>
                    <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">{{ $b->jam_booking ? \Carbon\Carbon::parse($b->jam_booking)->format('H:i') : '-' }}</p>
                </div>
            </div>
            <div class="text-xs">
                <span class="text-gray-400 uppercase tracking-wider text-[10px]">Tanggal</span>
                <p class="font-medium text-gray-800 dark:text-gray-200 text-xs">{{ $b->tanggal_booking ? \Carbon\Carbon::parse($b->tanggal_booking)->format('d/m/Y') : '-' }}</p>
            </div>
            <div class="text-xs">
                <span class="text-gray-400 uppercase tracking-wider text-[10px]">Keluhan</span>
                <p class="text-gray-700 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 rounded-md p-1.5 mt-0.5 text-xs">{{ $b->keluhan_awal ?? '-' }}</p>
            </div>
            <hr class="border-gray-50">
            <div class="flex gap-1.5 flex-wrap pt-0.5">
                @if($b->status == 'menunggu')
                <form action="{{ route('dokter.booking.approve', $b->id) }}" method="POST">
                    @csrf @method('PUT')
                    <button class="inline-flex items-center gap-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg shadow-sm transition">Setujui</button>
                </form>
                @elseif($b->status == 'disetujui')
                <form action="{{ route('dokter.booking.panggil', $b->id) }}" method="POST" class="flex-1 min-w-[70px]">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-warning w-full text-xs py-1">Panggil</button>
                </form>
                @elseif(in_array($b->status, ['dipanggil', 'check_in']))
                <form action="{{ route('dokter.booking.mulai-periksa', $b->id) }}" method="POST" class="flex-1 min-w-[70px]">
                    @csrf @method('PUT')
                    <button class="btn-sm btn-primary w-full text-xs py-1">Mulai Periksa</button>
                </form>
                @else
                <span class="text-gray-400 text-[10px] w-full text-center py-1">{{ ucfirst(str_replace('_', ' ', $b->status)) }}</span>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection