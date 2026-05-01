<?php

namespace Database\Seeders;

use App\Models\BloodRequest;
use App\Models\BloodStock;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BloodBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitals = Hospital::all();
        $adminUser = User::where('role', 'govt_admin')->first();
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

        // 1. Seed Blood Stocks for all hospitals
        foreach ($hospitals as $hospital) {
            foreach ($bloodGroups as $group) {
                // Determine realistic stock levels
                // Rare groups (AB-, B-, O-) have lower stock
                $isRare = in_array($group, ['AB-', 'B-', 'O-', 'A-']);
                $available = $isRare ? rand(0, 5) : rand(10, 50);
                $minReq = $isRare ? 2 : 15;

                BloodStock::updateOrCreate(
                    ['hospital_id' => $hospital->id, 'blood_group' => $group],
                    [
                        'hospital_name' => $hospital->name,
                        'district' => 'Dhaka', // Defaulting for now
                        'available_units' => $available,
                        'reserved_units' => rand(0, 2),
                        'minimum_required_units' => $minReq,
                        'last_updated_by' => $hospital->user_id,
                        'notes' => 'Stock verified by hospital staff.',
                    ]
                );
            }
        }

        // 2. Create some Pending Blood Requests
        // Request 1: Square Hospital requesting O- (Critical)
        $square = Hospital::where('name', 'like', '%Square%')->first();
        if ($square) {
            $patient = Patient::inRandomOrder()->first();
            BloodRequest::create([
                'requesting_hospital_id' => $square->id,
                'requesting_hospital_name' => $square->name,
                'district' => 'Dhaka',
                'patient_id' => $patient ? $patient->id : null,
                'blood_group' => 'O-',
                'requested_units' => 4,
                'urgency_level' => 'Critical',
                'request_reason' => 'Emergency bypass surgery. Hospital stock exhausted for O-.',
                'required_by' => Carbon::now()->addHours(2),
                'status' => 'Pending',
            ]);
        }

        // Request 2: DMCH requesting B+ (High)
        $dmch = Hospital::where('name', 'like', '%Dhaka Medical%')->first();
        if ($dmch) {
            BloodRequest::create([
                'requesting_hospital_id' => $dmch->id,
                'requesting_hospital_name' => $dmch->name,
                'district' => 'Dhaka',
                'blood_group' => 'B+',
                'requested_units' => 10,
                'urgency_level' => 'High',
                'request_reason' => 'Multiple trauma cases from highway accident.',
                'required_by' => Carbon::now()->addHours(6),
                'status' => 'Pending',
            ]);
        }

        // Request 3: United Hospital requesting AB- (Medium)
        $united = Hospital::where('name', 'like', '%United%')->first();
        if ($united) {
            BloodRequest::create([
                'requesting_hospital_id' => $united->id,
                'requesting_hospital_name' => $united->name,
                'district' => 'Dhaka',
                'blood_group' => 'AB-',
                'requested_units' => 2,
                'urgency_level' => 'Medium',
                'request_reason' => 'Scheduled rare group transfusion.',
                'required_by' => Carbon::now()->addDays(1),
                'status' => 'Pending',
            ]);
        }

        // 3. Create a Matched Request (to see how it looks)
        $evercare = Hospital::where('name', 'like', '%Evercare%')->first();
        if ($evercare && $square) {
            BloodRequest::create([
                'requesting_hospital_id' => $evercare->id,
                'requesting_hospital_name' => $evercare->name,
                'district' => 'Dhaka',
                'blood_group' => 'A+',
                'requested_units' => 5,
                'urgency_level' => 'Low',
                'request_reason' => 'Restocking for elective surgeries.',
                'required_by' => Carbon::now()->addDays(2),
                'status' => 'Matched',
                'matched_hospital_id' => $square->id,
                'matched_hospital_name' => $square->name,
                'approved_units' => 5,
                'admin_note' => 'Matched with Square Hospital. Availability confirmed.',
                'reviewed_by' => $adminUser ? $adminUser->id : null,
                'reviewed_at' => Carbon::now(),
            ]);
        }
    }
}
