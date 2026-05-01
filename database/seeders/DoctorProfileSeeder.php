<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorProfile;
use App\Models\Hospital;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DoctorProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure hospitals exist
        $hospitals = [
            'Square Hospital' => 1,
            'Evercare Hospital' => 2,
            'United Hospital' => 3,
            'Dhaka Medical College' => 4,
            'Popular Diagnostic' => 5,
        ];

        $doctors = Doctor::all();

        $specializations = [
            'Cardiology' => ['Heart Surgery', 'Angioplasty', 'ECG Interpretation'],
            'Neurology' => ['Brain Mapping', 'Stroke Management', 'Sleep Disorders'],
            'Orthopedics' => ['Joint Replacement', 'Sports Medicine', 'Fracture Care'],
            'Pediatrics' => ['Child Nutrition', 'Immunization', 'Neonatal Care'],
            'Internal Medicine' => ['Diabetes Management', 'Hypertension Control', 'Chronic Care'],
            'Dermatology' => ['Skin Allergy', 'Laser Surgery', 'Cosmetic Dermatology'],
            'General Practice' => ['Health Checkup', 'Vaccination', 'Primary Care'],
        ];

        $statuses = ['Pending', 'Verified', 'Rejected', 'Needs Review'];

        foreach ($doctors as $doctor) {
            $name = strtolower($doctor->first_name . ' ' . $doctor->last_name);
            $hospitalId = $doctor->hospital_id;
            
            // Fix mappings based on user request if needed
            if (str_contains($name, 'shireen')) {
                $hospitalId = 1; // Square
            } elseif (str_contains($name, 'tariq')) {
                $hospitalId = 1; // Square
            } elseif (str_contains($name, 'salma')) {
                $hospitalId = 2; // Evercare
            } elseif (str_contains($name, 'kamal')) {
                $hospitalId = 3; // United
            } elseif (str_contains($name, 'farhana')) {
                $hospitalId = 4; // DMCH
            }

            // Update doctor's hospital_id in the main doctors table as well
            $doctor->update(['hospital_id' => $hospitalId]);

            $hospital = Hospital::find($hospitalId);
            $specialty = $doctor->specialty ?: 'General Practice';
            $services = $specializations[$specialty] ?? ['General Checkup', 'Consultation'];
            
            DoctorProfile::updateOrCreate(
                ['user_id' => $doctor->user_id],
                [
                    'doctor_id' => $doctor->id,
                    'full_name' => "Dr. " . $doctor->first_name . " " . $doctor->last_name,
                    'date_of_birth' => Carbon::now()->subYears(rand(30, 55))->subDays(rand(1, 365)),
                    'gender' => rand(0, 1) ? 'Male' : 'Female',
                    'phone' => '01' . rand(7, 9) . rand(10000000, 99999999),
                    'email' => $doctor->user->email,
                    'address' => rand(10, 500) . ' Road No. ' . rand(1, 20) . ', ' . ($hospital->address ?? 'Dhaka'),
                    'license_number' => $doctor->bmdc_number,
                    'license_expiry_date' => Carbon::now()->addYears(rand(1, 5)),
                    'specialization' => $specialty,
                    'qualifications' => $doctor->qualifications ?: 'MBBS, FCPS',
                    'medical_college' => 'Dhaka Medical College',
                    'years_of_experience' => rand(5, 25),
                    'hospital_id' => $hospitalId,
                    'hospital_name' => $hospital->name ?? 'Private Practice',
                    'department' => $specialty . ' Department',
                    'designation' => rand(0, 1) ? 'Senior Consultant' : 'Associate Professor',
                    'consultation_fee' => rand(500, 2000),
                    'consultation_type' => rand(0, 1) ? (rand(0, 1) ? 'In-person' : 'Online') : 'Both',
                    'available_days' => 'Saturday, Monday, Wednesday, Thursday',
                    'available_time_slots' => '10:00 AM - 01:00 PM, 05:00 PM - 09:00 PM',
                    'languages_spoken' => 'Bengali, English',
                    'biography' => "Dr. {$doctor->first_name} is a highly skilled professional with extensive experience in {$specialty}.",
                    'services_offered' => implode(', ', $services),
                    'awards_certifications' => 'Gold Medalist in Medicine',
                    'emergency_availability' => rand(0, 1) == 1,
                    'verification_status' => $statuses[array_rand($statuses)],
                    'admin_notes' => 'Verified with BMDC online database.',
                ]
            );
        }
    }
}
