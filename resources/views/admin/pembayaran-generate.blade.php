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

        <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-sky-50 rounded-xl text-center border border-blue-100">
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
                    <label class="border rounded-xl p-4 text-center cursor-pointer hover:border-blue-400 transition metode-option selected-metode" data-value="qris" onclick="togglePanel('qris')">
                        <input type="radio" name="metode_bayar" value="qris" class="hidden" checked>
                        <div class="text-3xl mb-1">📱</div>
                        <div class="text-xs font-semibold">QRIS</div>
                    </label>
                    <label class="border rounded-xl p-4 text-center cursor-pointer hover:border-blue-400 transition metode-option" data-value="transfer" onclick="togglePanel('transfer')">
                        <input type="radio" name="metode_bayar" value="transfer" class="hidden">
                        <div class="text-3xl mb-1">🏦</div>
                        <div class="text-xs font-semibold">Transfer Bank</div>
                    </label>
                </div>
            </div>

            <div id="panelQris" class="mb-4">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-4 py-3 text-center">
                        <span class="text-white font-bold text-lg tracking-wider">QRIS</span>
                    </div>
                    <div class="p-6 text-center">
                        <div class="w-48 h-48 mx-auto mb-3 bg-white p-2 rounded-lg shadow-sm border border-gray-100">
                            <svg viewBox="0 0 200 200" class="w-full h-full">
                                <rect x="8" y="8" width="44" height="44" fill="white" stroke="black" stroke-width="3"/>
                                <rect x="12" y="12" width="8" height="8" fill="black"/><rect x="24" y="12" width="8" height="8" fill="black"/>
                                <rect x="36" y="12" width="8" height="8" fill="black"/><rect x="12" y="24" width="8" height="8" fill="black"/>
                                <rect x="36" y="24" width="8" height="8" fill="black"/><rect x="12" y="36" width="8" height="8" fill="black"/>
                                <rect x="24" y="36" width="8" height="8" fill="black"/><rect x="36" y="36" width="8" height="8" fill="black"/>
                                <rect x="148" y="8" width="44" height="44" fill="white" stroke="black" stroke-width="3"/>
                                <rect x="152" y="12" width="8" height="8" fill="black"/><rect x="164" y="12" width="8" height="8" fill="black"/>
                                <rect x="176" y="12" width="8" height="8" fill="black"/><rect x="152" y="24" width="8" height="8" fill="black"/>
                                <rect x="176" y="24" width="8" height="8" fill="black"/><rect x="152" y="36" width="8" height="8" fill="black"/>
                                <rect x="164" y="36" width="8" height="8" fill="black"/><rect x="176" y="36" width="8" height="8" fill="black"/>
                                <rect x="8" y="148" width="44" height="44" fill="white" stroke="black" stroke-width="3"/>
                                <rect x="12" y="152" width="8" height="8" fill="black"/><rect x="24" y="152" width="8" height="8" fill="black"/>
                                <rect x="36" y="152" width="8" height="8" fill="black"/><rect x="12" y="164" width="8" height="8" fill="black"/>
                                <rect x="36" y="164" width="8" height="8" fill="black"/><rect x="12" y="176" width="8" height="8" fill="black"/>
                                <rect x="24" y="176" width="8" height="8" fill="black"/><rect x="36" y="176" width="8" height="8" fill="black"/>
                                <rect x="64" y="12" width="8" height="8" fill="black"/><rect x="80" y="12" width="8" height="8" fill="black"/>
                                <rect x="96" y="12" width="8" height="8" fill="white"/><rect x="112" y="12" width="8" height="8" fill="black"/>
                                <rect x="128" y="12" width="8" height="8" fill="black"/><rect x="12" y="68" width="8" height="8" fill="black"/>
                                <rect x="28" y="68" width="8" height="8" fill="black"/><rect x="44" y="68" width="8" height="8" fill="white"/>
                                <rect x="60" y="68" width="8" height="8" fill="black"/><rect x="84" y="68" width="8" height="8" fill="black"/>
                                <rect x="108" y="68" width="8" height="8" fill="black"/><rect x="124" y="68" width="8" height="8" fill="white"/>
                                <rect x="148" y="68" width="8" height="8" fill="black"/><rect x="164" y="68" width="8" height="8" fill="black"/>
                                <rect x="180" y="68" width="8" height="8" fill="black"/><rect x="12" y="84" width="8" height="8" fill="white"/>
                                <rect x="36" y="84" width="8" height="8" fill="black"/><rect x="60" y="84" width="8" height="8" fill="black"/>
                                <rect x="84" y="84" width="8" height="8" fill="white"/><rect x="100" y="84" width="8" height="8" fill="black"/>
                                <rect x="132" y="84" width="8" height="8" fill="black"/><rect x="148" y="84" width="8" height="8" fill="white"/>
                                <rect x="172" y="84" width="8" height="8" fill="black"/><rect x="12" y="100" width="8" height="8" fill="black"/>
                                <rect x="36" y="100" width="8" height="8" fill="white"/><rect x="52" y="100" width="8" height="8" fill="black"/>
                                <rect x="76" y="100" width="8" height="8" fill="black"/><rect x="100" y="100" width="8" height="8" fill="black"/>
                                <rect x="116" y="100" width="8" height="8" fill="white"/><rect x="140" y="100" width="8" height="8" fill="white"/>
                                <rect x="164" y="100" width="8" height="8" fill="black"/><rect x="180" y="100" width="8" height="8" fill="black"/>
                                <rect x="12" y="116" width="8" height="8" fill="black"/><rect x="36" y="116" width="8" height="8" fill="white"/>
                                <rect x="60" y="116" width="8" height="8" fill="black"/><rect x="84" y="116" width="8" height="8" fill="black"/>
                                <rect x="100" y="116" width="8" height="8" fill="white"/><rect x="132" y="116" width="8" height="8" fill="black"/>
                                <rect x="148" y="116" width="8" height="8" fill="black"/><rect x="172" y="116" width="8" height="8" fill="black"/>
                                <rect x="12" y="132" width="8" height="8" fill="black"/><rect x="28" y="132" width="8" height="8" fill="black"/>
                                <rect x="44" y="132" width="8" height="8" fill="white"/><rect x="76" y="132" width="8" height="8" fill="white"/>
                                <rect x="108" y="132" width="8" height="8" fill="black"/><rect x="124" y="132" width="8" height="8" fill="black"/>
                                <rect x="148" y="132" width="8" height="8" fill="black"/><rect x="164" y="132" width="8" height="8" fill="white"/>
                                <rect x="180" y="132" width="8" height="8" fill="black"/><rect x="64" y="180" width="8" height="8" fill="white"/>
                                <rect x="80" y="180" width="8" height="8" fill="black"/><rect x="96" y="180" width="8" height="8" fill="black"/>
                                <rect x="112" y="180" width="8" height="8" fill="white"/><rect x="128" y="180" width="8" height="8" fill="black"/>
                                <rect x="84" y="84" width="32" height="32" fill="white" rx="4"/>
                                <text x="100" y="103" text-anchor="middle" font-size="9" font-weight="bold" fill="#1D4ED8">QR</text>
                                <text x="100" y="114" text-anchor="middle" font-size="7" font-weight="bold" fill="#1D4ED8">IS</text>
                            </svg>
                        </div>
                        <p class="text-xl font-bold text-gray-800 mb-1">Rp {{ number_format($pembayaran->total_biaya, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mb-3">MEDCAMPUS KLINIK DIGITAL</p>
                        <div class="bg-gray-50 rounded-lg px-3 py-2 text-xs text-gray-500">
                            <p>QRIS akan ditampilkan ke pasien</p>
                            <p>untuk scan pembayaran</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
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
        el.classList.remove('border-blue-500', 'bg-blue-50');
        if (el.dataset.value === val) {
            el.classList.add('border-blue-500', 'bg-blue-50');
        }
    });
}
</script>
@endpush
@endsection
