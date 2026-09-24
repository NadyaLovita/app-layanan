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
        Schema::create('service_realization_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_realization_id')->constrained('service_realizations')->cascadeOnDelete();
            $table->foreignId('service_area_id')->constrained('service_areas')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->integer('sequence')->default(1);
            $table->dateTime('arrived_at')->nullable();
            $table->decimal('volume', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['service_realization_id', 'sequence']);
            $table->index(['service_area_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_realization_areas');
    }
};
