<?php

namespace Database\Seeders;

use App\Models\DiseaseReport;
use App\Models\Hospital;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiseaseReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = Hospital::take(3)->get();
        $districts = ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna'];
        $diseases = ['Dengue', 'Malaria', 'COVID-19', 'Cholera', 'Influenza'];

        foreach (range(1, 15) as $index) {
            $hospital = $hospitals->random();
            $confirmed = rand(5, 120);
            
            DiseaseReport::create([
                'disease_name' => $diseases[array_rand($diseases)],
                'district' => $districts[array_rand($districts)],
                'hospital_id' => $hospital->id,
                'hospital_name' => $hospital->name,
                'reported_by' => 1, // System admin
                'suspected_cases' => $confirmed + rand(10, 50),
                'confirmed_cases' => $confirmed,
                'recovered_cases' => round($confirmed * 0.8),
                'death_cases' => rand(0, round($confirmed * 0.05)),
                'severity_level' => DiseaseReport::calculateSeverity($confirmed),
                'status' => ['New', 'Monitoring', 'Hospital Alerted', 'Resolved'][rand(0, 3)],
                'notes' => 'Generated seed data for monitoring.',
                'report_date' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
