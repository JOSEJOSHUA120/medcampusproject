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
        .footer { text-align: right; margin-top: 60px; color: #6b7280; font-size: 12px; }
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
        <p>{!! nl2br(e($rm->resep_obat ?? '-')) !!}</p>
    </div>

    <p class="footer">Dicetak: {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
