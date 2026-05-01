<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emergency;
use App\Models\Patient;
use Carbon\Carbon;

class CleanupEmergencies extends Seeder
{
    public function run()
    {
        // Clear all existing emergencies
        Emergency::truncate();

        // Optional: Add one fresh "Sent" emergency for testing
        $patient = Patient::first();
        if ($patient) {
            Emergency::create([
                'patient_id' => $patient->id,
                'emergency_type' => 'Accident / Trauma',
                'severity' => 'high',
                'status' => 'Sent',
                'contact_number' => $patient->phone ?? '01811000000',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'created_at' => Carbon::now(),
            ]);
        }
    }
}
