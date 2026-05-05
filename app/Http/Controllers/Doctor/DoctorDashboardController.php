<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;
        $search = $request->query('search', '');

        $pendingApprovals = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->where(function($q) {
                $q->where('status', 'pending')->orWhere('status', 'Pending');
            })
            ->with(['patient', 'hospital'])
            ->orderBy('date', 'asc')
            ->get();

        $waitingQueue = \App\Models\Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->where('status', 'approved')
            ->orderBy('time_slot', 'asc')
            ->get();

        $patientResults = null;
        if ($search !== '') {
            $patientResults = \App\Models\Patient::where('nid', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->get();
        }

        // Referral Statistics
        $userId = Auth::id();
        $referralStats = [
            'sent_pending' => \App\Models\PatientReferral::where('referred_by_doctor_id', $userId)->where('status', 'pending')->count(),
            'received' => \App\Models\PatientReferral::where('referred_to_doctor_id', $userId)->where('status', 'pending')->count(),
        ];
        
        return view('doctor.dashboard', [
            'waitingQueue' => $waitingQueue,
            'pendingApprovals' => $pendingApprovals,
            'search' => $search,
            'patientResults' => $patientResults,
            'doctor' => $doctor,
            'referralStats' => $referralStats,
        ]);
    }

    public function viewPatient($id)
    {
        // FIXED BY JUNAID: Implement strict data access control
        // Doctors must have an approved AccessRequest to view full medical history
        $patient = \App\Models\Patient::findOrFail($id);
        $doctor = Auth::user()->doctor;

        $hasConsent = \App\Models\AccessRequest::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'approved')
            ->exists();

        $medicalRecords = collect();
        $records = collect();
        
        if ($hasConsent) {
            $medicalRecords = \App\Models\MedicalRecord::where('patient_id', $patient->id)
                ->with('doctor')
                ->orderBy('date', 'desc')
                ->get();

            // Group records by type for display 
            $records = $medicalRecords->groupBy('record_type');
        }

        // Fetch lab tests for the consultation form
        $labTests = \App\Models\LabTestCatalog::orderBy('test_name', 'asc')->get();

        return view('doctor.patient_view', [
            'patient' => $patient,
            'records' => $records,
            'labTests' => $labTests,
            'hasConsent' => $hasConsent,
            'pendingRequest' => \App\Models\AccessRequest::where('patient_id', $patient->id)
                ->where('doctor_id', $doctor->id)
                ->where('status', 'pending')
                ->exists()
        ]);
    }

    public function requestAccess(Request $request, $id)
    {
        $patient = \App\Models\Patient::findOrFail($id);
        $doctor = Auth::user()->doctor;

        \App\Models\AccessRequest::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Access request sent to patient!');
    }

    public function consultation($patient_id)
    {
        // FIXED BY JUNAID: Security check for consultation access
        $patient = \App\Models\Patient::findOrFail($patient_id);
        $doctor = Auth::user()->doctor;

        $hasConsent = \App\Models\AccessRequest::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'approved')
            ->exists();

        if (!$hasConsent) {
            return redirect()->route('doctor.patient.view', $patient_id)
                ->with('error', 'You must have patient consent to start a consultation and view medical history.');
        }

        $medicalRecords = \App\Models\MedicalRecord::where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();

        return view('doctor.consultation', [
            'patient' => $patient,
            'medicalRecords' => $medicalRecords
        ]);
    }

    public function storeConsultation(Request $request, $patient_id)
    {
        $validated = $request->validate([
            'diagnosis'             => 'required|string',
            'medications'           => 'nullable|array',
            'medications.*.name'    => 'required|string',
            'medications.*.dosage'  => 'required|string',
            'medications.*.duration' => 'required|string',
            'medications.*.instructions' => 'required|string',
            'lab_test_ids'          => 'nullable|array',
            'lab_test_ids.*'        => 'exists:lab_test_catalogs,id',
        ]);

        $patient = \App\Models\Patient::findOrFail($patient_id);
        $doctor  = Auth::user()->doctor;
        $hospital = $doctor->hospital;

        // Build prescription text from structured medication fields
        $medsText = '';
        if (!empty($validated['medications'])) {
            foreach ($validated['medications'] as $i => $med) {
                $medsText .= ($i + 1) . ". {$med['name']} — {$med['dosage']} | {$med['duration']} | {$med['instructions']}\n";
            }
        }

        // Save prescription record
        \App\Models\MedicalRecord::create([
            'patient_id'              => $patient->id,
            'doctor_id'               => $doctor->id,
            'hospital_id'             => $hospital?->id,
            'record_type'             => 'prescription',
            'diagnosis'               => $validated['diagnosis'],
            'medications_or_results'  => $medsText ?: 'No medications prescribed.',
            'date'                    => now(),
        ]);

        // Save each lab test as a LabOrder — always linked to the doctor's hospital
        if (!empty($validated['lab_test_ids'])) {
            foreach ($validated['lab_test_ids'] as $testId) {
                \App\Models\LabOrder::create([
                    'patient_id'          => $patient->id,
                    'doctor_id'           => $doctor->id,
                    'hospital_id'         => $hospital?->id,
                    'lab_test_catalog_id' => $testId,
                    'status'              => 'pending',
                ]);
            }

            // Save a lab record in medical records so patient sees it
            $labNames = \App\Models\LabTestCatalog::whereIn('id', $validated['lab_test_ids'])
                ->pluck('test_name')->implode(', ');

            \App\Models\MedicalRecord::create([
                'patient_id'             => $patient->id,
                'doctor_id'              => $doctor->id,
                'hospital_id'            => $hospital?->id,
                'record_type'            => 'lab',
                'diagnosis'              => 'Lab Tests Ordered',
                'medications_or_results' => "Tests: {$labNames}\nPlease attend: " . ($hospital?->name ?? 'your doctor\'s hospital') . "\nStatus: Pending",
                'date'                   => now(),
            ]);
        }

        // Mark appointment as completed and set called_at to notify patient
        $appointment = \App\Models\Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where(function($q) {
                $q->where('status', 'approved')->orWhere('status', 'Approved');
            })
            ->latest()
            ->first();

        if ($appointment) {
            $appointment->update([
                'status'    => 'completed',
                'called_at' => now(),
            ]);
        }

        return redirect()->route('doctor.dashboard')->with('success', 'Consultation saved! Patient has been notified.');
    }

    public function markVisited(Request $request, $appointment_id)
    {
        $appointment = \App\Models\Appointment::findOrFail($appointment_id);
        $doctor = Auth::user()->doctor;

        if ($appointment->doctor_id !== $doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Set called_at so the patient sees a "Doctor has called you!" notification
        $appointment->update([
            'called_at' => now(),
        ]);

        // Redirect to patient profile so doctor can consult immediately in the new tab
        return redirect()->route('doctor.patient.view', $appointment->patient_id)
            ->with('success', 'Patient called! Please complete the consultation in the "Live Consultation" tab.');
    }

    public function approveAppointment(Request $request, $appointment_id)
    {
        $doctor = Auth::user()->doctor;
        $appointment = \App\Models\Appointment::where('id', $appointment_id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->firstOrFail();

        $appointment->update([
            'status' => 'approved',
            'token_number' => 'TKN-' . strtoupper(\Illuminate\Support\Str::random(5))
        ]);

        return redirect()->back()->with('success', 'Appointment approved successfully. Patient added to queue.');
    }

    public function storeMedicalRecord(Request $request, $patient_id)
    {
        $validated = $request->validate([
            'type' => 'required|in:prescription,lab_test,document',
            'title' => 'required|string|max:255',
            'notes' => 'required|string',
        ]);

        $patient = \App\Models\Patient::findOrFail($patient_id);
        $doctor = Auth::user()->doctor;
        
        // Get the hospital where the doctor works
        $hospital = $doctor->hospital;

        $status = $validated['type'] === 'lab_test' ? 'pending' : 'completed';

        \App\Models\MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'hospital_id' => $hospital?->id,
            'record_type' => $validated['type'],
            'diagnosis' => $validated['title'],
            'medications_or_results' => $validated['notes'],
            'status' => $status,
            'date' => now(),
        ]);

        $message = $validated['type'] === 'lab_test' 
            ? "Lab test sent to {$hospital?->name}! Status: Pending" 
            : 'Medical record added successfully!';

        return redirect()->back()->with('success', $message);
    }

    public function viewEmergency($id)
    {
        $emergency = \App\Models\Emergency::with('patient')->findOrFail($id);
        $doctor = Auth::user()->doctor;

        if ($emergency->assigned_doctor_id !== $doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('doctor.emergency_view', compact('emergency'));
    }

    public function storeTriage(Request $request, $id)
    {
        $emergency = \App\Models\Emergency::findOrFail($id);
        $doctor = Auth::user()->doctor;

        if ($emergency->assigned_doctor_id !== $doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $validated = $request->validate([
            'notes' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
        ]);

        $emergency->update([
            'symptoms' => $emergency->symptoms . "\n\n--- Triage Notes ---\n" . $validated['notes'],
            'severity' => $validated['severity'],
        ]);

        return redirect()->back()->with('success', 'Triage notes added successfully.');
    }

    public function prescribeVaccine(Request $request, $patientId)
    {
        $validated = $request->validate([
            'vaccine_name'   => 'required|string',
            'dose_number'    => 'required|integer',
            'scheduled_date' => 'required|date',
        ]);

        $patient = \App\Models\Patient::findOrFail($patientId);

        \App\Models\Vaccination::create([
            'patient_id'   => $patient->id,
            'vaccine_name' => $validated['vaccine_name'] . " (Dose " . $validated['dose_number'] . ")",
            'due_date'     => $validated['scheduled_date'],
            'status'       => 'pending',
        ]);

        return redirect()->back()->with('success', 'Vaccination prescribed successfully!');
    }

    public function reviews()
    {
        $doctor = Auth::user()->doctor;
        
        // Fetch raw evaluations
        $evaluations = \App\Models\DoctorEvaluation::where('doctor_id', $doctor->id)
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Transform the data for the new unified interface
        $reviews = $evaluations->map(function($eval) {
            // Auto-generate tags based on rating and comment length for the new UI
            $tags = collect();
            if ($eval->rating_1_to_5 == 5) $tags->push('Excellent Care');
            if ($eval->rating_1_to_5 >= 4 && strlen($eval->feedback_text) > 20) $tags->push('Detailed Feedback');
            if ($eval->rating_1_to_5 <= 2) $tags->push('Needs Attention');

            return (object) [
                'id' => $eval->id,
                'patient' => $eval->patient,
                'rating' => $eval->rating_1_to_5,
                'comment' => $eval->feedback_text,
                'tags' => $tags,
                'created_at' => $eval->created_at,
            ];
        });
        
        // Compute Advanced Analytics
        $totalReviews = $reviews->count();
        $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;
        
        // Rating Distribution (5 to 1)
        $distribution = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];

        // Calculate percentages for the progress bars
        $distributionPercentages = [];
        foreach ($distribution as $stars => $count) {
            $distributionPercentages[$stars] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        }
        
        $positivePercentage = $totalReviews > 0 
            ? round(($reviews->where('rating', '>=', 4)->count() / $totalReviews) * 100) 
            : 0;
        
        return view('doctor.reviews', [
            'reviews' => $reviews,
            'totalReviews' => $totalReviews,
            'averageRating' => $averageRating,
            'distribution' => $distribution,
            'distributionPercentages' => $distributionPercentages,
            'positivePercentage' => $positivePercentage
        ]);
    }
}