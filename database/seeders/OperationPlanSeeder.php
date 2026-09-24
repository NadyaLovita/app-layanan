<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\OperationPlan;
use App\Models\OperationPlanArea;
use App\Models\ServiceArea;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class OperationPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleMap = Vehicle::pluck('id', 'plate_number');
        $driverMap = Driver::pluck('id', 'name');
        $userMap = User::pluck('id', 'username');
        $serviceAreaMap = ServiceArea::pluck('id', 'name');

        $plans = [
            // Plan 1: Kemarin (selesai terlaksana)
            [
                'plan_date' => today()->subDay()->toDateString(),
                'plate_number' => 'AG 8001 YP',
                'driver_name' => 'Supriyadi',
                'username' => 'petugas',
                'notes' => 'Rute pengangkutan rit pagi sentra pasar dan alun-alun kota',
                'areas' => [
                    'Area Alun-Alun & Pendopo Trenggalek',
                    'Area Pasar Sore & Pertokoan Sudirman',
                    'Area Pasar Basah Trenggalek',
                ],
            ],

            // Plan 2: Hari ini (sedang berjalan)
            [
                'plan_date' => today()->toDateString(),
                'plate_number' => 'AG 8003 YP',
                'driver_name' => 'Bambang Hermanto',
                'username' => 'petugas',
                'notes' => 'Pengangkutan kontainer arm roll kawasan pasar basah dan terminal bus',
                'areas' => [
                    'Area Pasar Basah Trenggalek',
                    'Area Terminal Bus Surodakan - Ngantru',
                ],
            ],

            // Plan 3: Hari ini (rencana menunggu pemberangkatan)
            [
                'plan_date' => today()->toDateString(),
                'plate_number' => 'AG 8005 YP',
                'driver_name' => 'Totok Prasetyo',
                'username' => 'operator',
                'notes' => 'Rit compactor truck jalur pertokoan protokol dan perkantoran',
                'areas' => [
                    'Area Sentra Industri Keripik Tempe Tamanan',
                    'Area Kompleks Perkantoran Pemkab',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $vehicleId = $vehicleMap[$planData['plate_number']] ?? null;
            $driverId = $driverMap[$planData['driver_name']] ?? null;
            $userId = $userMap[$planData['username']] ?? null;

            if ($vehicleId && $driverId && $userId) {
                $plan = OperationPlan::updateOrCreate(
                    [
                        'plan_date' => $planData['plan_date'],
                        'vehicle_id' => $vehicleId,
                        'driver_id' => $driverId,
                    ],
                    [
                        'notes' => $planData['notes'],
                        'created_by' => $userId,
                    ]
                );

                // Sinkronisasi area rencana
                OperationPlanArea::where('operation_plan_id', $plan->id)->delete();

                foreach ($planData['areas'] as $index => $areaName) {
                    $serviceAreaId = $serviceAreaMap[$areaName] ?? null;

                    if ($serviceAreaId) {
                        OperationPlanArea::create([
                            'operation_plan_id' => $plan->id,
                            'service_area_id' => $serviceAreaId,
                            'sequence' => $index + 1,
                        ]);
                    }
                }
            }
        }
    }
}
