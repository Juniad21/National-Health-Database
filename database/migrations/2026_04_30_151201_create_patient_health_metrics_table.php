<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_health_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('patient_id');
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->decimal('glucose_level', 6, 1)->nullable();
            $table->decimal('oxygen_saturation', 4, 1)->nullable();
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->decimal('bmi', 5, 1)->nullable();
            $table->text('notes')->nullable();
            $table->date('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_health_metrics');
    }
};
