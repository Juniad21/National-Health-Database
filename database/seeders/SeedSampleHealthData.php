<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\PatientHealthMetric;
use Carbon\Carbon;

class SeedSampleHealthData extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'kazi.noman@patient.com')->first();
        if (!$user) return;

        $patient = $user->patient;
        if (!$patient) return;

        // Update profile
        $patient->update([
            'height_cm' => 175.0,
            'weight_kg' => 78.5,
        ]);

        // Clear existing metrics if any to avoid duplication for this demo
        PatientHealthMetric::where('patient_id', $patient->id)->delete();

        $data = [
            [
                'days_ago' => 14,
                'weight' => 80.2,
                'sys' => 135,
                'dia' => 88,
                'hr' => 82,
                'glu' => 110,
                'o2' => 97,
                'temp' => 36.8,
            ],
            [
                'days_ago' => 10,
                'weight' => 79.8,
                'sys' => 130,
                'dia' => 85,
                'hr' => 78,
                'glu' => 105,
                'o2' => 98,
                'temp' => 36.6,
            ],
            [
                'days_ago' => 7,
                'weight' => 79.2,
                'sys' => 125,
                'dia' => 82,
                'hr' => 75,
                'glu' => 102,
                'o2' => 98,
                'temp' => 36.5,
            ],
            [
                'days_ago' => 3,
                'weight' => 78.8,
                'sys' => 122,
                'dia' => 80,
                'hr' => 72,
                'glu' => 98,
                'o2' => 99,
                'temp' => 36.7,
            ],
            [
                'days_ago' => 0,
                'weight' => 78.5,
                'sys' => 120,
                'dia' => 80,
                'hr' => 70,
                'glu' => 95,
                'o2' => 99,
                'temp' => 36.6,
            ],
        ];

        foreach ($data as $item) {
            $date = Carbon::now()->subDays($item['days_ago']);
            
            $heightM = $patient->height_cm / 100;
            $bmi = round($item['weight'] / ($heightM * $heightM), 1);

            PatientHealthMetric::create([
                'user_id' => $user->id,
                'patient_id' => $patient->id,
                'weight_kg' => $item['weight'],
                'systolic_bp' => $item['sys'],
                'diastolic_bp' => $item['dia'],
                'heart_rate' => $item['hr'],
                'glucose_level' => $item['glu'],
                'oxygen_saturation' => $item['o2'],
                'temperature_c' => $item['temp'],
                'bmi' => $bmi,
                'recorded_at' => $date,
                'notes' => 'Automatic sample record.',
            ]);
        }
    }
}
