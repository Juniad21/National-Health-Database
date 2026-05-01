<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->onDelete('cascade');
            $table->string('ambulance_code');
            $table->string('vehicle_number');
            $table->enum('ambulance_type', [
                'Basic Life Support',
                'Advanced Life Support',
                'ICU Ambulance',
                'Neonatal Ambulance',
                'Patient Transport'
            ]);
            $table->integer('capacity')->default(1);
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->enum('current_status', [
                'Available',
                'Assigned',
                'On The Way',
                'At Patient Location',
                'Patient Picked Up',
                'Heading To Hospital',
                'Arrived At Hospital',
                'Completed',
                'Maintenance',
                'Out Of Service'
            ])->default('Available');
            $table->decimal('current_location_lat', 10, 8)->nullable();
            $table->decimal('current_location_lng', 11, 8)->nullable();
            $table->string('current_location_address')->nullable();
            $table->timestamp('last_location_updated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Unique code per hospital
            $table->unique(['hospital_id', 'ambulance_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ambulances');
    }
};
