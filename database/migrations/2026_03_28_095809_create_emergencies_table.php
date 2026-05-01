<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            
            $table->string('emergency_type')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('symptoms')->nullable();
            
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address')->nullable();
            
            $table->string('contact_number')->nullable();
            $table->string('guardian_contact')->nullable();
            
            $table->enum('status', [
                'Sent', 'Reviewing', 'Accepted', 'Ambulance Assigned', 
                'On The Way', 'Arrived', 'Resolved', 'Cancelled', 'Rejected'
            ])->default('Sent');
            
            $table->text('rejection_reason')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergencies');
    }
};
