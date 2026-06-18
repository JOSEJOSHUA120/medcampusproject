<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Laporan Pembayaran</title>
<style>body{font-family:sans-serif;font-size:12px;}table{width:100%;border-collapse:collapse;margin-top:20px;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#2563EB;color:#fff;}.text-end{text-align:right;}.fw-bold{font-weight:bold;}</style>
</head>
<body>
    <h2 style="text-align:center;">LAPORAN PEMBAYARAN</h2>
    <p style="text-align:center;">Periode: {{ $tanggalAwal }} - {{ $tanggalAkhir }}</p>
    <table>
        <thead><tr><th>No</th><th>Pasien</th><th>Tanggal Bayar</th><th>Metode</th><th>Status</th><th class="text-end">Total</th></tr></thead>
        <tbody>
            @foreach($data as $i => $p)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $p->rekamMedis->pasien->user->name ?? '-' }}</td>
                <td>{{ $p->tanggal_bayar ?? '-' }}</td>
                <td>{{ $p->metode_bayar ?? '-' }}</td>
                <td>{{ $p->status_bayar == 'lunas' ? 'Lunas' : 'Belum Bayar' }}</td>
                <td class="text-end">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr><th colspan="5" class="text-end">Total</th><th class="text-end">Rp {{ number_format($total, 0, ',', '.') }}</th></tr>
        </tfoot>
    </table>
    <p style="text-align:right;margin-top:40px;">Dicetak: {{ now()->format('Y-m-d H:i') }}</p>
</body>
</html>
