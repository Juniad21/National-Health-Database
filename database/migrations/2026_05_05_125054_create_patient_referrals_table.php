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
        Schema::create('patient_referrals', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys to users table
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('referred_by_doctor_id')->constrained('users')->onDelete('cascade');
            
            // Nullable Foreign Keys to users table
            $table->foreignId('referred_to_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referred_to_hospital_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Details
            $table->string('referral_type'); // specialist, hospital, department, diagnostic, emergency
            $table->string('department')->nullable();
            $table->string('priority')->default('normal'); // normal, urgent, emergency
            
            $table->text('reason');
            $table->text('clinical_summary')->nullable();
            $table->text('recommended_tests')->nullable();
            
            $table->string('status')->default('pending'); // pending, accepted, completed, cancelled, rejected
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function up_down(): void
    {
        Schema::dropIfExists('patient_referrals');
    }
};
