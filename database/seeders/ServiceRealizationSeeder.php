<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Enums\ConformityStatus;
use App\Enums\FollowUpStatus;
use App\Enums\RealizationStatus;
use App\Enums\ValidationResult;
use App\Enums\ValidationStatus;
use App\Enums\VolumeUnit;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\Driver;
use App\Models\IssueType;
use App\Models\Location;
use App\Models\OperationalIssue;
use App\Models\OperationPlan;
use App\Models\ServiceArea;
use App\Models\ServiceRealization;
use App\Models\ServiceRealizationArea;
use App\Models\ServiceRealizationValidation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class ServiceRealizationSeeder extends Seeder
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
        $locationMap = Location::pluck('id', 'name');
        $issueTypeMap = IssueType::pluck('id', 'name');

        $tpaLocationId = $locationMap['TPA Srabah'] ?? null;
        $tpstKelutanLocationId = $locationMap['TPST Kelutan Mandiri (TPS3R)'] ?? null;

        // Ambil plan yang sudah di-seed
        $planYesterday = OperationPlan::where('plan_date', today()->subDay()->toDateString())
            ->where('vehicle_id', $vehicleMap['AG 8001 YP'] ?? null)
            ->first();

        $planTodayRunning = OperationPlan::where('plan_date', today()->toDateString())
            ->where('vehicle_id', $vehicleMap['AG 8003 YP'] ?? null)
            ->first();

        $planTodayPlanned = OperationPlan::where('plan_date', today()->toDateString())
            ->where('vehicle_id', $vehicleMap['AG 8005 YP'] ?? null)
            ->first();

        // =========================================================================
        // SKENARIO 1: Terencana & Sesuai Rencana (Completed, As Planned, Valid)
        // =========================================================================
        if ($planYesterday) {
            $realization1 = ServiceRealization::updateOrCreate(
                [
                    'operation_plan_id' => $planYesterday->id,
                    'activity_date' => today()->subDay()->toDateString(),
                    'vehicle_id' => $vehicleMap['AG 8001 YP'],
                ],
                [
                    'activity_type' => ActivityType::Planned,
                    'started_at' => today()->subDay()->setTime(6, 15),
                    'finished_at' => today()->subDay()->setTime(9, 45),
                    'driver_id' => $driverMap['Supriyadi'],
                    'status' => RealizationStatus::Completed,
                    'total_volume' => 7.20,
                    'volume_unit' => VolumeUnit::M3,
                    'final_location_id' => $tpaLocationId,
                    'field_condition' => 'Cuaca cerah berawan, sirkulasi lalu lintas protokol pagi lancar.',
                    'conformity_status' => ConformityStatus::AsPlanned,
                    'change_reason' => null,
                    'validation_status' => ValidationStatus::Valid,
                    'created_by' => $userMap['petugas'],
                    'updated_by' => $userMap['koordinator'],
                ]
            );

            // Rute aktual
            ServiceRealizationArea::where('service_realization_id', $realization1->id)->delete();
            ServiceRealizationArea::create([
                'service_realization_id' => $realization1->id,
                'service_area_id' => $serviceAreaMap['Area Alun-Alun & Pendopo Trenggalek'],
                'location_id' => $locationMap['TPS Alun-Alun Trenggalek'] ?? null,
                'sequence' => 1,
                'arrived_at' => today()->subDay()->setTime(6, 30),
                'volume' => 2.20,
                'notes' => 'Pengangkutan tumpukan sampah taman dan alun-alun kota.',
            ]);
            ServiceRealizationArea::create([
                'service_realization_id' => $realization1->id,
                'service_area_id' => $serviceAreaMap['Area Pasar Sore & Pertokoan Sudirman'],
                'location_id' => $locationMap['TPS Pasar Sore Trenggalek'] ?? null,
                'sequence' => 2,
                'arrived_at' => today()->subDay()->setTime(7, 35),
                'volume' => 2.40,
                'notes' => 'Pengosongan bak TPS Pasar Sore lancar.',
            ]);
            ServiceRealizationArea::create([
                'service_realization_id' => $realization1->id,
                'service_area_id' => $serviceAreaMap['Area Pasar Basah Trenggalek'],
                'location_id' => $locationMap['TPS Pasar Basah Trenggalek'] ?? null,
                'sequence' => 3,
                'arrived_at' => today()->subDay()->setTime(8, 40),
                'volume' => 2.60,
                'notes' => 'Pintu loading dock pasar bersih, langsung bergerak ke TPA Srabah.',
            ]);

            // Validasi Koordinator
            ServiceRealizationValidation::updateOrCreate(
                ['service_realization_id' => $realization1->id],
                [
                    'validated_by' => $userMap['koordinator'],
                    'result' => ValidationResult::Valid,
                    'notes' => 'Realisasi pelayanan tuntas sesuai rute rencana, volume masuk TPA Srabah tercatat tertib.',
                    'validated_at' => today()->subDay()->setTime(11, 0),
                ]
            );

            // Audit Trail
            AuditLog::updateOrCreate(
                [
                    'auditable_type' => ServiceRealization::class,
                    'auditable_id' => $realization1->id,
                    'event' => 'created',
                ],
                [
                    'user_id' => $userMap['petugas'],
                    'new_values' => [
                        'status' => 'completed',
                        'total_volume' => 7.20,
                        'conformity_status' => 'as_planned',
                    ],
                    'ip_address' => '127.0.0.1',
                ]
            );
        }

        // =========================================================================
        // SKENARIO 2: Terencana Berubah dari Rencana (Completed, Changed, Valid)
        // =========================================================================
        $realization2 = ServiceRealization::updateOrCreate(
            [
                'operation_plan_id' => null,
                'activity_date' => today()->subDay()->toDateString(),
                'vehicle_id' => $vehicleMap['AG 8004 YP'],
                'activity_type' => ActivityType::Planned,
            ],
            [
                'started_at' => today()->subDay()->setTime(13, 0),
                'finished_at' => today()->subDay()->setTime(16, 30),
                'driver_id' => $driverMap['Bambang Hermanto'],
                'status' => RealizationStatus::Completed,
                'total_volume' => 6.00,
                'volume_unit' => VolumeUnit::M3,
                'final_location_id' => $tpaLocationId,
                'field_condition' => 'Ada penambahan rit darurat akibat giat kebersihan pasca acara kedinasan.',
                'conformity_status' => ConformityStatus::Changed,
                'change_reason' => 'Pengalihan dan penambahan titik angkut di Area Kompleks Perkantoran Pemkab karena permintaan darurat pembersihan pasca upacara daerah.',
                'validation_status' => ValidationStatus::Valid,
                'created_by' => $userMap['petugas'],
                'updated_by' => $userMap['koordinator'],
            ]
        );

        ServiceRealizationArea::where('service_realization_id', $realization2->id)->delete();
        ServiceRealizationArea::create([
            'service_realization_id' => $realization2->id,
            'service_area_id' => $serviceAreaMap['Area Pasar Basah Trenggalek'],
            'location_id' => $locationMap['TPS Pasar Basah Trenggalek'] ?? null,
            'sequence' => 1,
            'arrived_at' => today()->subDay()->setTime(13, 20),
            'volume' => 3.50,
            'notes' => 'Rit pertama pasar basah.',
        ]);
        ServiceRealizationArea::create([
            'service_realization_id' => $realization2->id,
            'service_area_id' => $serviceAreaMap['Area Kompleks Perkantoran Pemkab'],
            'location_id' => $locationMap['Titik Jemput RSUD dr. Soedomo'] ?? null,
            'sequence' => 2,
            'arrived_at' => today()->subDay()->setTime(14, 50),
            'volume' => 2.50,
            'notes' => 'Titik tambahan sesuai disposisi pengawas lapangan.',
        ]);

        ServiceRealizationValidation::updateOrCreate(
            ['service_realization_id' => $realization2->id],
            [
                'validated_by' => $userMap['koordinator'],
                'result' => ValidationResult::Valid,
                'notes' => 'Perubahan rute disetujui. Bukti pengangkutan darurat terkonfirmasi.',
                'validated_at' => today()->subDay()->setTime(17, 15),
            ]
        );

        // =========================================================================
        // SKENARIO 3: Layanan Insidental / Aktual (Incidental, Completed, Unplanned, Valid)
        // =========================================================================
        $realization3 = ServiceRealization::updateOrCreate(
            [
                'operation_plan_id' => null,
                'activity_date' => today()->subDay()->toDateString(),
                'vehicle_id' => $vehicleMap['AG 8007 YP'],
                'activity_type' => ActivityType::Incidental,
            ],
            [
                'started_at' => today()->subDay()->setTime(14, 0),
                'finished_at' => today()->subDay()->setTime(16, 0),
                'driver_id' => $driverMap['Sugeng Riyadi'],
                'status' => RealizationStatus::Completed,
                'total_volume' => 1.30,
                'volume_unit' => VolumeUnit::M3,
                'final_location_id' => $tpstKelutanLocationId,
                'field_condition' => 'Pembersihan tumpukan dahan ranting dan sampah dedaunan kering hasil kerja bakti warga.',
                'conformity_status' => ConformityStatus::Unplanned,
                'change_reason' => null,
                'validation_status' => ValidationStatus::Valid,
                'created_by' => $userMap['operator'],
                'updated_by' => $userMap['koordinator'],
            ]
        );

        ServiceRealizationArea::where('service_realization_id', $realization3->id)->delete();
        ServiceRealizationArea::create([
            'service_realization_id' => $realization3->id,
            'service_area_id' => $serviceAreaMap['Area Pemukiman Kelutan Permai & Stadion Menak Sopal'],
            'location_id' => null,
            'sequence' => 1,
            'arrived_at' => today()->subDay()->setTime(14, 20),
            'volume' => 1.30,
            'notes' => 'Lorong pemukiman padat Kelutan Permai.',
        ]);

        ServiceRealizationValidation::updateOrCreate(
            ['service_realization_id' => $realization3->id],
            [
                'validated_by' => $userMap['koordinator'],
                'result' => ValidationResult::Valid,
                'notes' => 'Layanan insidental permohonan warga telah selesai diangkut ke TPST Kelutan.',
                'validated_at' => today()->subDay()->setTime(17, 30),
            ]
        );

        // =========================================================================
        // SKENARIO 4: Layanan Terkendala (Obstructed + Operational Issue + Attachment)
        // =========================================================================
        $realization4 = ServiceRealization::updateOrCreate(
            [
                'activity_date' => today()->toDateString(),
                'vehicle_id' => $vehicleMap['AG 8002 YP'],
                'status' => RealizationStatus::Obstructed,
            ],
            [
                'operation_plan_id' => null,
                'activity_type' => ActivityType::Incidental,
                'started_at' => today()->setTime(7, 30),
                'finished_at' => null,
                'driver_id' => $driverMap['Joko Widodo'],
                'total_volume' => 2.00,
                'volume_unit' => VolumeUnit::M3,
                'final_location_id' => null,
                'field_condition' => 'Armada berhenti darurat di tepi jalan Karangan akibat ban belakang bocor tertusuk potongan besi cor proyek gorong-gorong.',
                'conformity_status' => ConformityStatus::Unplanned,
                'change_reason' => null,
                'validation_status' => ValidationStatus::Pending,
                'created_by' => $userMap['operator'],
                'updated_by' => null,
            ]
        );

        ServiceRealizationArea::where('service_realization_id', $realization4->id)->delete();
        ServiceRealizationArea::create([
            'service_realization_id' => $realization4->id,
            'service_area_id' => $serviceAreaMap['Area Pasar Hewan & Pasar Rakyat Karangan'],
            'location_id' => $locationMap['TPS Pasar Rakyat Karangan'] ?? null,
            'sequence' => 1,
            'arrived_at' => today()->setTime(8, 0),
            'volume' => 2.00,
            'notes' => 'Pengangkutan bak TPS Pasar Karangan baru separuh terangkut sebelum kendaraan terkendala.',
        ]);

        // Catatan kendala operasional (wajib ada untuk status obstructed sesuai PRD)
        $issue = OperationalIssue::updateOrCreate(
            [
                'service_realization_id' => $realization4->id,
                'issue_type_id' => $issueTypeMap['Ban Bocor / Pecah Ban'],
            ],
            [
                'description' => 'Ban belakang kanan kempes total terkena besi angkur proyek perbaikan drainase jalan Karangan, armada tidak aman dipaksakan jalan.',
                'occurred_at' => today()->setTime(8, 25),
                'follow_up' => 'Menghubungi tim bengkel dinas DLH untuk membawa ban serep dan dongkrak hidrolik ke lokasi.',
                'follow_up_status' => FollowUpStatus::InProgress,
                'created_by' => $userMap['operator'],
            ]
        );

        // Lampiran / Attachment foto kendala
        Attachment::updateOrCreate(
            [
                'service_realization_id' => $realization4->id,
                'operational_issue_id' => $issue->id,
            ],
            [
                'file_path' => 'attachments/seed/kendala_ban_ag8002yp.jpg',
                'caption' => 'Kondisi ban belakang AG 8002 YP kempes di tepi jalan Karangan.',
                'uploaded_by' => $userMap['operator'],
            ]
        );

        // =========================================================================
        // SKENARIO 5: Sedang Berjalan Hari Ini (Running, Pending)
        // =========================================================================
        if ($planTodayRunning) {
            $realization5 = ServiceRealization::updateOrCreate(
                [
                    'operation_plan_id' => $planTodayRunning->id,
                    'activity_date' => today()->toDateString(),
                    'vehicle_id' => $vehicleMap['AG 8003 YP'],
                ],
                [
                    'activity_type' => ActivityType::Planned,
                    'started_at' => today()->setTime(8, 0),
                    'finished_at' => null,
                    'driver_id' => $driverMap['Bambang Hermanto'],
                    'status' => RealizationStatus::Running,
                    'total_volume' => null,
                    'volume_unit' => VolumeUnit::M3,
                    'final_location_id' => null,
                    'field_condition' => 'Operasional pagi sedang berlangsung, armada bergerak ke titik kedua.',
                    'conformity_status' => ConformityStatus::AsPlanned,
                    'change_reason' => null,
                    'validation_status' => ValidationStatus::Pending,
                    'created_by' => $userMap['petugas'],
                ]
            );

            ServiceRealizationArea::where('service_realization_id', $realization5->id)->delete();
            ServiceRealizationArea::create([
                'service_realization_id' => $realization5->id,
                'service_area_id' => $serviceAreaMap['Area Pasar Basah Trenggalek'],
                'location_id' => $locationMap['TPS Pasar Basah Trenggalek'] ?? null,
                'sequence' => 1,
                'arrived_at' => today()->setTime(8, 20),
                'volume' => 3.20,
                'notes' => 'Kontainer Pasar Basah telah dinaikkan ke arm roll.',
            ]);
            ServiceRealizationArea::create([
                'service_realization_id' => $realization5->id,
                'service_area_id' => $serviceAreaMap['Area Terminal Bus Surodakan - Ngantru'],
                'location_id' => $locationMap['TPS Terminal Bus Surodakan'] ?? null,
                'sequence' => 2,
                'arrived_at' => null,
                'volume' => null,
                'notes' => 'Sedang menuju lokasi terminal.',
            ]);
        }

        // =========================================================================
        // SKENARIO 6: Rencana Hari Ini Belum Mulai (Planned, Pending)
        // =========================================================================
        if ($planTodayPlanned) {
            ServiceRealization::updateOrCreate(
                [
                    'operation_plan_id' => $planTodayPlanned->id,
                    'activity_date' => today()->toDateString(),
                    'vehicle_id' => $vehicleMap['AG 8005 YP'],
                ],
                [
                    'activity_type' => ActivityType::Planned,
                    'started_at' => null,
                    'finished_at' => null,
                    'driver_id' => $driverMap['Totok Prasetyo'],
                    'status' => RealizationStatus::Planned,
                    'total_volume' => null,
                    'volume_unit' => VolumeUnit::M3,
                    'final_location_id' => null,
                    'field_condition' => null,
                    'conformity_status' => ConformityStatus::AsPlanned,
                    'change_reason' => null,
                    'validation_status' => ValidationStatus::Pending,
                    'created_by' => $userMap['operator'],
                ]
            );
        }
    }
}
