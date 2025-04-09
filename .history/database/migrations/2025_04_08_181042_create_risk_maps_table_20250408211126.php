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
        Schema::create('risk_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workplace_id')->constrained()->cascadeOnDelete();
            $table->date('assessment_date');
            $table->json('commission_members')->nullable(); // Store commission details as JSON
            $table->string('status')->default('draft'); // Status: draft, completed, archived
            $table->foreignId('conducted_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->json('participants')->nullable(); // Store participants (e.g., user IDs) as JSON
            $table->timestamps();
            $table->softDeletes(); // Add soft delete column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_maps');
    }
};
