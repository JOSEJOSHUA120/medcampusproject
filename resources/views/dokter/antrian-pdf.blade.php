<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pasien - {{ $antrian->pasien->user->name ?? 'Tanpa Nama' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 40px; color: #333; }
        h1 { text-align: center; font-size: 22px; margin-bottom: 6px; color: #111; }
        .subtitle { text-align: center; font-size: 12px; color: #888; margin-bottom: 30px; }
        table.info { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.info td { padding: 10px 14px; border: 1px solid #ccc; }
        table.info .label { font-weight: 600; background: #f7f7f7; width: 35%; color: #444; }
        table.info .value { color: #222; }
        .footer { position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <h1>DATA PASIEN</h1>
    <p class="subtitle">MEDCAMPUS — Dokter Panel</p>

    <table class="info">
        <tr>
            <td class="label">Nama Pasien</td>
            <td class="value">{{ $antrian->pasien->user->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Periksa</td>
            <td class="value">{{ \Carbon\Carbon::parse($antrian->tanggal_antrian)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Dokter</td>
            <td class="value">{{ $antrian->dokter->user->name ?? $antrian->dokter->nama_dokter ?? auth()->user()->name }}</td>
        </tr>
        <tr>
            <td class="label">Keluhan Penyakit</td>
            <td class="value">{{ $antrian->complaint ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Catatan Tambahan</td>
            <td class="value">{{ $antrian->notes ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Ruangan</td>
            <td class="value">{{ $antrian->room->room_number ?? '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i:s') }} &mdash; &copy; MEDCAMPUS
    </div>
</body>
</html>
