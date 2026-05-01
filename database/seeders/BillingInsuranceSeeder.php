<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Hospital;
use App\Models\Bill;
use App\Models\InsuranceClaim;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BillingInsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointments = Appointment::with(['patient', 'hospital'])->get();
        $insuranceProviders = ['MetLife Bangladesh', 'Green Delta Insurance', 'Pragati Insurance', 'Reliance Insurance', 'Jiban Bima Corporation'];
        
        $statuses = ['paid', 'unpaid', 'partially_paid'];
        
        $billCount = 0;
        $claimCount = 0;

        foreach ($appointments as $appointment) {
            // Only create bills for some appointments (e.g., Completed or old ones)
            // But for seeding purposes, let's create a bill for 80% of appointments
            if (rand(1, 100) <= 80) {
                
                $consultationFee = rand(500, 2000);
                $labFee = rand(0, 1) ? rand(1000, 5000) : 0;
                $medicineFee = rand(0, 1) ? rand(500, 3000) : 0;
                $roomFee = rand(0, 100) > 80 ? rand(2000, 10000) : 0; // 20% had a room stay
                $emergencyFee = rand(0, 100) > 90 ? rand(1000, 3000) : 0; // 10% emergency
                $otherCharges = rand(0, 500);
                
                $totalAmount = $consultationFee + $labFee + $medicineFee + $roomFee + $emergencyFee + $otherCharges;
                
                // Add some discount
                $discount = rand(0, 100) > 70 ? rand(0, $totalAmount * 0.1) : 0; // 30% get up to 10% discount
                
                $finalTotal = $totalAmount - $discount;
                
                $status = $statuses[array_rand($statuses)];
                
                $paidAmount = 0;
                if ($status === 'paid') {
                    $paidAmount = $finalTotal;
                } elseif ($status === 'partially_paid') {
                    // Partially paid
                    $paidAmount = rand(0, $finalTotal * 0.5);
                }
                
                $dueAmount = $finalTotal - $paidAmount;
                
                // Set issued date around the appointment date
                $issuedDate = Carbon::parse($appointment->appointment_date)->addHours(rand(1, 24));

                $bill = Bill::create([
                    'patient_id' => $appointment->patient_id,
                    'hospital_id' => $appointment->hospital_id,
                    'appointment_id' => $appointment->id,
                    'bill_number' => 'BILL-' . strtoupper(Str::random(8)),
                    'consultation_fee' => $consultationFee,
                    'lab_fee' => $labFee,
                    'medicine_fee' => $medicineFee,
                    'room_fee' => $roomFee,
                    'emergency_fee' => $emergencyFee,
                    'other_charges' => $otherCharges,
                    'discount' => $discount,
                    'total_amount' => $finalTotal,
                    'paid_amount' => $paidAmount,
                    'due_amount' => $dueAmount,
                    'payment_status' => $status,
                    'issued_date' => $issuedDate,
                    'notes' => 'Generated automatically from appointment.',
                ]);
                $billCount++;

                // Let's create an insurance claim for 40% of the bills
                if (rand(1, 100) <= 40) {
                    $claimStatuses = ['pending', 'approved', 'rejected', 'settled'];
                    $claimStatus = $claimStatuses[array_rand($claimStatuses)];
                    
                    // Claim amount is usually the total minus maybe some non-covered items
                    $claimAmount = $finalTotal; 
                    
                    $approvedAmount = 0;
                    if ($claimStatus === 'approved' || $claimStatus === 'settled') {
                        // Approve 70-100% of the claim
                        $approvedAmount = rand($claimAmount * 0.7, $claimAmount);
                        
                        // If insurance paid it, the bill might be marked Paid or partially_paid
                        $bill->paid_amount = min($bill->total_amount, $bill->paid_amount + $approvedAmount);
                        $bill->due_amount = max(0, $bill->total_amount - $bill->paid_amount);
                        if ($bill->due_amount == 0) {
                            $bill->payment_status = 'paid';
                        } else {
                            $bill->payment_status = 'partially_paid';
                        }
                        $bill->save();
                    }

                    InsuranceClaim::create([
                        'bill_id' => $bill->id,
                        'patient_id' => $bill->patient_id,
                        'hospital_id' => $bill->hospital_id,
                        'insurance_provider' => $insuranceProviders[array_rand($insuranceProviders)],
                        'policy_number' => 'POL-' . rand(1000000, 9999999),
                        'claim_amount' => $claimAmount,
                        'approved_amount' => $approvedAmount,
                        'claim_status' => $claimStatus,
                        'remarks' => 'Routine insurance claim processing.',
                    ]);
                    $claimCount++;
                }
            }
        }
        
        $this->command->info("Seeded {$billCount} realistic bills and {$claimCount} insurance claims!");
    }
}
