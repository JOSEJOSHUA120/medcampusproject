@extends('layouts.admin')

@section('title', 'Generate Pembayaran')

@section('content')
<div class="page-header">
    <h4>Generate Pembayaran</h4>
    <p>Generate metode pembayaran QRIS atau Transfer Bank untuk pasien.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card-dashboard p-6">
        <h5 class="font-bold text-gray-800 mb-4">Detail Tagihan</h5>
        <table class="w-full text-sm">
            <tr>
                <td class="py-2 text-gray-500">Pasien</td>
                <td class="py-2 font-semibold">: {{ $pembayaran->rekamMedis->pasien->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="py-2 text-gray-500">Dokter</td>
                <td class="py-2 font-semibold">: dr. {{ $pembayaran->rekamMedis->dokter->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="py-2 text-gray-500">Tanggal</td>
                <td class="py-2 font-semibold">: {{ $pembayaran->rekamMedis->created_at->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td class="py-2 text-gray-500">Diagnosa</td>
                <td class="py-2 font-semibold">: {{ $pembayaran->rekamMedis->diagnosa ?? '-' }}</td>
            </tr>
        </table>

        @if($pembayaran->rekamMedis->resepObat->count())
        <h6 class="font-bold text-gray-700 mt-4 mb-2">Rincian Obat:</h6>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Obat</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-gray-600">Jumlah</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Harga</th>
                    <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembayaran->rekamMedis->resepObat as $ro)
                <tr>
                    <td class="px-3 py-1.5">{{ $ro->obat->nama_obat }}</td>
                    <td class="px-3 py-1.5 text-center">{{ $ro->jumlah }} {{ $ro->obat->satuan }}</td>
                    <td class="px-3 py-1.5 text-right">Rp {{ number_format($ro->harga_satuan, 0, ',', '.') }}</td>
                    <td class="px-3 py-1.5 text-right">Rp {{ number_format($ro->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold">
                    <td colspan="3" class="px-3 py-2 text-right">Total:</td>
                    <td class="px-3 py-2 text-right text-primary-600">Rp {{ number_format($pembayaran->total_biaya, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @endif

        <div class="mt-4 p-4 bg-gray-50 rounded-xl text-center">
            <p class="text-2xl font-bold text-primary-600">Rp {{ number_format($pembayaran->total_biaya, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500">Total Tagihan</p>
        </div>
    </div>

    <div class="card-dashboard p-6">
        <h5 class="font-bold text-gray-800 mb-4">Generate Metode Pembayaran</h5>
        <form action="{{ route('admin.pembayaran.generate.store', $pembayaran->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="form-label">Metode Pembayaran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="border rounded-xl p-4 text-center cursor-pointer hover:border-sky-400 transition metode-option" data-value="qris" onclick="togglePanel('qris')">
                        <input type="radio" name="metode_bayar" value="qris" class="hidden" checked>
                        <div class="text-3xl mb-1">📱</div>
                        <div class="text-xs font-semibold">QRIS</div>
                    </label>
                    <label class="border rounded-xl p-4 text-center cursor-pointer hover:border-sky-400 transition metode-option" data-value="transfer" onclick="togglePanel('transfer')">
                        <input type="radio" name="metode_bayar" value="transfer" class="hidden">
                        <div class="text-3xl mb-1">🏦</div>
                        <div class="text-xs font-semibold">Transfer Bank</div>
                    </label>
                </div>
            </div>

            <div id="panelQris" class="mb-4">
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center mb-4">
                    <div class="w-40 h-40 mx-auto bg-white border-2 border-gray-200 rounded-xl flex items-center justify-center mb-3">
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
                    <p class="text-sm text-gray-500">QRIS akan ditampilkan ke pasien untuk scan pembayaran.</p>
                </div>
                <div class="mb-4">
                    <label class="form-label">Nomor Referensi QRIS <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_referensi" class="form-input-custom w-full" value="QRIS-{{ now()->format('Ymd') }}-{{ strtoupper(substr(uniqid(), -6)) }}" placeholder="Nomor referensi">
                </div>
            </div>

            <div id="panelTransfer" class="mb-4 hidden">
                <div class="mb-3">
                    <label class="form-label">Pilih Bank <span class="text-red-500">*</span></label>
                    <select name="bank" class="form-select-custom w-full">
                        <option value="">-- Pilih Bank --</option>
                        <option value="BCA">BCA — 1234567890 a.n. MEDCAMPUS</option>
                        <option value="Mandiri">Mandiri — 9876543210 a.n. MEDCAMPUS</option>
                        <option value="BNI">BNI — 5678901234 a.n. MEDCAMPUS</option>
                        <option value="BRI">BRI — 4321098765 a.n. MEDCAMPUS</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Nomor Referensi Transfer <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_referensi" class="form-input-custom w-full" placeholder="Masukkan nomor referensi">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Generate & Simpan Pembayaran</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePanel(val) {
    document.getElementById('panelQris').classList.add('hidden');
    document.getElementById('panelTransfer').classList.add('hidden');
    if (val === 'qris') document.getElementById('panelQris').classList.remove('hidden');
    if (val === 'transfer') document.getElementById('panelTransfer').classList.remove('hidden');
    document.querySelectorAll('.metode-option').forEach(function(el) {
        el.classList.remove('border-sky-500', 'bg-sky-50');
        if (el.dataset.value === val) {
            el.classList.add('border-sky-500', 'bg-sky-50');
        }
    });
}
</script>
@endpush
@endsection
