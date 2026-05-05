<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\PatientReferral;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DoctorReferralController extends Controller
{
    /**
     * Display a listing of referrals.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Referrals created by this doctor
        $createdReferrals = PatientReferral::where('referred_by_doctor_id', $userId)
            ->with(['patient', 'referredToDoctor', 'referredToHospital'])
            ->latest()
            ->get();
            
        // Referrals assigned to this doctor
        $receivedReferrals = PatientReferral::where('referred_to_doctor_id', $userId)
            ->with(['patient', 'referredByDoctor'])
            ->latest()
            ->get();
            
        return view('doctor.referrals.index', compact('createdReferrals', 'receivedReferrals'));
    }

    /**
     * Show the form for creating a new referral.
     */
    public function create($patientId)
    {
        // Find patient through the Patient model to check authorization if needed
        $patientModel = Patient::findOrFail($patientId);
        $patientUser = $patientModel->user;
        
        // Load potential recipients (excluding the current doctor)
        $doctors = Doctor::with(['user', 'hospital'])
            ->where('user_id', '!=', Auth::id())
            ->get();
        $hospitals = Hospital::with('user')->get();
        
        return view('doctor.referrals.create', compact('patientUser', 'doctors', 'hospitals'));
    }

    /**
     * Store a newly created referral.
     */
    public function store(Request $request, $patientUserId)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:2000',
            'clinical_summary' => 'nullable|string|max:3000',
            'recommended_tests' => 'nullable|string|max:2000',
            'referred_to_doctor_id' => 'required|exists:users,id',
            'department' => 'nullable|string|max:255',
        ]);

        PatientReferral::create([
            'patient_id' => $patientUserId,
            'referred_by_doctor_id' => Auth::id(),
            'referred_to_doctor_id' => $request->referred_to_doctor_id,
            'referral_type' => 'specialist',
            'department' => $request->department,
            'priority' => 'normal',
            'reason' => $request->reason,
            'clinical_summary' => $request->clinical_summary,
            'recommended_tests' => $request->recommended_tests,
            'status' => 'pending',
        ]);

        return redirect()->route('doctor.referrals.index')->with('success', 'Referral created successfully.');
    }

    /**
     * Display the specified referral.
     */
    public function show(PatientReferral $referral)
    {
        $userId = Auth::id();
        
        // Security Check: Only creator or receiver
        if ($referral->referred_by_doctor_id !== $userId && $referral->referred_to_doctor_id !== $userId) {
            // Also allow the hospital admin if referred to hospital
            if ($referral->referred_to_hospital_id !== $userId) {
                abort(403, 'Unauthorized access to this referral.');
            }
        }

        $referral->load(['patient', 'referredByDoctor', 'referredToDoctor', 'referredToHospital']);
        
        return view('doctor.referrals.show', compact('referral'));
    }

    /**
     * Update the status of the referral.
     */
    public function updateStatus(Request $request, PatientReferral $referral)
    {
        $userId = Auth::id();
        
        // Only referred_to_doctor or hospital admin can change status
        if ($referral->referred_to_doctor_id !== $userId && $referral->referred_to_hospital_id !== $userId) {
            // Allow creator to cancel only if pending
            if ($referral->referred_by_doctor_id === $userId && $referral->status === 'pending' && $request->status === 'cancelled') {
                // This is allowed
            } else {
                abort(403, 'Unauthorized to update this referral status.');
            }
        }

        $request->validate([
            'status' => 'required|in:pending,accepted,completed,cancelled,rejected',
        ]);

        $referral->update([
            'status' => $request->status,
        ]);

        // Auto-create appointment if accepted
        if ($request->status === 'accepted' && $referral->referred_to_doctor_id) {
            $doctorProfile = \App\Models\Doctor::where('user_id', $referral->referred_to_doctor_id)->first();
            $patientProfile = \App\Models\Patient::where('user_id', $referral->patient_id)->first();
            
            if ($doctorProfile && $patientProfile) {
                \App\Models\Appointment::create([
                    'patient_id' => $patientProfile->id,
                    'doctor_id' => $doctorProfile->id,
                    'hospital_id' => $doctorProfile->hospital_id,
                    'date' => now()->addDay()->format('Y-m-d'), // Default to tomorrow
                    'time_slot' => '10:00 AM - 11:00 AM', // Placeholder slot
                    'status' => 'pending',
                    'booking_id' => 'BK-REF-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Referral status updated to ' . $request->status);
    }
}
