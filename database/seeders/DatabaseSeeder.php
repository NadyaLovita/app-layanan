<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DistrictSeeder::class,
            VillageSeeder::class,
            ServiceAreaSeeder::class,
            LocationSeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            IssueTypeSeeder::class,
            OperationPlanSeeder::class,
            ServiceRealizationSeeder::class,
        ]);
    }
}
