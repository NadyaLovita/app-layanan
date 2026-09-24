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
        Schema::create('operation_plan_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_plan_id')->constrained('operation_plans')->cascadeOnDelete();
            $table->foreignId('service_area_id')->constrained('service_areas')->restrictOnDelete();
            $table->integer('sequence')->default(1);
            $table->timestamps();

            $table->index(['operation_plan_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_plan_areas');
    }
};
