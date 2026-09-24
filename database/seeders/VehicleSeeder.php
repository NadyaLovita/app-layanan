<?php

namespace Database\Seeders;

use App\Enums\VehicleOperationalStatus;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'plate_number' => 'AG 8001 YP',
                'type' => 'Dump Truck Isuzu Giga',
                'capacity' => 8.00,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Active,
                'notes' => 'Armada pengangkut rute utama perkotaan & sentra pasar',
            ],
            [
                'plate_number' => 'AG 8002 YP',
                'type' => 'Dump Truck Hino Dutro',
                'capacity' => 6.00,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Active,
                'notes' => 'Armada rit perkotaan, fasilitas umum, dan instansi kedinasan',
            ],
            [
                'plate_number' => 'AG 8003 YP',
                'type' => 'Arm Roll Truck Mitsubishi Canter',
                'capacity' => 6.00,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Active,
                'notes' => 'Armada pengangkut kontainer TPS Alun-Alun dan Pasar Sore',
            ],
            [
                'plate_number' => 'AG 8004 YP',
                'type' => 'Arm Roll Truck Mitsubishi Canter',
                'capacity' => 6.00,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Active,
                'notes' => 'Armada pengangkut kontainer Pasar Basah dan Terminal Surodakan',
            ],
            [
                'plate_number' => 'AG 8005 YP',
                'type' => 'Compactor Truck Isuzu Elf',
                'capacity' => 10.00,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Active,
                'notes' => 'Armada truk pemadat sampah rute pertokoan dan jalan protokol perkotaan',
            ],
            [
                'plate_number' => 'AG 8006 YP',
                'type' => 'Dump Truck Isuzu Elf',
                'capacity' => 5.00,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Damaged,
                'notes' => 'Kerusakan transmisi dan hidrolik, dalam proses perbaikan di bengkel rekanan dinas',
            ],
            [
                'plate_number' => 'AG 8007 YP',
                'type' => 'Motor Roda Tiga Viar Karya',
                'capacity' => 1.50,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Active,
                'notes' => 'Armada pengangkut lorong gang sempit & perumahan padat Kelutan - Sumbergedong',
            ],
            [
                'plate_number' => 'AG 8008 YP',
                'type' => 'Motor Roda Tiga Tossa',
                'capacity' => 1.50,
                'capacity_unit' => 'm3',
                'operational_status' => VehicleOperationalStatus::Inactive,
                'notes' => 'Kondisi rusak berat aus usia pakai, usulan lelang penghapusan aset daerah',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                $vehicle
            );
        }
    }
}
