<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekam Medis</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; padding: 40px; color: #1f2937; }
        h2 { text-align: center; color: #0ea5e9; margin-bottom: 30px; }
        .info-table { width: 100%; margin-bottom: 24px; }
        .info-table td { padding: 4px 8px; }
        .info-table td:first-child { font-weight: 600; width: 120px; }
        .section { margin-bottom: 24px; }
        .section h4 { background: #0ea5e9; color: #fff; padding: 8px 14px; border-radius: 6px; font-size: 14px; margin: 0 0 10px 0; }
        .section p { margin: 0; line-height: 1.6; }
        table.obat-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.obat-table th { background: #f3f4f6; padding: 8px 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
        table.obat-table td { padding: 6px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        table.obat-table .text-right { text-align: right; }
        table.obat-table .text-center { text-align: center; }
        .total-row td { font-weight: 700; border-top: 2px solid #0ea5e9; padding-top: 8px; }
        .footer { text-align: right; margin-top: 60px; color: #6b7280; font-size: 12px; }
        .stamp { margin-top: 30px; text-align: right; }
        .stamp .ttd { margin-top: 60px; font-size: 13px; }
    </style>
</head>
<body>
    <h2>REKAM MEDIS</h2>
    <table class="info-table">
        <tr><td>Pasien:</td><td>{{ $rm->pasien->user->name ?? '-' }}</td></tr>
        <tr><td>Dokter:</td><td>dr. {{ $rm->dokter->user->name ?? '-' }}</td></tr>
        <tr><td>Tanggal:</td><td>{{ $rm->created_at->format('Y-m-d H:i') }}</td></tr>
        <tr><td>No. Antrian:</td><td>{{ $rm->antrian->nomor_antrian ?? '-' }}</td></tr>
    </table>

    <div class="section">
        <h4>Diagnosa</h4>
        <p>{{ $rm->diagnosa ?? '-' }}</p>
    </div>

    <div class="section">
        <h4>Tindakan</h4>
        <p>{{ $rm->tindakan ?? '-' }}</p>
    </div>

    <div class="section">
        <h4>Catatan Dokter</h4>
        <p>{{ $rm->catatan_dokter ?? '-' }}</p>
    </div>

    <div class="section">
        <h4>Resep Obat</h4>
        @if($rm->resepObat->count())
        <table class="obat-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rm->resepObat as $i => $ro)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $ro->obat->nama_obat ?? '-' }}</td>
                    <td class="text-center">{{ $ro->jumlah }}</td>
                    <td class="text-center">{{ $ro->obat->satuan ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($ro->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($ro->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                @if($rm->pembayaran)
                <tr class="total-row">
                    <td colspan="5" class="text-right">Total Biaya Obat:</td>
                    <td class="text-right">Rp {{ number_format($rm->pembayaran->total_biaya, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
        @else
        <p>{!! nl2br(e($rm->resep_obat ?? '-')) !!}</p>
        @endif
    </div>

    <div class="stamp">
        <p>Hormat Kami,</p>
        <div class="ttd">dr. {{ $rm->dokter->user->name ?? '.........' }}</div>
    </div>

    <p class="footer">Dicetak: {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
