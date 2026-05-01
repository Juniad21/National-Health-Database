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
        Schema::create('doctor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->onDelete('set null');
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('license_number');
            $table->date('license_expiry_date')->nullable();
            $table->string('specialization');
            $table->text('qualifications')->nullable();
            $table->string('medical_college')->nullable();
            $table->integer('years_of_experience')->default(0);
            $table->foreignId('hospital_id')->nullable()->constrained('hospitals')->onDelete('set null');
            $table->string('hospital_name')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->decimal('consultation_fee', 10, 2)->nullable();
            $table->string('consultation_type')->nullable(); // Online, In-person, Both
            $table->string('available_days')->nullable(); // Store as comma-separated or JSON
            $table->string('available_time_slots')->nullable(); // Store as comma-separated or JSON
            $table->string('languages_spoken')->nullable();
            $table->text('biography')->nullable();
            $table->text('services_offered')->nullable();
            $table->text('awards_certifications')->nullable();
            $table->boolean('emergency_availability')->default(false);
            $table->string('verification_status')->default('Pending'); // Pending, Verified, Rejected, Needs Review
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_profiles');
    }
};
