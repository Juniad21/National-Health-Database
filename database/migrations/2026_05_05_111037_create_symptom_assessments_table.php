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
        Schema::create('symptom_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->json('selected_symptoms');
            $table->text('additional_notes')->nullable();
            $table->string('severity');
            $table->string('duration')->nullable();
            $table->string('suggested_specialty')->nullable();
            $table->json('analysis_results')->nullable(); // For storing detailed probability scores
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptom_assessments');
    }
};
