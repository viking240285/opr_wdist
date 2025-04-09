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
        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_map_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hazard_id')->constrained()->cascadeOnDelete();
            $table->float('probability');
            $table->float('severity');
            $table->float('exposure');
            $table->float('calculated_risk')->nullable();
            $table->string('risk_category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
    }
};
