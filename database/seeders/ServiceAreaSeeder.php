<?php

namespace Database\Seeders;

use App\Models\ServiceArea;
use App\Models\Village;
use Illuminate\Database\Seeder;

class ServiceAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villageMap = Village::pluck('id', 'code');

        $serviceAreas = [
            [
                'village_code' => '35.03.01.1001', // Sumbergedong
                'name' => 'Area Alun-Alun & Pendopo Trenggalek',
                'description' => 'Pusat kegiatan publik alun-alun, taman kota, dan kawasan Pendopo Manggala Praja Nugraha',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1001', // Sumbergedong
                'name' => 'Area Pasar Sore & Pertokoan Sudirman',
                'description' => 'Kawasan niaga Jl. Panglima Sudirman, ruko, dan Pasar Sore Sumbergedong',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1002', // Ngantru
                'name' => 'Area Pasar Basah Trenggalek',
                'description' => 'Sentra pasar tradisional basah Trenggalek dengan volume timbulan sampah harian tinggi',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1002', // Ngantru
                'name' => 'Area Terminal Bus Surodakan - Ngantru',
                'description' => 'Kawasan terminal bus antar kota, sub-terminal, dan kios pedagang kaki lima',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1003', // Surodakan
                'name' => 'Area Kompleks Perkantoran Pemkab',
                'description' => 'Kompleks kantor dinas instansi Pemkab Trenggalek dan fasilitas layanan publik',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1003', // Surodakan
                'name' => 'Area Pemukiman Surodakan Asri',
                'description' => 'Zona perumahan dan permukiman warga Kelurahan Surodakan',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1004', // Kelutan
                'name' => 'Area Pemukiman Kelutan Permai & Stadion Menak Sopal',
                'description' => 'Kawasan olahraga, stadion kebanggaan Menak Sopal, dan kompleks perumahan Kelutan',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1004', // Kelutan
                'name' => 'Area TPST Kelutan Mandiri',
                'description' => 'Kawasan pengolahan sampah dan pemilahan mandiri TPS3R Kelutan',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.01.1005', // Tamanan
                'name' => 'Area Sentra Industri Keripik Tempe Tamanan',
                'description' => 'Sentra industri rumahan oleh-oleh khas keripik tempe dan UMKM kuliner',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.02.2002', // Bendorejo (Pogalan)
                'name' => 'Area Pasar Sub-Terminal Pogalan - Bendorejo',
                'description' => 'Pusat perniagaan pasar dan sub-terminal penghubung di Kecamatan Pogalan',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.03.2001', // Durenan
                'name' => 'Area Pasar Tradisional Durenan & Jalur Protokol',
                'description' => 'Pasar Durenan dan jalur protokol perbatasan Trenggalek - Tulungagung',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.05.2001', // Karangan
                'name' => 'Area Pasar Hewan & Pasar Rakyat Karangan',
                'description' => 'Kawasan perdagangan pasar rakyat desa dan pasar hewan Karangan',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.07.2001', // Srabah (Bendungan)
                'name' => 'Zona Operasional Pemrosesan TPA Srabah',
                'description' => 'Kawasan inti Tempat Pemrosesan Akhir sampah kabupaten DLH Trenggalek di Desa Srabah',
                'is_active' => true,
            ],
            [
                'village_code' => '35.03.09.2002', // Tasikmadu (Watulimo)
                'name' => 'Area Wisata Bahari Pantai Prigi & Pelabuhan Perikanan',
                'description' => 'Kawasan destinasi wisata pantai pesisir selatan, TPI, dan pelabuhan nusantara',
                'is_active' => true,
            ],
        ];

        foreach ($serviceAreas as $areaData) {
            $villageId = $villageMap[$areaData['village_code']] ?? null;

            if ($villageId) {
                ServiceArea::updateOrCreate(
                    [
                        'village_id' => $villageId,
                        'name' => $areaData['name'],
                    ],
                    [
                        'description' => $areaData['description'],
                        'is_active' => $areaData['is_active'],
                    ]
                );
            }
        }
    }
}
