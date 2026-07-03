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

<div id="modalBayar" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-screen overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 text-lg">Bayar Tagihan</h5>
            <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg text-2xl">&times;</button>
        </div>
        <form id="formBayar" method="POST">
            @csrf @method('PUT')
            <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-sky-50 rounded-xl border border-blue-100">
                <p class="text-sm text-gray-500">Total Tagihan</p>
                <p id="totalTagihan" class="text-2xl font-bold text-gray-800">Rp 0</p>
            </div>

            <div class="mb-4">
                <label class="form-label">Jumlah Bayar <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah_bayar" id="jumlahBayar" class="form-input-custom w-full" required min="0" placeholder="Masukkan nominal pembayaran">
            </div>

            <div class="mb-4">
                <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2" id="metodeContainer">
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
                    <label class="metode-option border rounded-xl p-3 text-center cursor-pointer hover:border-blue-400 transition" data-value="transfer">
                        <input type="radio" name="metode_bayar" value="transfer" class="hidden">
                        <div class="text-2xl mb-1">&#x1F3E6;</div>
                        <div class="text-xs font-semibold">Transfer</div>
                    </label>
                </div>
            </div>

            <div id="panelQris" class="mb-4 hidden">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 text-center">
                        <span class="text-white font-bold text-lg tracking-wider">QRIS</span>
                    </div>
                    <div class="p-6 text-center">
                        <div class="w-48 h-48 mx-auto mb-3 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <rect x="8" y="8" width="44" height="44" fill="white" stroke="black" stroke-width="3"/>
                                <rect x="12" y="12" width="8" height="8" fill="black"/>
                                <rect x="24" y="12" width="8" height="8" fill="black"/>
                                <rect x="36" y="12" width="8" height="8" fill="black"/>
                                <rect x="12" y="24" width="8" height="8" fill="black"/>
                                <rect x="36" y="24" width="8" height="8" fill="black"/>
                                <rect x="12" y="36" width="8" height="8" fill="black"/>
                                <rect x="24" y="36" width="8" height="8" fill="black"/>
                                <rect x="36" y="36" width="8" height="8" fill="black"/>

                                <rect x="148" y="8" width="44" height="44" fill="white" stroke="black" stroke-width="3"/>
                                <rect x="152" y="12" width="8" height="8" fill="black"/>
                                <rect x="164" y="12" width="8" height="8" fill="black"/>
                                <rect x="176" y="12" width="8" height="8" fill="black"/>
                                <rect x="152" y="24" width="8" height="8" fill="black"/>
                                <rect x="176" y="24" width="8" height="8" fill="black"/>
                                <rect x="152" y="36" width="8" height="8" fill="black"/>
                                <rect x="164" y="36" width="8" height="8" fill="black"/>
                                <rect x="176" y="36" width="8" height="8" fill="black"/>

                                <rect x="8" y="148" width="44" height="44" fill="white" stroke="black" stroke-width="3"/>
                                <rect x="12" y="152" width="8" height="8" fill="black"/>
                                <rect x="24" y="152" width="8" height="8" fill="black"/>
                                <rect x="36" y="152" width="8" height="8" fill="black"/>
                                <rect x="12" y="164" width="8" height="8" fill="black"/>
                                <rect x="36" y="164" width="8" height="8" fill="black"/>
                                <rect x="12" y="176" width="8" height="8" fill="black"/>
                                <rect x="24" y="176" width="8" height="8" fill="black"/>
                                <rect x="36" y="176" width="8" height="8" fill="black"/>

                                <rect x="64" y="12" width="8" height="8" fill="black"/>
                                <rect x="80" y="12" width="8" height="8" fill="black"/>
                                <rect x="96" y="12" width="8" height="8" fill="white"/>
                                <rect x="112" y="12" width="8" height="8" fill="black"/>
                                <rect x="128" y="12" width="8" height="8" fill="black"/>
                                <rect x="12" y="68" width="8" height="8" fill="black"/>
                                <rect x="28" y="68" width="8" height="8" fill="black"/>
                                <rect x="44" y="68" width="8" height="8" fill="white"/>
                                <rect x="60" y="68" width="8" height="8" fill="black"/>
                                <rect x="84" y="68" width="8" height="8" fill="black"/>
                                <rect x="108" y="68" width="8" height="8" fill="black"/>
                                <rect x="124" y="68" width="8" height="8" fill="white"/>
                                <rect x="148" y="68" width="8" height="8" fill="black"/>
                                <rect x="164" y="68" width="8" height="8" fill="black"/>
                                <rect x="180" y="68" width="8" height="8" fill="black"/>
                                <rect x="12" y="84" width="8" height="8" fill="white"/>
                                <rect x="36" y="84" width="8" height="8" fill="black"/>
                                <rect x="60" y="84" width="8" height="8" fill="black"/>
                                <rect x="84" y="84" width="8" height="8" fill="white"/>
                                <rect x="100" y="84" width="8" height="8" fill="black"/>
                                <rect x="132" y="84" width="8" height="8" fill="black"/>
                                <rect x="148" y="84" width="8" height="8" fill="white"/>
                                <rect x="172" y="84" width="8" height="8" fill="black"/>
                                <rect x="12" y="100" width="8" height="8" fill="black"/>
                                <rect x="36" y="100" width="8" height="8" fill="white"/>
                                <rect x="52" y="100" width="8" height="8" fill="black"/>
                                <rect x="76" y="100" width="8" height="8" fill="black"/>
                                <rect x="100" y="100" width="8" height="8" fill="black"/>
                                <rect x="116" y="100" width="8" height="8" fill="white"/>
                                <rect x="140" y="100" width="8" height="8" fill="white"/>
                                <rect x="164" y="100" width="8" height="8" fill="black"/>
                                <rect x="180" y="100" width="8" height="8" fill="black"/>
                                <rect x="12" y="116" width="8" height="8" fill="black"/>
                                <rect x="36" y="116" width="8" height="8" fill="white"/>
                                <rect x="60" y="116" width="8" height="8" fill="black"/>
                                <rect x="84" y="116" width="8" height="8" fill="black"/>
                                <rect x="100" y="116" width="8" height="8" fill="white"/>
                                <rect x="132" y="116" width="8" height="8" fill="black"/>
                                <rect x="148" y="116" width="8" height="8" fill="black"/>
                                <rect x="172" y="116" width="8" height="8" fill="black"/>
                                <rect x="12" y="132" width="8" height="8" fill="black"/>
                                <rect x="28" y="132" width="8" height="8" fill="black"/>
                                <rect x="44" y="132" width="8" height="8" fill="white"/>
                                <rect x="76" y="132" width="8" height="8" fill="white"/>
                                <rect x="108" y="132" width="8" height="8" fill="black"/>
                                <rect x="124" y="132" width="8" height="8" fill="black"/>
                                <rect x="148" y="132" width="8" height="8" fill="black"/>
                                <rect x="164" y="132" width="8" height="8" fill="white"/>
                                <rect x="180" y="132" width="8" height="8" fill="black"/>
                                <rect x="64" y="180" width="8" height="8" fill="white"/>
                                <rect x="80" y="180" width="8" height="8" fill="black"/>
                                <rect x="96" y="180" width="8" height="8" fill="black"/>
                                <rect x="112" y="180" width="8" height="8" fill="white"/>
                                <rect x="128" y="180" width="8" height="8" fill="black"/>

                                <rect x="84" y="84" width="32" height="32" fill="white" rx="4"/>
                                <text x="100" y="103" text-anchor="middle" font-size="9" font-weight="bold" fill="#1D4ED8">QR</text>
                                <text x="100" y="114" text-anchor="middle" font-size="7" font-weight="bold" fill="#1D4ED8">IS</text>
                            </svg>
                        </div>
                        <p id="qrisAmount" class="text-xl font-bold text-gray-800 mb-1">Rp 0</p>
                        <p class="text-xs text-gray-500 mb-3">MEDCAMPUS KLINIK DIGITAL</p>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-500">
                            <p>Scan QRIS di atas menggunakan aplikasi</p>
                            <p>pembayaran (GoPay, OVO, Dana, dll)</p>
                        </div>
                    </div>
                </div>
            </div>

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
    document.getElementById('panelTransfer').classList.add('hidden');
    if (val === 'qris') document.getElementById('panelQris').classList.remove('hidden');
    if (val === 'transfer') document.getElementById('panelTransfer').classList.remove('hidden');
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
