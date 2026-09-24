<?php

namespace Database\Seeders;

use App\Enums\VillageType;
use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class VillageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districtMap = District::pluck('id', 'code');

        $villages = [
            // Kecamatan Trenggalek (35.03.01)
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.1001',
                'name' => 'Sumbergedong',
                'type' => VillageType::UrbanVillage,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.1002',
                'name' => 'Ngantru',
                'type' => VillageType::UrbanVillage,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.1003',
                'name' => 'Surodakan',
                'type' => VillageType::UrbanVillage,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.1004',
                'name' => 'Kelutan',
                'type' => VillageType::UrbanVillage,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.1005',
                'name' => 'Tamanan',
                'type' => VillageType::UrbanVillage,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.2006',
                'name' => 'Ngares',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.2007',
                'name' => 'Rejowinangun',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.2008',
                'name' => 'Sambirejo',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.2009',
                'name' => 'Sukorejo',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.01',
                'code' => '35.03.01.2010',
                'name' => 'Dawuhan',
                'type' => VillageType::Village,
                'is_active' => true,
            ],

            // Kecamatan Pogalan (35.03.02)
            [
                'district_code' => '35.03.02',
                'code' => '35.03.02.2001',
                'name' => 'Pogalan',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.02',
                'code' => '35.03.02.2002',
                'name' => 'Bendorejo',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.02',
                'code' => '35.03.02.2003',
                'name' => 'Ngetal',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.02',
                'code' => '35.03.02.2004',
                'name' => 'Kedungsigit',
                'type' => VillageType::Village,
                'is_active' => true,
            ],

            // Kecamatan Durenan (35.03.03)
            [
                'district_code' => '35.03.03',
                'code' => '35.03.03.2001',
                'name' => 'Durenan',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.03',
                'code' => '35.03.03.2002',
                'name' => 'Kamulan',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.03',
                'code' => '35.03.03.2003',
                'name' => 'Kendalrejo',
                'type' => VillageType::Village,
                'is_active' => true,
            ],

            // Kecamatan Karangan (35.03.05)
            [
                'district_code' => '35.03.05',
                'code' => '35.03.05.2001',
                'name' => 'Karangan',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.05',
                'code' => '35.03.05.2002',
                'name' => 'Kerjo',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.05',
                'code' => '35.03.05.2003',
                'name' => 'Sumber',
                'type' => VillageType::Village,
                'is_active' => true,
            ],

            // Kecamatan Tugu (35.03.06)
            [
                'district_code' => '35.03.06',
                'code' => '35.03.06.2001',
                'name' => 'Nglongsor',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.06',
                'code' => '35.03.06.2002',
                'name' => 'Dermosari',
                'type' => VillageType::Village,
                'is_active' => true,
            ],

            // Kecamatan Bendungan (35.03.07)
            [
                'district_code' => '35.03.07',
                'code' => '35.03.07.2001',
                'name' => 'Srabah',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.07',
                'code' => '35.03.07.2002',
                'name' => 'Masaran',
                'type' => VillageType::Village,
                'is_active' => true,
            ],

            // Kecamatan Watulimo (35.03.09)
            [
                'district_code' => '35.03.09',
                'code' => '35.03.09.2001',
                'name' => 'Prigi',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
            [
                'district_code' => '35.03.09',
                'code' => '35.03.09.2002',
                'name' => 'Tasikmadu',
                'type' => VillageType::Village,
                'is_active' => true,
            ],
        ];

        foreach ($villages as $villageData) {
            $districtId = $districtMap[$villageData['district_code']] ?? null;

            if ($districtId) {
                Village::updateOrCreate(
                    ['code' => $villageData['code']],
                    [
                        'district_id' => $districtId,
                        'name' => $villageData['name'],
                        'type' => $villageData['type'],
                        'is_active' => $villageData['is_active'],
                    ]
                );
            }
        }
    }
}
