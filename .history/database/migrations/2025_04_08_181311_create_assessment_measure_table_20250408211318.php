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
        Schema::create('assessment_measure', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measure_id')->constrained()->cascadeOnDelete();
            $table->enum('measure_type', ['existing', 'planned']); // Is the measure existing or planned for this assessment?
            // No timestamps needed for typical pivot table

            // Ensure a measure is only linked once per assessment with a specific type
            $table->unique(['risk_assessment_id', 'measure_id', 'measure_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_measure');
    }
};
