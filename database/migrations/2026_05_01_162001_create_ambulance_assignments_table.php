<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulance_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambulance_id')->constrained()->onDelete('cascade');
            $table->foreignId('emergency_alert_id')->constrained('emergencies')->onDelete('cascade');
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('arrived_patient_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('arrived_hospital_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', [
                'Assigned',
                'Accepted',
                'On The Way',
                'At Patient Location',
                'Patient Picked Up',
                'Heading To Hospital',
                'Arrived At Hospital',
                'Completed',
                'Cancelled'
            ])->default('Assigned');
            $table->decimal('pickup_lat', 10, 8)->nullable();
            $table->decimal('pickup_lng', 11, 8)->nullable();
            $table->string('pickup_address')->nullable();
            $table->foreignId('destination_hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();
            $table->string('destination_address')->nullable();
            $table->decimal('estimated_distance_km', 8, 2)->nullable();
            $table->integer('eta_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulance_assignments');
    }
};
