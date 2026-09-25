<?php

namespace Tests\Feature;

use App\Enums\ActivityType;
use App\Enums\ConformityStatus;
use App\Enums\FollowUpStatus;
use App\Enums\LocationType;
use App\Enums\RealizationStatus;
use App\Enums\UserRole;
use App\Enums\ValidationStatus;
use App\Enums\VehicleOperationalStatus;
use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\District;
use App\Models\Driver;
use App\Models\Location;
use App\Models\OperationPlan;
use App\Models\ServiceArea;
use App\Models\ServiceRealization;
use App\Models\ServiceRealizationValidation;
use App\Models\User;
use App\Models\Village;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SipampahSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_and_populates_all_sipampah_data(): void
    {
        // Jalankan DatabaseSeeder
        $this->seed(DatabaseSeeder::class);

        // 1. Verifikasi Users & Roles
        $this->assertDatabaseHas('users', [
            'username' => 'admin',
            'role' => UserRole::Admin->value,
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'petugas',
            'role' => UserRole::Officer->value,
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'koordinator',
            'role' => UserRole::Coordinator->value,
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'pimpinan',
            'role' => UserRole::Head->value,
        ]);
        $this->assertDatabaseHas('users', [
            'username' => 'supriyadi',
            'role' => UserRole::Driver->value,
        ]);

        // 2. Verifikasi Districts (14 Kecamatan Trenggalek)
        $this->assertCount(14, District::all());
        $this->assertDatabaseHas('districts', ['code' => '35.03.01', 'name' => 'Trenggalek']);
        $this->assertDatabaseHas('districts', ['code' => '35.03.07', 'name' => 'Bendungan']);

        // 3. Verifikasi Villages
        $this->assertTrue(Village::where('name', 'Sumbergedong')->exists());
        $this->assertTrue(Village::where('name', 'Srabah')->exists());

        // 4. Verifikasi Service Areas
        $this->assertTrue(ServiceArea::where('name', 'Zona Operasional Pemrosesan TPA Srabah')->exists());
        $this->assertTrue(ServiceArea::where('name', 'Area Alun-Alun & Pendopo Trenggalek')->exists());

        // 5. Verifikasi Locations
        $tpa = Location::where('name', 'TPA Srabah')->first();
        $this->assertNotNull($tpa);
        $this->assertEquals(LocationType::Tpa, $tpa->type);

        $tpst = Location::where('type', LocationType::Tpst->value)->first();
        $this->assertNotNull($tpst);

        $tps = Location::where('type', LocationType::Tps->value)->first();
        $this->assertNotNull($tps);

        // 6. Verifikasi Vehicles
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AG 8001 YP',
            'operational_status' => VehicleOperationalStatus::Active->value,
        ]);
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AG 8006 YP',
            'operational_status' => VehicleOperationalStatus::Damaged->value,
        ]);
        $this->assertDatabaseHas('vehicles', [
            'plate_number' => 'AG 8008 YP',
            'operational_status' => VehicleOperationalStatus::Inactive->value,
        ]);

        // 7. Verifikasi Drivers & Relasi User
        $driverSupriyadi = Driver::where('name', 'Supriyadi')->first();
        $this->assertNotNull($driverSupriyadi);
        $this->assertNotNull($driverSupriyadi->user);
        $this->assertEquals('supriyadi', $driverSupriyadi->user->username);

        // Driver lepas tanpa akun user
        $driverJoko = Driver::where('name', 'Joko Widodo')->first();
        $this->assertNotNull($driverJoko);
        $this->assertNull($driverJoko->user_id);

        // 8. Verifikasi Issue Types
        $this->assertDatabaseHas('issue_types', ['name' => 'Ban Bocor / Pecah Ban']);
        $this->assertDatabaseHas('issue_types', ['name' => 'Armada Rusak / Mesin Mogok']);

        // 9. Verifikasi Operation Plans & Area Sequence
        $plan = OperationPlan::with('planAreas')->first();
        $this->assertNotNull($plan);
        $this->assertNotEmpty($plan->planAreas);

        // 10. Verifikasi Realisasi Layanan & Skenario PRD
        // Skenario 1: As Planned
        $asPlanned = ServiceRealization::where('conformity_status', ConformityStatus::AsPlanned->value)
            ->where('status', RealizationStatus::Completed->value)
            ->first();
        $this->assertNotNull($asPlanned);
        $this->assertTrue($asPlanned->realizationAreas()->count() >= 3);
        $this->assertEquals(ValidationStatus::Valid, $asPlanned->validation_status);

        // Skenario 2: Changed from Plan
        $changed = ServiceRealization::where('conformity_status', ConformityStatus::Changed->value)->first();
        $this->assertNotNull($changed);
        $this->assertNotEmpty($changed->change_reason);

        // Skenario 3: Layanan Insidental / Tanpa Rencana
        $incidental = ServiceRealization::where('activity_type', ActivityType::Incidental->value)
            ->whereNull('operation_plan_id')
            ->where('status', RealizationStatus::Completed->value)
            ->first();
        $this->assertNotNull($incidental);
        $this->assertEquals(ConformityStatus::Unplanned, $incidental->conformity_status);

        // Skenario 4: Terkendala (Obstructed) dengan kendala & lampiran foto
        $obstructed = ServiceRealization::where('status', RealizationStatus::Obstructed->value)->first();
        $this->assertNotNull($obstructed);
        $this->assertTrue($obstructed->issues()->count() > 0);
        $this->assertEquals(FollowUpStatus::InProgress, $obstructed->issues->first()->follow_up_status);
        $this->assertTrue(Attachment::where('service_realization_id', $obstructed->id)->exists());

        // Skenario 5: Sedang Berjalan (Running)
        $running = ServiceRealization::where('status', RealizationStatus::Running->value)->first();
        $this->assertNotNull($running);
        $this->assertNull($running->finished_at);

        // 11. Verifikasi Validasi Koordinator
        $validation = ServiceRealizationValidation::first();
        $this->assertNotNull($validation);
        $this->assertTrue($validation->isValid());

        // 12. Verifikasi Audit Log
        $auditLog = AuditLog::first();
        $this->assertNotNull($auditLog);
        $this->assertEquals(ServiceRealization::class, $auditLog->auditable_type);
    }
}
