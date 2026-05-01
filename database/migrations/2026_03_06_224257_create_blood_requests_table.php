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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requesting_hospital_id')->constrained('hospitals')->cascadeOnDelete();
            $table->string('requesting_hospital_name')->nullable();
            $table->string('district')->nullable();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('blood_group');
            $table->integer('requested_units');
            $table->enum('urgency_level', ['Low', 'Medium', 'High', 'Critical']);
            $table->text('request_reason')->nullable();
            $table->dateTime('required_by')->nullable();
            $table->enum('status', ['Pending', 'Under Review', 'Matched', 'Approved', 'Partially Approved', 'Rejected', 'Fulfilled', 'Cancelled'])->default('Pending');
            $table->foreignId('matched_hospital_id')->nullable()->constrained('hospitals')->nullOnDelete();
            $table->string('matched_hospital_name')->nullable();
            $table->integer('approved_units')->nullable();
            $table->text('admin_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
