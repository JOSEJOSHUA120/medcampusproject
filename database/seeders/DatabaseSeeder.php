<?php

namespace Database\Seeders;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $admin = User::create([
            'name' => 'Admin Medcampus',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $dokterUsers = [];
        $dokterData = [
            ['nama_dokter' => 'Dr. Axel Mahendra', 'spesialisasi' => 'Umum', 'no_telp' => '0811111111'],
            ['nama_dokter' => 'Dr. Siti Rahma', 'spesialisasi' => 'Anak', 'no_telp' => '0812222222'],
            ['nama_dokter' => 'Dr. Budi Santoso', 'spesialisasi' => 'Gigi', 'no_telp' => '0813333333'],
        ];
        foreach ($dokterData as $i => $d) {
            $user = User::create([
                'name' => $d['nama_dokter'],
                'email' => $i === 0 ? "dokter@gmail.com" : "dokter" . ($i + 1) . "@gmail.com",
                'password' => bcrypt('password'),
                'role' => 'dokter',
            ]);
            $dokterUsers[] = Dokter::create([
                'user_id' => $user->id,
                'nama_dokter' => $d['nama_dokter'],
                'spesialisasi' => $d['spesialisasi'],
                'no_telp' => $d['no_telp'],
            ]);
        }

        $pasienList = [];
        $pasienData = [
            ['name' => 'Ahmad Fauzi', 'no_telp' => '0814444444', 'alamat' => 'Jl. Merdeka No.1', 'tanggal_lahir' => '1990-05-15', 'jenis_kelamin' => 'L'],
            ['name' => 'Dewi Lestari', 'no_telp' => '0815555555', 'alamat' => 'Jl. Sudirman No.2', 'tanggal_lahir' => '1995-08-20', 'jenis_kelamin' => 'P'],
            ['name' => 'Rudi Hermawan', 'no_telp' => '0816666666', 'alamat' => 'Jl. Diponegoro No.3', 'tanggal_lahir' => '1988-12-10', 'jenis_kelamin' => 'L'],
        ];
        foreach ($pasienData as $i => $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => $i === 0 ? "pasien@gmail.com" : "pasien" . ($i + 1) . "@gmail.com",
                'password' => bcrypt('password'),
                'role' => 'pasien',
            ]);
            $pasienList[] = Pasien::create([
                'user_id' => $user->id,
                'no_telp' => $p['no_telp'],
                'alamat' => $p['alamat'],
                'tanggal_lahir' => $p['tanggal_lahir'],
                'jenis_kelamin' => $p['jenis_kelamin'],
            ]);
        }

        $obatList = [];
        $obatData = [
            ['nama_obat' => 'Paracetamol 500mg', 'harga' => 15000, 'satuan' => 'tablet', 'keterangan' => 'Obat demam & nyeri'],
            ['nama_obat' => 'Amoxicillin 250mg', 'harga' => 25000, 'satuan' => 'kapsul', 'keterangan' => 'Antibiotik'],
            ['nama_obat' => 'Ibuprofen 400mg', 'harga' => 20000, 'satuan' => 'tablet', 'keterangan' => 'Anti inflamasi'],
            ['nama_obat' => 'CTM 4mg', 'harga' => 5000, 'satuan' => 'tablet', 'keterangan' => 'Antihistamin / alergi'],
            ['nama_obat' => 'Vitamin C 500mg', 'harga' => 10000, 'satuan' => 'tablet', 'keterangan' => 'Suplemen vitamin'],
            ['nama_obat' => 'Antasida DOEN', 'harga' => 12000, 'satuan' => 'tablet', 'keterangan' => 'Obat maag'],
            ['nama_obat' => 'Salep Betadine 10g', 'harga' => 35000, 'satuan' => 'tube', 'keterangan' => 'Antiseptik luka'],
            ['nama_obat' => 'Dextromethorphan', 'harga' => 18000, 'satuan' => 'tablet', 'keterangan' => 'Obat batuk kering'],
        ];
        foreach ($obatData as $o) {
            $obatList[] = Obat::create($o);
        }

        $antrianList = [];
        $antrianData = [
            ['pasien' => 0, 'dokter' => 0, 'tanggal' => $yesterday, 'jam' => '08:00', 'status' => 'selesai'],
            ['pasien' => 1, 'dokter' => 0, 'tanggal' => $yesterday, 'jam' => '08:30', 'status' => 'selesai'],
            ['pasien' => 2, 'dokter' => 1, 'tanggal' => $today, 'jam' => '09:00', 'status' => 'menunggu'],
            ['pasien' => 0, 'dokter' => 1, 'tanggal' => $today, 'jam' => '10:00', 'status' => 'dipanggil'],
            ['pasien' => 1, 'dokter' => 2, 'tanggal' => $today, 'jam' => '11:00', 'status' => 'diperiksa'],
        ];
        foreach ($antrianData as $i => $a) {
            $antrianList[] = Antrian::create([
                'pasien_id' => $pasienList[$a['pasien']]->id,
                'dokter_id' => $dokterUsers[$a['dokter']]->id,
                'nomor_antrian' => str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'tanggal_antrian' => $a['tanggal'],
                'jam_antrian' => $a['jam'] . ':00',
                'status' => $a['status'],
            ]);
        }

        $rmList = [];
        $rekamMedisData = [
            ['pasien' => 0, 'dokter' => 0, 'antrian' => 0, 'diagnosa' => 'Batuk pilek', 'tindakan' => 'Terapi uap', 'catatan' => 'Minum air hangat', 'resep' => "Paracetamol 500mg 3x1\nAmoxicillin 250mg 2x1"],
            ['pasien' => 1, 'dokter' => 0, 'antrian' => 1, 'diagnosa' => 'Demam tinggi', 'tindakan' => 'Kompres hangat', 'catatan' => 'Istirahat total', 'resep' => "Paracetamol 500mg 3x1\nIbuprofen 400mg 2x1"],
        ];
        foreach ($rekamMedisData as $i => $r) {
            $rm = RekamMedis::create([
                'pasien_id' => $pasienList[$r['pasien']]->id,
                'dokter_id' => $dokterUsers[$r['dokter']]->id,
                'antrian_id' => $antrianList[$r['antrian']]->id,
                'diagnosa' => $r['diagnosa'],
                'tindakan' => $r['tindakan'],
                'catatan_dokter' => $r['catatan'],
                'resep_obat' => $r['resep'],
            ]);

            if ($i === 0) {
                $rm->resepObat()->create([
                    'obat_id' => $obatList[0]->id,
                    'jumlah' => 10,
                    'harga_satuan' => $obatList[0]->harga,
                    'subtotal' => $obatList[0]->harga * 10,
                ]);
                $rm->resepObat()->create([
                    'obat_id' => $obatList[1]->id,
                    'jumlah' => 5,
                    'harga_satuan' => $obatList[1]->harga,
                    'subtotal' => $obatList[1]->harga * 5,
                ]);
            }

            $rmList[] = $rm;
        }

        Pembayaran::create([
            'rekam_medis_id' => $rmList[0]->id,
            'tanggal_bayar' => $yesterday,
            'metode_bayar' => 'Tunai',
            'status_bayar' => 'lunas',
            'total_biaya' => 150000,
        ]);
        Pembayaran::create([
            'rekam_medis_id' => $rmList[0]->id,
            'tanggal_bayar' => null,
            'metode_bayar' => null,
            'status_bayar' => 'belum_bayar',
            'total_biaya' => 275000,
        ]);
    }
}
