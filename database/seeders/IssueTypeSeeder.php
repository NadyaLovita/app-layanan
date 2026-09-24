<?php

namespace Database\Seeders;

use App\Models\IssueType;
use Illuminate\Database\Seeder;

class IssueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $issueTypes = [
            'Armada Rusak / Mesin Mogok',
            'Ban Bocor / Pecah Ban',
            'Masalah Sistem Hidrolik / Arm Roll / Dump',
            'Akses Menuju Lokasi Tertutup / Terhalang',
            'Jalan Ditutup / Pengalihan Arus (Acara Warga / Perbaikan)',
            'Volume Sampah Meluap (Overload Kapasitas)',
            'Cuaca Buruk / Hujan Deras / Genangan Air',
            'Antrean Bongkar Padat di TPA Srabah',
            'Kecelakaan / Insiden Lalu Lintas Ringan',
        ];

        foreach ($issueTypes as $name) {
            IssueType::updateOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );
        }
    }
}
