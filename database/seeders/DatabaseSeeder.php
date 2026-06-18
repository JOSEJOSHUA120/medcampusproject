<?php

namespace Database\Seeders;

use App\Models\Antrian;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data awal aplikasi.
     * Menggunakan Eloquent ORM (Active Record) untuk insert data ke database
     */
    public function run()
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        # Admin
        $admin = User::create([
            'name' => 'Admin Medcampus',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        # Dokter (3 user + 3 dokter) — dengan jadwal praktik
        # Dr. Axel Mahendra (Umum): Sen - Jum, 06:00 - 23:00
        # Dr. Siti Rahma (Anak): 12:00 - 18:00
        # Dr. Budi Santoso (Gigi): 09:00 - 15:00
        $dokterUsers = [];
        $dokterData = [
            ['nama_dokter' => 'Dr. Axel Mahendra', 'spesialisasi' => 'Umum', 'no_telp' => '0811111111', 'jam_mulai' => '06:00', 'jam_selesai' => '23:00', 'hari' => 'Sen,Sel,Rab,Kam,Jum'],
            ['nama_dokter' => 'Dr. Siti Rahma', 'spesialisasi' => 'Anak', 'no_telp' => '0812222222', 'jam_mulai' => '12:00', 'jam_selesai' => '18:00', 'hari' => 'Sen,Sel,Rab,Kam,Jum'],
            ['nama_dokter' => 'Dr. Budi Santoso', 'spesialisasi' => 'Gigi', 'no_telp' => '0813333333', 'jam_mulai' => '09:00', 'jam_selesai' => '15:00', 'hari' => 'Sen,Sel,Rab,Kam,Jum'],
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
                'jam_praktek_mulai' => $d['jam_mulai'],
                'jam_praktek_selesai' => $d['jam_selesai'],
                'hari_praktek' => $d['hari'],
            ]);
        }

        # Pasien (3 user + 3 pasien)
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

        # Antrian (5 records — some yesterday for history, some today for active flow)
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

        # Rekam Medis (2 records for the "selesai" antrian + resep obat)
        $rmList = [];
        $rekamMedisData = [
            ['pasien' => 1, 'dokter' => 0, 'antrian' => 1, 'diagnosa' => 'Batuk pilek', 'tindakan' => 'Terapi uap', 'catatan' => 'Minum air hangat', 'resep' => "Paracetamol 500mg 3x1\nAmoxicillin 250mg 2x1"],
        ];
        foreach ($rekamMedisData as $r) {
            $rmList[] = RekamMedis::create([
                'pasien_id' => $pasienList[$r['pasien']]->id,
                'dokter_id' => $dokterUsers[$r['dokter']]->id,
                'antrian_id' => $antrianList[$r['antrian']]->id,
                'diagnosa' => $r['diagnosa'],
                'tindakan' => $r['tindakan'],
                'catatan_dokter' => $r['catatan'],
                'resep_obat' => $r['resep'],
            ]);
        }

        # Pembayaran (1 lunas + 1 belum_bayar)
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
            'total_biaya' => 200000,
        ]);
    }
}
