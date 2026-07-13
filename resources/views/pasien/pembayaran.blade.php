@extends('layouts.pasien')

@section('title', 'Pembayaran')

@section('content')
<div class="page-header">
    <h4>Riwayat Pembayaran</h4>
    <p>Riwayat pembayaran pemeriksaan Anda. Lakukan pembayaran untuk tagihan yang belum lunas.</p>
</div>

<div class="card-dashboard p-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($data as $i => $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm dark:text-gray-300">{{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-3 text-sm dark:text-gray-300">Pemeriksaan {{ $p->rekamMedis->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-white">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm dark:text-gray-300">{{ $p->metode_bayar ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm"><span class="badge-status badge-{{ $p->status_bayar }}">{{ $p->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}</span></td>
                    <td class="px-4 py-3 text-sm">
                        @if($p->status_bayar == 'belum_bayar' && $p->total_biaya > 0)
                        <button onclick="openBayar({{ $p->id }}, '{{ number_format($p->total_biaya, 0, ',', '.') }}')" class="btn-primary btn-sm">Bayar Sekarang</button>
                        @elseif($p->status_bayar == 'belum_bayar' && $p->total_biaya <= 0)
                        <span class="text-xs text-gray-400 italic">Menunggu admin</span>
                        @else
                        <span class="text-xs text-green-600">&check; Lunas</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="modalBayar" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 dark:text-white text-lg">Bayar Tagihan</h5>
            <button onclick="closeModal()" class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg text-2xl dark:text-gray-300">&times;</button>
        </div>
        <form id="formBayar" method="POST">
            @csrf @method('PUT')
            <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-sky-50 dark:from-blue-900/30 dark:to-sky-900/30 rounded-xl border border-blue-100 dark:border-blue-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Tagihan</p>
                <p id="totalTagihan" class="text-2xl font-bold text-gray-800 dark:text-white">Rp 0</p>
            </div>

            <div class="mb-4">
                <label class="form-label">Jumlah Bayar <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-input-custom w-full" required min="0" placeholder="Masukkan nominal pembayaran">
            </div>

            <div class="mb-4">
                <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-2" id="metodeContainer">
                    <label class="metode-option border rounded-xl p-3 text-center cursor-pointer hover:border-blue-400 transition selected-metode" data-value="tunai">
                        <input type="radio" name="metode_bayar" value="tunai" class="hidden" checked>
                        <div class="text-2xl mb-1">&#x1F4B5;</div>
                        <div class="text-xs font-semibold">Tunai</div>
                    </label>
                    <label class="metode-option border rounded-xl p-3 text-center cursor-pointer hover:border-blue-400 transition" data-value="qris">
                        <input type="radio" name="metode_bayar" value="qris" class="hidden">
                        <div class="text-2xl mb-1">&#x1F4F1;</div>
                        <div class="text-xs font-semibold">QRIS</div>
                    </label>
                </div>
            </div>

            <div id="panelQris" class="mb-4 hidden">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 text-center">
                        <span class="text-white font-bold text-lg tracking-wider">QRIS</span>
                    </div>
                    <div class="p-4 text-center">
                        <img src="{{ asset('qris medcampus.png') }}" alt="QRIS MEDCAMPUS" class="w-40 h-40 mx-auto mb-3 object-contain">
                        <p id="qrisAmount" class="text-xl font-bold text-gray-800 mb-1">Rp 0</p>
                        <p class="text-xs text-gray-500 mb-3">MEDCAMPUS KLINIK DIGITAL</p>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-500">
                            <p>Scan QRIS di atas menggunakan aplikasi</p>
                            <p>pembayaran (GoPay, OVO, Dana, dll)</p>
                        </div>
                    </div>
                </div>
            </div>



            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Konfirmasi Pembayaran</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openBayar(id, total) {
    document.getElementById('formBayar').action = "{{ url('pasien/pembayaran') }}/" + id + "/bayar";
    document.getElementById('totalTagihan').textContent = 'Rp ' + total;
    document.getElementById('qrisAmount').textContent = 'Rp ' + total;
    document.getElementById('jumlahBayar').value = total.replace(/\./g, '');
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('modalBayar').classList.add('flex');
    toggleMetode('tunai');
}

function closeModal() {
    document.getElementById('modalBayar').classList.add('hidden');
    document.getElementById('modalBayar').classList.remove('flex');
}

function toggleMetode(val) {
    document.getElementById('panelQris').classList.add('hidden');
    if (val === 'qris') document.getElementById('panelQris').classList.remove('hidden');
    document.querySelectorAll('.metode-option').forEach(function(el) {
        el.classList.remove('selected-metode', 'border-blue-500', 'bg-blue-50');
        if (el.dataset.value === val) {
            el.classList.add('selected-metode', 'border-blue-500', 'bg-blue-50');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.metode-option').forEach(function(el) {
        el.addEventListener('click', function() {
            var val = this.dataset.value;
            this.querySelector('input[type="radio"]').checked = true;
            toggleMetode(val);
        });
    });
    document.getElementById('modalBayar').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
@endsection
