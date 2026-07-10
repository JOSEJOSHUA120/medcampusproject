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
use App\Models\User;
use Illuminate\Database\Seeder;

class DummyPasienSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $today = $now->toDateString();
        $twoDaysAgo = $now->copy()->subDays(2)->toDateString();

        $dokters = Dokter::limit(3)->get();
        $obatList = Obat::take(6)->get();

        $newPatients = [
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'dummy1@gmail.com',
                'no_telp' => '0877111111',
                'alamat' => 'Jl. Anggrek No. 10, Jakarta',
                'tanggal_lahir' => '1992-03-20',
                'jenis_kelamin' => 'P',
                'keluhan' => 'Nyeri ulu hati dan mual setelah makan',
                'diagnosa' => 'Gastritis akut (radang lambung)',
                'tindakan' => 'Pemberian obat antasida dan inhibitor proton pump',
                'catatan' => 'Hindari makanan pedas dan asam. Konsumsi makanan kecil tapi sering.',
                'resep' => "Omeprazole 20mg 1x1\nAntasida sirup 3x1\nRanitidine 150mg 2x1",
                'biaya' => 185000,
            ],
            [
                'name' => 'Bambang Suprapto',
                'email' => 'dummy2@gmail.com',
                'no_telp' => '0877222222',
                'alamat' => 'Jl. Kenanga No. 25, Bandung',
                'tanggal_lahir' => '1985-07-14',
                'jenis_kelamin' => 'L',
                'keluhan' => 'Lutut kanan bengkak dan nyeri saat ditekuk',
                'diagnosa' => 'Osteoarthritis lutut kanan',
                'tindakan' => 'Terapi fisik dan pemberian antiinflamasi',
                'catatan' => 'Kurangi aktivitas berat. Kompres dingin jika bengkak.',
                'resep' => "Ibuprofen 400mg 3x1\nGlucosamine 500mg 2x1\nParacetamol 500mg jika nyeri",
                'biaya' => 220000,
            ],
            [
                'name' => 'Rina Marlina',
                'email' => 'dummy3@gmail.com',
                'no_telp' => '0877333333',
                'alamat' => 'Jl. Melati No. 5, Surabaya',
                'tanggal_lahir' => '1993-11-28',
                'jenis_kelamin' => 'P',
                'keluhan' => 'Ruam merah dan gatal di kedua lengan',
                'diagnosa' => 'Dermatitis kontak alergi',
                'tindakan' => 'Pemberian kortikosteroid topikal dan antihistamin',
                'catatan' => 'Hindari sabun dan deterjen berbahan keras. Gunakan pelembap.',
                'resep' => "Cetirizine 10mg 1x1\nSalep hidrokortison 2% oles 2x1\nMethylprednisolone 4mg 2x1",
                'biaya' => 165000,
            ],
        ];

        foreach ($newPatients as $i => $data) {
            $dokter = $dokters[$i % $dokters->count()];

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('password'),
                'role' => 'pasien',
            ]);

            $pasien = Pasien::create([
                'user_id' => $user->id,
                'no_telp' => $data['no_telp'],
                'alamat' => $data['alamat'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
            ]);

            $jadwal = JadwalDokter::where('user_id', $dokter->user_id)->first();
            $jadwalId = $jadwal ? $jadwal->id : null;

            $totalMinutes = $i * 30;
            $hour = 8 + intdiv($totalMinutes, 60);
            $minute = $totalMinutes % 60;
            $jamStr = sprintf('%02d:%02d:00', $hour, $minute);

            $booking = Booking::create([
                'pasien_id' => $user->id,
                'dokter_id' => $dokter->user_id,
                'jadwal_dokter_id' => $jadwalId ?? 1,
                'tanggal_booking' => $twoDaysAgo,
                'jam_booking' => $jamStr,
                'keluhan_awal' => $data['keluhan'],
                'status' => 'selesai',
            ]);

            $antrian = Antrian::create([
                'pasien_id' => $pasien->id,
                'dokter_id' => $dokter->id,
                'nomor_antrian' => str_pad(100 + $i + 1, 3, '0', STR_PAD_LEFT),
                'tanggal_antrian' => $twoDaysAgo,
                'jam_antrian' => $jamStr,
                'status' => 'selesai',
                'complaint' => $data['keluhan'],
                'duration' => $i === 0 ? '1 minggu' : ($i === 1 ? '2 minggu' : '3 hari'),
                'pain_level' => 5 + $i,
            ]);

            $rm = RekamMedis::create([
                'pasien_id' => $pasien->id,
                'dokter_id' => $dokter->id,
                'antrian_id' => $antrian->id,
                'diagnosa' => $data['diagnosa'],
                'tindakan' => $data['tindakan'],
                'catatan_dokter' => $data['catatan'],
                'resep_obat' => $data['resep'],
            ]);

            if ($obatList->count() >= 3) {
                $obats = $obatList->slice($i * 2 % $obatList->count(), 2);
                foreach ($obats as $obat) {
                    $qty = rand(5, 15);
                    $rm->resepObat()->create([
                        'obat_id' => $obat->id,
                        'jumlah' => $qty,
                        'harga_satuan' => $obat->harga,
                        'subtotal' => $obat->harga * $qty,
                    ]);
                }
            }

            Pembayaran::create([
                'rekam_medis_id' => $rm->id,
                'tanggal_bayar' => $twoDaysAgo,
                'metode_bayar' => ['Tunai', 'Transfer Bank', 'E-Wallet'][$i],
                'status_bayar' => 'lunas',
                'total_biaya' => $data['biaya'],
                'nomor_referensi' => 'INV-' . strtoupper(substr($data['name'], 0, 3)) . '-' . $twoDaysAgo,
                'bank' => $i === 1 ? 'BCA' : null,
            ]);
        }

        $this->command->info('3 dummy pasien created successfully!');
    }
}
