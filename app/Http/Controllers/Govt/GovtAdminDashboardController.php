<?php

namespace App\Http\Controllers\Govt;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\InsuranceClaim;
use App\Models\HospitalResource;
use Illuminate\Http\Request;

class GovtAdminDashboardController extends Controller
{
    public function index()
    {
        // Real data from the database
        $stats = [
            'registered_doctors' => Doctor::count(),
            'pending_verifications' => 0, // No status field in schema
            'registered_hospitals' => Hospital::count(),
            'hospitals_under_review' => 0, // No status field in schema
            'active_patients' => Patient::count(),
            'claims_processed' => InsuranceClaim::count(),
            'reported_incidents' => 0
        ];

        // Fetch some doctors for the verification queue (simulated as latest entries)
        $pendingDoctors = Doctor::with('hospital')->latest()->take(3)->get()->map(function ($doctor) {
            return [
                'name' => "Dr. " . $doctor->first_name . " " . $doctor->last_name,
                'license' => $doctor->bmdc_number,
                'specialty' => $doctor->specialty,
                'hospital' => $doctor->hospital->name ?? 'N/A',
                'date' => $doctor->created_at->format('Y-m-d'),
                'status' => 'Pending'
            ];
        });

        // Fetch hospital resource data
        $hospitals = Hospital::with('doctors')->take(3)->get()->map(function ($hospital) {
            $resources = HospitalResource::where('hospital_id', $hospital->id)->get();
            $beds = $resources->where('resource_type', 'General Bed')->first();
            $icu = $resources->where('resource_type', 'ICU Unit')->first();
            $vent = $resources->where('resource_type', 'Ventilator')->first();
            $blood = $resources->where('resource_type', 'Blood Bank')->first();

            $bedOcc = $beds ? round(($beds->currently_in_use / $beds->total_capacity) * 100) . '%' : '0%';
            $icuStat = $icu ? ($icu->total_capacity - $icu->currently_in_use) . '/' . $icu->total_capacity : '0/0';
            $ventStat = $vent ? ($vent->total_capacity - $vent->currently_in_use) . '/' . $vent->total_capacity : '0/0';
            
            $bloodStatus = 'Normal';
            if ($blood) {
                $ratio = $blood->currently_in_use / $blood->total_capacity;
                if ($ratio < 0.2) $bloodStatus = 'Critical';
                elseif ($ratio < 0.5) $bloodStatus = 'Warning';
            }

            return [
                'name' => $hospital->name,
                'district' => explode(',', $hospital->address)[0] ?? 'N/A',
                'type' => str_contains($hospital->name, 'College') || str_contains($hospital->name, 'DMCH') ? 'Government' : 'Private',
                'beds' => $bedOcc,
                'icu' => $icuStat,
                'vent' => $ventStat,
                'blood' => $bloodStatus,
                'compliance' => 'Normal'
            ];
        });

        $alerts = [
            ['title' => 'Critical ICU Shortage', 'target' => 'Evercare Hospital', 'severity' => 'Critical', 'time' => '10 mins ago'],
            ['title' => 'Low O- Blood Stock', 'target' => 'DMCH', 'severity' => 'High', 'time' => '1 hour ago'],
            ['title' => 'Expired License Detected', 'target' => 'Dr. Rahim (BMDC-0012)', 'severity' => 'Medium', 'time' => '3 hours ago'],
        ];

        return view('govt_admin.dashboard', compact('stats', 'pendingDoctors', 'hospitals', 'alerts'));
    }
}
