@extends('layouts.admin')

@section('title', 'Laporan Pembayaran')

@section('content')
<div class="page-header">
    <h4>Laporan Pembayaran</h4>
    <p>Download laporan pembayaran PDF berdasarkan periode.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.laporan') }}" method="GET" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="form-label">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" class="form-input-custom" value="{{ request('tanggal_awal', now()->startOfMonth()->toDateString()) }}" required>
            </div>
            <div>
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-input-custom" value="{{ request('tanggal_akhir', now()->endOfMonth()->toDateString()) }}" required>
            </div>
            <div>
                <button type="submit" class="btn-primary w-full">Filter</button>
            </div>
            <div>
                <a href="{{ route('admin.laporan.pdf', request()->only(['tanggal_awal', 'tanggal_akhir'])) }}" target="_blank" class="btn-warning w-full inline-block text-center">Download PDF</a>
            </div>
        </div>
    </form>

    @if(isset($data) && count($data) > 0)
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Bayar</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->rekamMedis->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->tanggal_bayar ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->metode_bayar ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm"><span class="badge-status badge-{{ $p->status_bayar }}">{{ $p->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}</span></td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm text-right">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td colspan="5" class="px-4 py-3 text-sm text-right">Total</td>
                    <td class="px-4 py-3 text-sm text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <p class="text-gray-400 text-center py-6">Belum ada data pembayaran untuk periode ini.</p>
    @endif
</div>
@endsection
