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
            'pending_verifications' => \App\Models\DoctorProfile::where('verification_status', 'Pending')->count(),
            'registered_hospitals' => Hospital::count(),
            'hospitals_under_review' => 0, 
            'active_patients' => Patient::count(),
            'claims_processed' => InsuranceClaim::count(),
            'reported_incidents' => \App\Models\Emergency::count()
        ];

        // Fetch pending verifications
        $pendingDoctors = \App\Models\DoctorProfile::where('verification_status', 'Pending')
            ->with(['doctor', 'hospital'])
            ->latest()
            ->take(5)
            ->get();

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

    public function emergencies()
    {
        $emergencies = \App\Models\Emergency::with(['patient', 'hospital'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $emergencies->count(),
            'active' => $emergencies->whereNotIn('status', ['Resolved', 'Cancelled', 'Rejected'])->count(),
            'resolved' => $emergencies->where('status', 'Resolved')->count(),
            'rejected' => $emergencies->where('status', 'Rejected')->count(),
        ];

        $allHospitals = Hospital::orderBy('name')->get();

        return view('govt_admin.emergencies.index', compact('emergencies', 'stats', 'allHospitals'));
    }

    public function doctors(Request $request)
    {
        $query = \App\Models\DoctorProfile::query();

        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }
        if ($request->filled('status')) {
            $query->where('verification_status', $request->status);
        }
        if ($request->filled('hospital')) {
            $query->whereHas('hospital', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->hospital . '%');
            });
        }

        $doctors = $query->with(['doctor', 'hospital'])->paginate(15);
        
        return view('govt_admin.doctors.index', compact('doctors'));
    }

    public function showDoctor($id)
    {
        $profile = \App\Models\DoctorProfile::with(['user', 'doctor', 'hospital'])->findOrFail($id);
        return view('govt_admin.doctors.show', compact('profile'));
    }

    public function verifyDoctor(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pending,Verified,Rejected,Needs Review',
            'admin_notes' => 'nullable|string',
        ]);

        $profile = \App\Models\DoctorProfile::findOrFail($id);
        $profile->update([
            'verification_status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('govt_admin.doctors.index')->with('success', "Doctor verification status updated to {$validated['status']}");
    }

    public function hospitals(Request $request)
    {
        $query = Hospital::with(['doctors', 'emergencies']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        $hospitals = $query->paginate(10);

        // Fetch resource summaries for each hospital
        foreach ($hospitals as $hospital) {
            $resources = $hospital->resources;
            $hospital->resource_summary = [
                'beds' => $resources->where('resource_type', 'General Bed')->first(),
                'icu' => $resources->where('resource_type', 'ICU Unit')->first(),
                'vent' => $resources->where('resource_type', 'Ventilator')->first(),
                'oxygen' => $resources->where('resource_type', 'Oxygen Cylinder')->first(),
            ];
        }

        return view('govt_admin.hospitals.index', compact('hospitals'));
    }

    public function ambulances(Request $request)
    {
        $query = \App\Models\Ambulance::with(['hospital', 'currentAssignment.emergency']);

        if ($request->filled('hospital')) {
            $query->whereHas('hospital', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->hospital . '%');
            });
        }
        if ($request->filled('type')) {
            $query->where('ambulance_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('current_status', $request->status);
        }

        $ambulances = $query->paginate(15);
        
        $stats = [
            'total' => \App\Models\Ambulance::count(),
            'available' => \App\Models\Ambulance::where('current_status', 'Available')->count(),
            'active_trips' => \App\Models\AmbulanceAssignment::whereNotIn('status', ['Completed', 'Cancelled'])->count(),
        ];

        return view('govt_admin.ambulances.index', compact('ambulances', 'stats'));
    }

    public function dispatchEmergency(Request $request, $id)
    {
        $validated = $request->validate([
            'hospital_ids' => 'required|array',
            'hospital_ids.*' => 'exists:hospitals,id',
        ]);

        $emergency = \App\Models\Emergency::findOrFail($id);
        $emergency->targetHospitals()->sync($validated['hospital_ids']);
        $emergency->update(['status' => 'Sent']);

        return back()->with('success', 'Emergency alert dispatched to selected hospitals.');
    }
}
