<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->decimal('height_cm', 5, 1)->nullable()->after('address');
            $table->decimal('weight_kg', 5, 1)->nullable()->after('height_cm');
            $table->string('emergency_contact_name')->nullable()->after('weight_kg');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->text('allergies')->nullable()->after('emergency_contact_phone');
            $table->text('medical_conditions')->nullable()->after('allergies');
            $table->text('current_medications')->nullable()->after('medical_conditions');
            $table->text('past_surgeries')->nullable()->after('current_medications');
            $table->text('family_history')->nullable()->after('past_surgeries');
            $table->text('lifestyle_notes')->nullable()->after('family_history');
            $table->string('smoking_status')->nullable()->after('lifestyle_notes');
            $table->string('alcohol_status')->nullable()->after('smoking_status');
            $table->string('activity_level')->nullable()->after('alcohol_status');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'height_cm', 'weight_kg',
                'emergency_contact_name', 'emergency_contact_phone',
                'allergies', 'medical_conditions', 'current_medications',
                'past_surgeries', 'family_history', 'lifestyle_notes',
                'smoking_status', 'alcohol_status', 'activity_level',
            ]);
        });
    }
};
