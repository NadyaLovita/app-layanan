<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_plan_id')->nullable()->constrained('operation_plans')->nullOnDelete();
            $table->string('activity_type')->default('incidental'); // planned, incidental
            $table->date('activity_date');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->foreignId('vehicle_id')->constrained('vehicles')->restrictOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->restrictOnDelete();
            $table->string('status')->default('planned'); // planned, running, completed, obstructed, delayed, cancelled
            $table->decimal('total_volume', 10, 2)->nullable();
            $table->string('volume_unit')->nullable(); // ton, kg, m3, trip
            $table->foreignId('final_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->text('field_condition')->nullable();
            $table->string('conformity_status')->default('unplanned'); // as_planned, changed, not_executed, unplanned
            $table->text('change_reason')->nullable();
            $table->string('validation_status')->default('pending'); // pending, valid, needs_revision
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activity_date', 'status']);
            $table->index(['vehicle_id', 'activity_date']);
            $table->index(['driver_id', 'activity_date']);
            $table->index('validation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_realizations');
    }
};
