<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DiseaseReport;
use App\Models\Hospital;
use App\Models\User;
use Carbon\Carbon;

class DiseaseMonitoringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $diseases = [
            'Dengue' => 'High',
            'Cholera' => 'Critical',
            'Malaria' => 'Medium',
            'Typhoid' => 'Low',
            'COVID-19' => 'Medium',
            'Influenza' => 'Low',
            'Pneumonia' => 'Medium'
        ];

        $districts = ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 'Sylhet', 'Barisal', 'Rangpur', 'Mymensingh'];
        $hospitals = Hospital::all();
        $adminUser = User::where('role', 'govt_admin')->first();

        foreach ($diseases as $diseaseName => $defaultSeverity) {
            // Create 3-5 reports per disease
            $numReports = rand(3, 5);
            
            for ($i = 0; $i < $numReports; $i++) {
                $hospital = $hospitals->random();
                $suspected = rand(10, 200);
                $confirmed = rand(5, $suspected);
                $recovered = rand(0, $confirmed);
                $deaths = rand(0, $confirmed - $recovered);
                
                // Recalculate severity based on model logic
                $severity = DiseaseReport::calculateSeverity($confirmed);
                
                $statuses = ['New', 'Monitoring', 'Notice Sent', 'Hospital Alerted', 'Resolved'];
                
                DiseaseReport::create([
                    'disease_name' => $diseaseName,
                    'district' => $districts[array_rand($districts)],
                    'hospital_id' => $hospital->id,
                    'hospital_name' => $hospital->name,
                    'reported_by' => $adminUser ? $adminUser->id : null,
                    'suspected_cases' => $suspected,
                    'confirmed_cases' => $confirmed,
                    'recovered_cases' => $recovered,
                    'death_cases' => $deaths,
                    'severity_level' => $severity,
                    'status' => $statuses[array_rand($statuses)],
                    'notes' => "Automatic surveillance report for {$diseaseName} outbreak monitoring.",
                    'report_date' => Carbon::now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }
}
