<?php

namespace Database\Seeders;

use App\Models\Antrian;
use App\Models\Booking;
use App\Models\Dokter;
use App\Models\JadwalDokter;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\RekamMedis;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\URL;

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
            'foto' => URL::to('/images/admin-profile.jpg'),
        ]);

        $dokterUsers = [];
        $dokterData = [
            ['nama_dokter' => 'Dr. Axel Mahendra', 'spesialisasi' => 'Umum', 'no_telp' => '0811111111', 'email' => 'dokter@gmail.com'],
            ['nama_dokter' => 'Dr. Siti Rahma', 'spesialisasi' => 'Anak', 'no_telp' => '0812222222', 'email' => 'dokter2@gmail.com'],
            ['nama_dokter' => 'Dr. Budi Santoso', 'spesialisasi' => 'Gigi', 'no_telp' => '0813333333', 'email' => 'dokter3@gmail.com'],
            ['nama_dokter' => 'Dr. Adrian Wijaya', 'spesialisasi' => 'Dokter Mata', 'no_telp' => '081234567814', 'email' => 'adrianwijaya@gmail.com'],
            ['nama_dokter' => 'Dr. Celine Maharani', 'spesialisasi' => 'Dokter Saraf', 'no_telp' => '081234567817', 'email' => 'celinemaharani@gmail.com'],
            ['nama_dokter' => 'Dr. Vania Kusuma', 'spesialisasi' => 'Dokter Kulit', 'no_telp' => '081234567813', 'email' => 'vaniakusuma@gmail.com'],
        ];
        foreach ($dokterData as $d) {
            $isMainDokter = $d['email'] === 'dokter@gmail.com';
            $user = User::create([
                'name' => $d['nama_dokter'],
                'email' => $d['email'],
                'password' => bcrypt('password'),
                'role' => 'dokter',
                'foto' => $isMainDokter ? URL::to('/images/doctor-profile.jpg') : null,
            ]);
            $dokterUsers[] = Dokter::create([
                'user_id' => $user->id,
                'nama_dokter' => $d['nama_dokter'],
                'spesialisasi' => $d['spesialisasi'],
                'no_telp' => $d['no_telp'],
                'foto' => $isMainDokter ? URL::to('/images/doctor-profile.jpg') : null,
            ]);
        }

        $pasienList = [];
        $pasienData = [
            ['name' => 'Ahmad Fauzi', 'no_telp' => '0814444444', 'alamat' => 'Jl. Merdeka No.1', 'tanggal_lahir' => '1990-05-15', 'jenis_kelamin' => 'L'],
            ['name' => 'Dewi Lestari', 'no_telp' => '0815555555', 'alamat' => 'Jl. Sudirman No.2', 'tanggal_lahir' => '1995-08-20', 'jenis_kelamin' => 'P'],
            ['name' => 'Rudi Hermawan', 'no_telp' => '0816666666', 'alamat' => 'Jl. Diponegoro No.3', 'tanggal_lahir' => '1988-12-10', 'jenis_kelamin' => 'L'],
        ];
        foreach ($pasienData as $i => $p) {
            $isMainPasien = $i === 0;
            $user = User::create([
                'name' => $p['name'],
                'email' => $isMainPasien ? "pasien@gmail.com" : "pasien" . ($i + 1) . "@gmail.com",
                'password' => bcrypt('password'),
                'role' => 'pasien',
                'foto' => $isMainPasien ? URL::to('/images/user-profile.jpg') : null,
            ]);
            $pasienList[] = Pasien::create([
                'user_id' => $user->id,
                'no_telp' => $p['no_telp'],
                'alamat' => $p['alamat'],
                'tanggal_lahir' => $p['tanggal_lahir'],
                'jenis_kelamin' => $p['jenis_kelamin'],
                'foto' => $isMainPasien ? URL::to('/images/user-profile.jpg') : null,
            ]);
        }

        $rooms = [];
        for ($i = 1; $i <= 5; $i++) {
            $rooms[] = Room::create([
                'room_number' => 'R-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'status' => 'free',
                'description' => 'Ruangan praktek ' . $i,
            ]);
        }

        Obat::factory()->count(100)->create();

        $hariMapping = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jadwalList = [];
        foreach ($dokterUsers as $di => $dokter) {
            $user = $dokter->user;
            $hariDokter = array_slice($hariMapping, ($di * 2) % 6, 3);
            foreach ($hariDokter as $hi => $hari) {
                $jamMulai = 8 + $hi * 3;
                $jadwalList[] = JadwalDokter::create([
                    'user_id' => $user->id,
                    'hari' => $hari,
                    'jam_mulai' => sprintf('%02d:00', $jamMulai),
                    'jam_selesai' => sprintf('%02d:00', $jamMulai + 2),
                    'durasi_slot' => 30,
                    'kuota' => 4,
                    'status' => 'aktif',
                ]);
            }
        }

        foreach ($pasienList as $pi => $pasien) {
            if ($pi < 2 && isset($jadwalList[$pi])) {
                Booking::create([
                    'pasien_id' => $pasien->user_id,
                    'dokter_id' => $dokterUsers[$pi]->user_id,
                    'jadwal_dokter_id' => $jadwalList[$pi]->id,
                    'tanggal_booking' => $today,
                    'jam_booking' => sprintf('%02d:00', 8 + $pi),
                    'keluhan_awal' => $pi === 0 ? 'Batuk pilek, ingin periksa' : 'Demam tinggi, ingin konsultasi',
                    'status' => $pi === 0 ? 'menunggu' : 'disetujui',
                ]);
            }
        }

        $antrianList = [];
        $antrianData = [
            ['pasien' => 0, 'dokter' => 0, 'tanggal' => $yesterday, 'jam' => '08:00', 'status' => 'selesai', 'complaint' => 'Batuk pilek sejak 3 hari', 'pain' => 3],
            ['pasien' => 1, 'dokter' => 0, 'tanggal' => $yesterday, 'jam' => '08:30', 'status' => 'selesai', 'complaint' => 'Demam tinggi 38.5°C', 'pain' => 6],
            ['pasien' => 2, 'dokter' => 1, 'tanggal' => $today, 'jam' => '09:00', 'status' => 'menunggu', 'complaint' => 'Sakit perut bagian bawah', 'pain' => 5],
            ['pasien' => 0, 'dokter' => 1, 'tanggal' => $today, 'jam' => '10:00', 'status' => 'dipanggil', 'complaint' => 'Sakit kepala migrain', 'pain' => 7],
            ['pasien' => 1, 'dokter' => 2, 'tanggal' => $today, 'jam' => '11:00', 'status' => 'sedang_dilayani', 'complaint' => 'Sakit gigi berlubang', 'pain' => 8],
        ];
        foreach ($antrianData as $i => $a) {
            $antrianList[] = Antrian::create([
                'pasien_id' => $pasienList[$a['pasien']]->id,
                'dokter_id' => $dokterUsers[$a['dokter']]->id,
                'nomor_antrian' => str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'tanggal_antrian' => $a['tanggal'],
                'jam_antrian' => $a['jam'] . ':00',
                'status' => $a['status'],
                'complaint' => $a['complaint'],
                'pain_level' => $a['pain'],
                'duration' => $i < 2 ? '3 hari' : ($i === 2 ? '2 hari' : ($i === 3 ? '1 minggu' : '1 hari')),
            ]);
        }

        $rmList = [];
        $rekamMedisData = [
            ['pasien' => 0, 'dokter' => 0, 'antrian' => 0, 'diagnosa' => 'Batuk pilek', 'tindakan' => 'Terapi uap', 'catatan' => 'Minum air hangat', 'resep' => "Paracetamol 500mg 3x1\nAmoxicillin 250mg 2x1"],
            ['pasien' => 1, 'dokter' => 0, 'antrian' => 1, 'diagnosa' => 'Demam tinggi', 'tindakan' => 'Kompres hangat', 'catatan' => 'Istirahat total', 'resep' => "Paracetamol 500mg 3x1\nIbuprofen 400mg 2x1"],
        ];
        $obatList = Obat::take(8)->get();
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

            if ($i === 0 && $obatList->count() >= 2) {
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
