<?php

namespace Tests\Feature;

use App\Enums\ActivityType;
use App\Enums\ConformityStatus;
use App\Enums\FollowUpStatus;
use App\Enums\LocationType;
use App\Enums\RealizationStatus;
use App\Enums\UserRole;
use App\Enums\ValidationResult;
use App\Enums\ValidationStatus;
use App\Enums\VehicleOperationalStatus;
use App\Enums\VillageType;
use App\Enums\VolumeUnit;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\District;
use App\Models\Driver;
use App\Models\IssueType;
use App\Models\Location;
use App\Models\OperationalIssue;
use App\Models\OperationPlan;
use App\Models\OperationPlanArea;
use App\Models\ServiceArea;
use App\Models\ServiceRealization;
use App\Models\ServiceRealizationArea;
use App\Models\ServiceRealizationValidation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SipampahSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_sipampah_tables_exist(): void
    {
        $tables = [
            'users',
            'districts',
            'villages',
            'service_areas',
            'locations',
            'vehicles',
            'drivers',
            'issue_types',
            'operation_plans',
            'operation_plan_areas',
            'service_realizations',
            'service_realization_areas',
            'operational_issues',
            'attachments',
            'service_realization_validations',
            'audit_logs',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} should exist");
        }
    }

    public function test_models_relationships_and_enums(): void
    {
        // 1. User
        $user = User::create([
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@trenggalekkab.go.id',
            'password' => bcrypt('password'),
            'phone' => '081234567890',
            'role' => UserRole::Officer,
            'is_active' => true,
        ]);
        $this->assertEquals(UserRole::Officer, $user->role);
        $this->assertTrue($user->isOfficer());

        // 2. District
        $district = District::create([
            'code' => '35.03.01',
            'name' => 'Trenggalek',
            'is_active' => true,
        ]);

        // 3. Village
        $village = Village::create([
            'district_id' => $district->id,
            'code' => '35.03.01.1001',
            'name' => 'Sumbergedong',
            'type' => VillageType::UrbanVillage,
            'is_active' => true,
        ]);
        $this->assertEquals($district->id, $village->district->id);

        // 4. Service Area
        $serviceArea = ServiceArea::create([
            'village_id' => $village->id,
            'name' => 'Pasar Sore',
            'description' => 'Area sekitar pasar sore Sumbergedong',
            'is_active' => true,
        ]);
        $this->assertEquals($village->id, $serviceArea->village->id);

        // 5. Location
        $location = Location::create([
            'service_area_id' => $serviceArea->id,
            'name' => 'TPS Pasar Sore',
            'type' => LocationType::Tps,
            'address' => 'Jl. Panglima Sudirman',
            'latitude' => -8.05123456,
            'longitude' => 111.71123456,
            'is_active' => true,
        ]);
        $this->assertEquals(LocationType::Tps, $location->type);

        // Final dump location (TPA)
        $tpa = Location::create([
            'service_area_id' => $serviceArea->id,
            'name' => 'TPA Srabah',
            'type' => LocationType::Tpa,
            'is_active' => true,
        ]);

        // 6. Vehicle
        $vehicle = Vehicle::create([
            'plate_number' => 'AG 8001 YP',
            'type' => 'Dump Truck',
            'capacity' => 6.00,
            'capacity_unit' => 'm3',
            'operational_status' => VehicleOperationalStatus::Active,
        ]);
        $this->assertEquals(VehicleOperationalStatus::Active, $vehicle->operational_status);

        // 7. Driver
        $driver = Driver::create([
            'user_id' => $user->id,
            'name' => 'Supriyadi',
            'phone' => '082134567891',
            'is_active' => true,
        ]);
        $this->assertEquals($user->id, $driver->user->id);

        // 8. Issue Type
        $issueType = IssueType::create([
            'name' => 'Armada Rusak',
            'is_active' => true,
        ]);

        // 9. Operation Plan
        $plan = OperationPlan::create([
            'plan_date' => now()->toDateString(),
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'notes' => 'Rencana rit pagi',
            'created_by' => $user->id,
        ]);
        $this->assertEquals($vehicle->id, $plan->vehicle->id);

        // 10. Operation Plan Area
        $planArea = OperationPlanArea::create([
            'operation_plan_id' => $plan->id,
            'service_area_id' => $serviceArea->id,
            'sequence' => 1,
        ]);
        $this->assertEquals($serviceArea->id, $planArea->serviceArea->id);

        // 11. Service Realization
        $realization = ServiceRealization::create([
            'operation_plan_id' => $plan->id,
            'activity_type' => ActivityType::Planned,
            'activity_date' => now()->toDateString(),
            'started_at' => now()->subHours(2),
            'finished_at' => now(),
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => RealizationStatus::Completed,
            'total_volume' => 5.50,
            'volume_unit' => VolumeUnit::M3,
            'final_location_id' => $tpa->id,
            'conformity_status' => ConformityStatus::AsPlanned,
            'validation_status' => ValidationStatus::Pending,
            'created_by' => $user->id,
        ]);
        $this->assertTrue($realization->isPlanned());
        $this->assertTrue($realization->isCompleted());
        $this->assertEquals($tpa->id, $realization->finalLocation->id);

        // 12. Service Realization Area
        $realizationArea = ServiceRealizationArea::create([
            'service_realization_id' => $realization->id,
            'service_area_id' => $serviceArea->id,
            'location_id' => $location->id,
            'sequence' => 1,
            'volume' => 5.50,
        ]);
        $this->assertEquals($location->id, $realizationArea->location->id);

        // 13. Operational Issue
        $issue = OperationalIssue::create([
            'service_realization_id' => $realization->id,
            'issue_type_id' => $issueType->id,
            'description' => 'Ban bocor di sekitar jalan protokol',
            'occurred_at' => now()->subHour(),
            'follow_up_status' => FollowUpStatus::Open,
            'created_by' => $user->id,
        ]);
        $this->assertEquals($realization->id, $issue->serviceRealization->id);

        // 14. Attachment
        $attachment = Attachment::create([
            'service_realization_id' => $realization->id,
            'operational_issue_id' => $issue->id,
            'file_path' => 'attachments/2026/ban_bocor.jpg',
            'caption' => 'Foto ban kempes',
            'uploaded_by' => $user->id,
        ]);
        $this->assertEquals($realization->id, $attachment->serviceRealization->id);

        // 15. Service Realization Validation
        $validation = ServiceRealizationValidation::create([
            'service_realization_id' => $realization->id,
            'validated_by' => $user->id,
            'result' => ValidationResult::Valid,
            'notes' => 'Selesai dan sesuai',
            'validated_at' => now(),
        ]);
        $this->assertTrue($validation->isValid());
        $this->assertEquals($validation->id, $realization->fresh()->latestValidation->id);

        // 16. Audit Log
        $auditLog = AuditLog::create([
            'user_id' => $user->id,
            'event' => 'created',
            'auditable_type' => ServiceRealization::class,
            'auditable_id' => $realization->id,
            'new_values' => ['status' => 'completed'],
            'ip_address' => '127.0.0.1',
        ]);
        $this->assertEquals(ServiceRealization::class, $auditLog->auditable_type);
        $this->assertEquals($realization->id, $auditLog->auditable->id);
    }
}
