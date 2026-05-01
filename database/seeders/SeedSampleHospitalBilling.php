<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\Bill;
use App\Models\InsuranceClaim;
use Carbon\Carbon;

class SeedSampleHospitalBilling extends Seeder
{
    public function run()
    {
        $hospital = Hospital::where('name', 'like', '%Square%')->first();
        if (!$hospital) return;

        // Fetch some patients to assign bills to
        $patients = Patient::take(5)->get();
        if ($patients->isEmpty()) return;

        // Clear existing bills for this hospital to start fresh for demo
        Bill::where('hospital_id', $hospital->id)->delete();

        $billingData = [
            [
                'patient_idx' => 0,
                'total' => 15000,
                'paid' => 15000,
                'status' => 'paid',
                'days_ago' => 10,
            ],
            [
                'patient_idx' => 1,
                'total' => 25000,
                'paid' => 10000,
                'status' => 'partially_paid',
                'days_ago' => 5,
            ],
            [
                'patient_idx' => 2,
                'total' => 8500,
                'paid' => 0,
                'status' => 'unpaid',
                'days_ago' => 2,
            ],
            [
                'patient_idx' => 3,
                'total' => 45000,
                'paid' => 45000,
                'status' => 'paid',
                'days_ago' => 15,
            ],
            [
                'patient_idx' => 4,
                'total' => 12000,
                'paid' => 0,
                'status' => 'unpaid',
                'days_ago' => 1,
            ],
        ];

        foreach ($billingData as $data) {
            $patient = $patients[$data['patient_idx']] ?? $patients->first();
            
            $bill = Bill::create([
                'hospital_id' => $hospital->id,
                'patient_id' => $patient->id,
                'bill_number' => 'BILL-' . strtoupper(bin2hex(random_bytes(4))),
                'consultation_fee' => $data['total'] * 0.2,
                'lab_fee' => $data['total'] * 0.3,
                'medicine_fee' => $data['total'] * 0.3,
                'room_fee' => $data['total'] * 0.1,
                'emergency_fee' => 0,
                'other_charges' => $data['total'] * 0.1,
                'discount' => 0,
                'total_amount' => $data['total'],
                'paid_amount' => $data['paid'],
                'due_amount' => $data['total'] - $data['paid'],
                'payment_status' => $data['status'],
                'issued_date' => Carbon::now()->subDays($data['days_ago']),
                'notes' => 'Sample bill for demo purposes.',
            ]);

            // Add an insurance claim for one of the larger bills
            if ($data['total'] > 20000) {
                InsuranceClaim::create([
                    'bill_id' => $bill->id,
                    'patient_id' => $patient->id,
                    'hospital_id' => $hospital->id,
                    'insurance_provider' => 'Green Delta Insurance',
                    'policy_number' => 'POL-' . rand(100000, 999999),
                    'claim_amount' => $bill->total_amount * 0.8,
                    'claim_status' => $data['status'] == 'paid' ? 'settled' : 'pending',
                    'approved_amount' => $data['status'] == 'paid' ? $bill->total_amount * 0.8 : null,
                    'remarks' => 'Sample insurance claim.',
                ]);
            }
        }
    }
}
