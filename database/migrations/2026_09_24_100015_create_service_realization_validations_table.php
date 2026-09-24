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
        Schema::create('service_realization_validations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_realization_id')->constrained('service_realizations')->cascadeOnDelete();
            $table->foreignId('validated_by')->constrained('users')->restrictOnDelete();
            $table->string('result'); // valid, needs_revision
            $table->text('notes')->nullable();
            $table->dateTime('validated_at');
            $table->timestamps();

            $table->index(['service_realization_id', 'validated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_realization_validations');
    }
};
