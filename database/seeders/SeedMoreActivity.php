<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\Doctor;
use App\Models\Emergency;
use App\Models\Bill;
use App\Models\LabOrder;
use App\Models\LabTestCatalog;
use App\Models\User;
use Carbon\Carbon;

class SeedMoreActivity extends Seeder
{
    public function run()
    {
        $patients = Patient::all();
        $squareHospital = Hospital::where('name', 'like', '%Square%')->first();
        $doctors = Doctor::where('hospital_id', $squareHospital->id)->get();
        $labTests = LabTestCatalog::all();

        if ($patients->isEmpty() || !$squareHospital) return;

        // 1. More Emergencies
        $emergencyTypes = ['Cardiac Arrest', 'Respiratory Distress', 'Severe Bleeding', 'Stroke', 'Unconscious'];
        foreach (range(1, 4) as $i) {
            $patient = $patients->random();
            $status = $i % 2 == 0 ? 'Sent' : 'Accepted';
            
            Emergency::create([
                'patient_id' => $patient->id,
                'hospital_id' => $status == 'Accepted' ? $squareHospital->id : null,
                'emergency_type' => $emergencyTypes[array_rand($emergencyTypes)],
                'severity' => $i % 2 == 0 ? 'critical' : 'high',
                'status' => $status,
                'contact_number' => $patient->phone,
                'latitude' => 23.81 + (rand(-10, 10) / 1000),
                'longitude' => 90.41 + (rand(-10, 10) / 1000),
                'created_at' => Carbon::now()->subMinutes(rand(5, 120)),
            ]);
        }

        // 2. More Bills for various patients at Square Hospital
        foreach (range(1, 8) as $i) {
            $patient = $patients->random();
            $total = rand(5000, 50000);
            $isPaid = $i % 3 == 0;
            
            Bill::create([
                'hospital_id' => $squareHospital->id,
                'patient_id' => $patient->id,
                'bill_number' => 'BILL-' . strtoupper(bin2hex(random_bytes(4))),
                'consultation_fee' => $total * 0.15,
                'lab_fee' => $total * 0.25,
                'medicine_fee' => $total * 0.4,
                'room_fee' => $total * 0.1,
                'emergency_fee' => 0,
                'other_charges' => $total * 0.1,
                'discount' => 0,
                'total_amount' => $total,
                'paid_amount' => $isPaid ? $total : (rand(0, 1) ? $total * 0.5 : 0),
                'due_amount' => $isPaid ? 0 : ($total - ($isPaid ? $total : (rand(0, 1) ? $total * 0.5 : 0))),
                'payment_status' => $isPaid ? 'paid' : (rand(0, 1) ? 'partially_paid' : 'unpaid'),
                'issued_date' => Carbon::now()->subDays(rand(1, 30)),
                'notes' => 'Generated activity seeder.',
            ]);
        }

        // 3. More Lab Orders
        if (!$doctors->isEmpty() && !$labTests->isEmpty()) {
            foreach (range(1, 5) as $i) {
                LabOrder::create([
                    'patient_id' => $patients->random()->id,
                    'doctor_id' => $doctors->random()->id,
                    'hospital_id' => $squareHospital->id,
                    'lab_test_catalog_id' => $labTests->random()->id,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
