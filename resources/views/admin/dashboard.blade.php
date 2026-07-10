@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <h4>Dashboard Admin</h4>
    <p>Selamat datang, {{ auth()->user()->name }}! Ringkasan aktivitas klinik hari ini.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <a href="{{ route('admin.booking') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-blue-200 transition block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-3xl font-bold tracking-tight text-gray-800">{{ $bookingHariIni }}</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Booking Hari Ini</p>
        <p class="text-xs text-gray-400 mt-1">Jadwal pasien hari ini</p>
    </a>


    <a href="{{ route('admin.pasien') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-green-200 transition block">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <span class="text-3xl font-bold tracking-tight text-gray-800">{{ $pasienBulanIni }}</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Pasien Bulan Ini</p>
        <p class="text-xs text-gray-400 mt-1">Pasien berobat bulan {{ now()->format('F Y') }}</p>
    </a>

    <a href="{{ route('admin.pembayaran') }}" class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:border-emerald-200 transition block">
        <div class="mb-3">
            <span class="text-3xl font-bold tracking-tight text-gray-800">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</span>
        </div>
        <p class="text-gray-500 text-sm font-medium">Pendapatan Bulan Ini</p>
        <p class="text-xs text-gray-400 mt-1">Pembayaran lunas {{ now()->format('F Y') }}</p>
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800">Booking Hari Ini</h5>
            <a href="{{ route('admin.booking') }}" class="btn-sm btn-primary">Kelola</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Dokter</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Jam</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataBooking as $b)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $b->pasien->name ?? '-' }}</td>
                        <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $b->dokter->name ?? '-' }}</td>
                        <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($b->jam_booking)->format('H:i') }}</td>
                        <td class="px-4 py-3 border-b border-gray-100 text-sm">
                            <span class="badge-status badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 border-b border-gray-100 text-sm text-center text-gray-400">Tidak ada booking hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800">Tagihan Menunggu</h5>
            <a href="{{ route('admin.pembayaran') }}" class="btn-sm btn-primary">Kelola</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tagihan</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayaranTerbaru as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->rekamMedis->pasien->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 border-b border-gray-100 text-sm font-semibold">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 border-b border-gray-100 text-sm">
                            <span class="badge-status badge-belum_bayar">Belum Bayar</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-3 border-b border-gray-100 text-sm text-center text-gray-400">Tidak ada tagihan tertunda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection