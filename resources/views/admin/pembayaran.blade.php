@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('content')
<div class="page-header">
    <h4>Data Pembayaran</h4>
    <p>Kelola data pembayaran pasien. Verifikasi pembayaran dan catat metode pembayaran.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800">Daftar Pembayaran</h5>
        <a href="{{ route('admin.pembayaran.create') }}" class="btn-sm btn-primary">+ Tambah Pembayaran</a>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Biaya</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Referensi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal Bayar</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->rekamMedis->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->metode_bayar ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm text-xs">{{ $p->nomor_referensi ? $p->bank.' - '.$p->nomor_referensi : '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm"><span class="badge-status badge-{{ $p->status_bayar }}">{{ $p->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}</span></td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm flex gap-1">
                        @if($p->status_bayar == 'belum_bayar')
                        <button onclick="openBayar({{ $p->id }}, {{ $p->total_biaya }})" class="btn-sm btn-success">Bayar</button>
                        @endif
                        <a href="{{ route('admin.pembayaran.edit', $p->id) }}" class="btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.pembayaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus pembayaran ini?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Verifikasi Pembayaran (Admin) --}}
<div id="modalBayar" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 text-lg">Verifikasi Pembayaran</h5>
            <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg text-2xl">&times;</button>
        </div>
        <form id="formBayar" method="POST">
            @csrf @method('PUT')
            <div class="mb-4 p-4 bg-gray-50 rounded-xl">
                <p class="text-sm text-gray-500">Total Tagihan</p>
                <p id="totalTagihan" class="text-2xl font-bold text-gray-800">Rp 0</p>
            </div>
            <div class="mb-4">
                <label class="form-label">Jumlah Bayar <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-input-custom w-full" required min="0">
            </div>
            <div class="mb-4">
                <label class="form-label">Metode Bayar <span class="text-red-500">*</span></label>
                <select name="metode_bayar" class="form-select-custom w-full" required>
                    <option value="">-- Pilih --</option>
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label">Bank</label>
                <input type="text" name="bank" class="form-input-custom w-full" placeholder="Nama bank (jika transfer)">
            </div>
            <div class="mb-4">
                <label class="form-label">Nomor Referensi</label>
                <input type="text" name="nomor_referensi" class="form-input-custom w-full" placeholder="Nomor referensi pembayaran">
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Verifikasi &amp; Konfirmasi</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openBayar(id, total) {
    document.getElementById('formBayar').action = "{{ url('admin/pembayaran') }}/" + id + "/bayar";
    document.getElementById('totalTagihan').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('jumlahBayar').value = total;
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('modalBayar').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalBayar').classList.add('hidden');
    document.getElementById('modalBayar').classList.remove('flex');
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalBayar').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
@endsection
