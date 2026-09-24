<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $districts = [
            ['code' => '35.03.01', 'name' => 'Trenggalek', 'is_active' => true],
            ['code' => '35.03.02', 'name' => 'Pogalan', 'is_active' => true],
            ['code' => '35.03.03', 'name' => 'Durenan', 'is_active' => true],
            ['code' => '35.03.04', 'name' => 'Gandusari', 'is_active' => true],
            ['code' => '35.03.05', 'name' => 'Karangan', 'is_active' => true],
            ['code' => '35.03.06', 'name' => 'Tugu', 'is_active' => true],
            ['code' => '35.03.07', 'name' => 'Bendungan', 'is_active' => true],
            ['code' => '35.03.08', 'name' => 'Kampak', 'is_active' => true],
            ['code' => '35.03.09', 'name' => 'Watulimo', 'is_active' => true],
            ['code' => '35.03.10', 'name' => 'Munjungan', 'is_active' => true],
            ['code' => '35.03.11', 'name' => 'Panggul', 'is_active' => true],
            ['code' => '35.03.12', 'name' => 'Dongko', 'is_active' => true],
            ['code' => '35.03.13', 'name' => 'Pule', 'is_active' => true],
            ['code' => '35.03.14', 'name' => 'Suruh', 'is_active' => true],
        ];

        foreach ($districts as $district) {
            District::updateOrCreate(
                ['code' => $district['code']],
                $district
            );
        }
    }
}
