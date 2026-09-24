<?php

namespace Database\Seeders;

use App\Enums\LocationType;
use App\Models\Location;
use App\Models\ServiceArea;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceAreaMap = ServiceArea::pluck('id', 'name');

        $locations = [
            // TPA Utama Kabupaten Trenggalek
            [
                'service_area_name' => 'Zona Operasional Pemrosesan TPA Srabah',
                'name' => 'TPA Srabah',
                'type' => LocationType::Tpa,
                'address' => 'Jl. Raya Srabah - Bendungan Km. 4, Desa Srabah',
                'latitude' => -8.02055600,
                'longitude' => 111.72583300,
                'is_active' => true,
            ],

            // TPST
            [
                'service_area_name' => 'Area Pasar Sore & Pertokoan Sudirman',
                'name' => 'TPST Sumbergedong',
                'type' => LocationType::Tpst,
                'address' => 'Jl. Ki Mangun Sarkoro, Kel. Sumbergedong',
                'latitude' => -8.05312300,
                'longitude' => 111.71345600,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area TPST Kelutan Mandiri',
                'name' => 'TPST Kelutan Mandiri (TPS3R)',
                'type' => LocationType::Tpst,
                'address' => 'Jl. Menak Sopal Gg. Flamboyan, Kel. Kelutan',
                'latitude' => -8.06234100,
                'longitude' => 111.70891200,
                'is_active' => true,
            ],

            // TPS
            [
                'service_area_name' => 'Area Alun-Alun & Pendopo Trenggalek',
                'name' => 'TPS Alun-Alun Trenggalek',
                'type' => LocationType::Tps,
                'address' => 'Sisi Timur Alun-Alun Trenggalek, Kel. Sumbergedong',
                'latitude' => -8.04891200,
                'longitude' => 111.71234500,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Pasar Sore & Pertokoan Sudirman',
                'name' => 'TPS Pasar Sore Trenggalek',
                'type' => LocationType::Tps,
                'address' => 'Kompleks Belakang Pasar Sore, Kel. Sumbergedong',
                'latitude' => -8.05211100,
                'longitude' => 111.71422200,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Pasar Basah Trenggalek',
                'name' => 'TPS Pasar Basah Trenggalek',
                'type' => LocationType::Tps,
                'address' => 'Pintu Masuk Bongkar Muat Pasar Basah, Kel. Ngantru',
                'latitude' => -8.04987600,
                'longitude' => 111.71543200,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Terminal Bus Surodakan - Ngantru',
                'name' => 'TPS Terminal Bus Surodakan',
                'type' => LocationType::Tps,
                'address' => 'Sisi Barat Terminal Surodakan, Kel. Ngantru',
                'latitude' => -8.05678900,
                'longitude' => 111.71987600,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Pasar Tradisional Durenan & Jalur Protokol',
                'name' => 'TPS Pasar Tradisional Durenan',
                'type' => LocationType::Tps,
                'address' => 'Jl. Raya Durenan - Bandung, Desa Durenan',
                'latitude' => -8.08234500,
                'longitude' => 111.80123400,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Pasar Hewan & Pasar Rakyat Karangan',
                'name' => 'TPS Pasar Rakyat Karangan',
                'type' => LocationType::Tps,
                'address' => 'Jl. Raya Trenggalek - Ponorogo, Desa Karangan',
                'latitude' => -8.06789100,
                'longitude' => 111.66789100,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Wisata Bahari Pantai Prigi & Pelabuhan Perikanan',
                'name' => 'TPS Wisata Pantai Prigi 360',
                'type' => LocationType::Tps,
                'address' => 'Kawasan Panggung 360 Pantai Prigi, Desa Tasikmadu',
                'latitude' => -8.28345600,
                'longitude' => 111.72123400,
                'is_active' => true,
            ],

            // Pickup Points (Titik Jemput)
            [
                'service_area_name' => 'Area Kompleks Perkantoran Pemkab',
                'name' => 'Titik Jemput RSUD dr. Soedomo',
                'type' => LocationType::PickupPoint,
                'address' => 'Bak Sampah Domestik RSUD dr. Soedomo, Jl. dr. Soetomo No. 2',
                'latitude' => -8.05432100,
                'longitude' => 111.71876500,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Alun-Alun & Pendopo Trenggalek',
                'name' => 'Titik Jemput Pendopo Trenggalek',
                'type' => LocationType::PickupPoint,
                'address' => 'Kawasan Halaman Belakang Pendopo, Kel. Sumbergedong',
                'latitude' => -8.04765400,
                'longitude' => 111.71187600,
                'is_active' => true,
            ],
            [
                'service_area_name' => 'Area Sentra Industri Keripik Tempe Tamanan',
                'name' => 'Titik Jemput Sentra Keripik Tamanan',
                'type' => LocationType::PickupPoint,
                'address' => 'Jl. Sukarno Hatta Sentra Keripik, Kel. Tamanan',
                'latitude' => -8.05891200,
                'longitude' => 111.72345600,
                'is_active' => true,
            ],
        ];

        foreach ($locations as $locData) {
            $serviceAreaId = $serviceAreaMap[$locData['service_area_name']] ?? null;

            if ($serviceAreaId) {
                Location::updateOrCreate(
                    [
                        'service_area_id' => $serviceAreaId,
                        'name' => $locData['name'],
                    ],
                    [
                        'type' => $locData['type'],
                        'address' => $locData['address'],
                        'latitude' => $locData['latitude'],
                        'longitude' => $locData['longitude'],
                        'is_active' => $locData['is_active'],
                    ]
                );
            }
        }
    }
}
