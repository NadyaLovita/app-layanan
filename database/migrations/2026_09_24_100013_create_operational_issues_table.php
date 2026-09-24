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
        Schema::create('operational_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_realization_id')->constrained('service_realizations')->cascadeOnDelete();
            $table->foreignId('issue_type_id')->constrained('issue_types')->restrictOnDelete();
            $table->text('description');
            $table->dateTime('occurred_at');
            $table->text('follow_up')->nullable();
            $table->string('follow_up_status')->default('open'); // open, in_progress, done
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['service_realization_id', 'issue_type_id']);
            $table->index('follow_up_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operational_issues');
    }
};
