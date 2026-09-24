<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userMap = User::pluck('id', 'username');

        $drivers = [
            [
                'username' => 'supriyadi',
                'name' => 'Supriyadi',
                'phone' => '081234567011',
                'is_active' => true,
            ],
            [
                'username' => 'bambang',
                'name' => 'Bambang Hermanto',
                'phone' => '081234567012',
                'is_active' => true,
            ],
            [
                'username' => 'sugeng',
                'name' => 'Sugeng Riyadi',
                'phone' => '081234567013',
                'is_active' => true,
            ],
            [
                'username' => 'totok',
                'name' => 'Totok Prasetyo',
                'phone' => '081234567014',
                'is_active' => true,
            ],
            [
                'username' => null,
                'name' => 'Joko Widodo',
                'phone' => '081234567015',
                'is_active' => true,
            ],
            [
                'username' => null,
                'name' => 'Slamet Raharjo',
                'phone' => '081234567016',
                'is_active' => false,
            ],
        ];

        foreach ($drivers as $driverData) {
            $userId = $driverData['username'] ? ($userMap[$driverData['username']] ?? null) : null;

            Driver::updateOrCreate(
                ['name' => $driverData['name']],
                [
                    'user_id' => $userId,
                    'phone' => $driverData['phone'],
                    'is_active' => $driverData['is_active'],
                ]
            );
        }
    }
}
