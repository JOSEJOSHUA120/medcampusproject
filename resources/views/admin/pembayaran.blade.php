@extends('layouts.admin')

@section('title', 'Pembayaran')

@section('content')
<div class="page-header">
    <h4>Data Pembayaran</h4>
    <p>Kelola data pembayaran pasien. Verifikasi pembayaran QRIS & Tunai.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between mb-4">
        <h5 class="font-bold text-gray-800 dark:text-white">Daftar Pembayaran</h5>
        <a href="{{ route('admin.pembayaran.create') }}" class="btn-sm btn-primary">+ Tambah Pembayaran</a>
    </div>
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Obat</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total Biaya</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tanggal Bayar</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $i+1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-200">{{ $p->rekamMedis->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300 text-xs max-w-[200px]">
                        @if($p->rekamMedis->resepObat->count())
                            @foreach($p->rekamMedis->resepObat as $ro)
                            <div>{{ $ro->obat->nama_obat ?? '-' }} x{{ $ro->jumlah }}</div>
                            @endforeach
                        @elseif($p->rekamMedis->resep_obat)
                            <div class="whitespace-pre-line">{{ $p->rekamMedis->resep_obat }}</div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $p->metode_bayar ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300">{{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm dark:text-gray-300"><span class="badge-status badge-{{ $p->status_bayar }}">{{ $p->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}</span></td>
                    <td class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 text-sm flex gap-1 flex-wrap">
                        @if($p->status_bayar == 'belum_bayar')
                        <button onclick="openBayar({{ $p->id }}, {{ $p->total_biaya }}, '{{ $p->rekamMedis->resep_obat ? addslashes(str_replace(["\r\n", "\r", "\n"], '\\n', $p->rekamMedis->resep_obat)) : '' }}')" class="btn-sm btn-success">Bayar</button>
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
                <div id="resepInfo" class="mt-2 text-xs text-gray-500 border-t border-gray-200 pt-2 hidden">
                    <p class="font-medium text-gray-700 mb-1">Resep Dokter:</p>
                    <p id="resepText" class="whitespace-pre-line"></p>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Jumlah Bayar <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-input-custom w-full" required min="0">
            </div>
            <div class="mb-4">
                <label class="form-label">Metode Bayar <span class="text-red-500">*</span></label>
                <select name="metode_bayar" class="form-select-custom w-full" required onchange="toggleMetodeAdmin(this.value)">
                    <option value="">-- Pilih --</option>
                    <option value="tunai">Tunai</option>
                    <option value="qris">QRIS</option>
                </select>
            </div>
            <div id="panelQrisAdmin" class="mb-4 hidden">
                <div class="bg-gray-50 rounded-xl p-4 text-center">
                    <img src="{{ asset('qris medcampus.png') }}" alt="QRIS MEDCAMPUS" class="w-40 h-40 mx-auto object-contain">
                    <p class="text-xs text-gray-500 mt-3">Scan QRIS untuk verifikasi pembayaran</p>
                </div>
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Verifikasi & Konfirmasi</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openBayar(id, total, resep) {
    document.getElementById('formBayar').action = "{{ url('admin/pembayaran') }}/" + id + "/bayar";
    document.getElementById('totalTagihan').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('jumlahBayar').value = total;
    if (resep) {
        document.getElementById('resepInfo').classList.remove('hidden');
        document.getElementById('resepText').textContent = resep;
    } else {
        document.getElementById('resepInfo').classList.add('hidden');
    }
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('modalBayar').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalBayar').classList.add('hidden');
    document.getElementById('modalBayar').classList.remove('flex');
}
function toggleMetodeAdmin(val) {
    document.getElementById('panelQrisAdmin').classList.add('hidden');
    if (val === 'qris') document.getElementById('panelQrisAdmin').classList.remove('hidden');
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalBayar').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
@endsection
