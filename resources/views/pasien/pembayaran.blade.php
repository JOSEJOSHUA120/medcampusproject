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
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Metode</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Referensi</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($data as $i => $p)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm">{{ $p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar)->format('Y-m-d') : '-' }}</td>
                    <td class="px-4 py-3 text-sm">Pemeriksaan dr. {{ $p->rekamMedis->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-800">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $p->metode_bayar ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-xs">{{ $p->nomor_referensi ? $p->bank.' - '.$p->nomor_referensi : '-' }}</td>
                    <td class="px-4 py-3 text-sm"><span class="badge-status badge-{{ $p->status_bayar }}">{{ $p->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}</span></td>
                    <td class="px-4 py-3 text-sm">
                        @if($p->status_bayar == 'belum_bayar')
                        <button onclick="openBayar({{ $p->id }}, '{{ number_format($p->total_biaya, 0, ',', '.') }}')" class="btn-primary btn-sm">Bayar Sekarang</button>
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

{{-- Modal Pembayaran: Form untuk memilih metode bayar dan melakukan pembayaran --}}
<div id="modalBayar" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 text-lg">Bayar Tagihan</h5>
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
                <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-input-custom w-full" required min="0" placeholder="Masukkan nominal pembayaran">
            </div>

            {{-- Pilihan Metode Pembayaran: Tunai, QRIS, Transfer Bank --}}
            <div class="mb-4">
                <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2" id="metodeContainer">
                    <label class="metode-option border rounded-xl p-3 text-center cursor-pointer hover:border-sky-400 transition selected-metode" data-value="tunai">
                        <input type="radio" name="metode_bayar" value="tunai" class="hidden" checked>
                        <div class="text-2xl mb-1">&#x1F4B5;</div>
                        <div class="text-xs font-semibold">Tunai</div>
                    </label>
                    <label class="metode-option border rounded-xl p-3 text-center cursor-pointer hover:border-sky-400 transition" data-value="qris">
                        <input type="radio" name="metode_bayar" value="qris" class="hidden">
                        <div class="text-2xl mb-1">&#x1F4F1;</div>
                        <div class="text-xs font-semibold">QRIS</div>
                    </label>
                    <label class="metode-option border rounded-xl p-3 text-center cursor-pointer hover:border-sky-400 transition" data-value="transfer">
                        <input type="radio" name="metode_bayar" value="transfer" class="hidden">
                        <div class="text-2xl mb-1">&#x1F3E6;</div>
                        <div class="text-xs font-semibold">Transfer</div>
                    </label>
                </div>
            </div>

            {{-- Panel QRIS --}}
            <div id="panelQris" class="mb-4 hidden">
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center">
                    <div class="w-40 h-40 mx-auto mb-3 bg-white border-2 border-gray-200 rounded-xl flex items-center justify-center">
                        <div class="text-center p-2">
                            <svg viewBox="0 0 100 100" class="w-full h-full">
                                <rect x="5" y="5" width="20" height="20" fill="black"/>
                                <rect x="30" y="5" width="10" height="10" fill="black"/>
                                <rect x="50" y="5" width="10" height="10" fill="black"/>
                                <rect x="70" y="5" width="20" height="20" fill="black"/>
                                <rect x="5" y="30" width="10" height="10" fill="black"/>
                                <rect x="50" y="30" width="10" height="10" fill="black"/>
                                <rect x="80" y="30" width="10" height="10" fill="black"/>
                                <rect x="5" y="50" width="20" height="10" fill="black"/>
                                <rect x="30" y="50" width="20" height="10" fill="black"/>
                                <rect x="70" y="50" width="20" height="10" fill="black"/>
                                <rect x="5" y="70" width="20" height="20" fill="black"/>
                                <rect x="30" y="70" width="10" height="10" fill="black"/>
                                <rect x="50" y="70" width="10" height="10" fill="black"/>
                                <rect x="70" y="70" width="20" height="20" fill="black"/>
                                <rect x="30" y="30" width="15" height="15" fill="white" stroke="black" stroke-width="2"/>
                                <circle cx="37" cy="37" r="2" fill="black"/>
                            </svg>
                            <p class="text-xs font-bold text-gray-700 mt-1">QRIS</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">Scan QRIS di atas menggunakan aplikasi pembayaran (GoPay, OVO, Dana, dll)</p>
                </div>
            </div>

            {{-- Panel Transfer Bank --}}
            <div id="panelTransfer" class="mb-4 hidden">
                <div class="mb-3">
                    <label class="form-label">Pilih Bank <span class="text-red-500">*</span></label>
                    <select name="bank" class="form-select-custom w-full">
                        <option value="">-- Pilih Bank --</option>
                        <option value="BCA">BCA &mdash; 1234567890 a.n. MEDCAMPUS</option>
                        <option value="Mandiri">Mandiri &mdash; 9876543210 a.n. MEDCAMPUS</option>
                        <option value="BNI">BNI &mdash; 5678901234 a.n. MEDCAMPUS</option>
                        <option value="BRI">BRI &mdash; 4321098765 a.n. MEDCAMPUS</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Nomor Referensi Transfer</label>
                    <input type="text" name="nomor_referensi" class="form-input-custom w-full" placeholder="Masukkan nomor referensi transfer">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Konfirmasi Pembayaran</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
// openBayar: Membuka modal pembayaran dengan data tagihan
function openBayar(id, total) {
    document.getElementById('formBayar').action = "{{ url('pasien/pembayaran') }}/" + id + "/bayar";
    document.getElementById('totalTagihan').textContent = 'Rp ' + total;
    document.getElementById('jumlahBayar').value = total.replace(/\./g, '');
    document.getElementById('modalBayar').classList.remove('hidden');
    document.getElementById('modalBayar').classList.add('flex');
    toggleMetode('tunai');
}

// closeModal: Menutup modal
function closeModal() {
    document.getElementById('modalBayar').classList.add('hidden');
    document.getElementById('modalBayar').classList.remove('flex');
}

// toggleMetode: Menampilkan panel sesuai metode bayar yang dipilih
function toggleMetode(val) {
    document.getElementById('panelQris').classList.add('hidden');
    document.getElementById('panelTransfer').classList.add('hidden');
    if (val === 'qris') document.getElementById('panelQris').classList.remove('hidden');
    if (val === 'transfer') document.getElementById('panelTransfer').classList.remove('hidden');
    // Update visual selected
    document.querySelectorAll('.metode-option').forEach(function(el) {
        el.classList.remove('selected-metode', 'border-sky-500', 'bg-sky-50');
        if (el.dataset.value === val) {
            el.classList.add('selected-metode', 'border-sky-500', 'bg-sky-50');
        }
    });
}

// Event listener untuk pilihan metode
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.metode-option').forEach(function(el) {
        el.addEventListener('click', function() {
            var val = this.dataset.value;
            this.querySelector('input[type="radio"]').checked = true;
            toggleMetode(val);
        });
    });
    // Tutup modal saat klik di luar
    document.getElementById('modalBayar').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush
@endsection
