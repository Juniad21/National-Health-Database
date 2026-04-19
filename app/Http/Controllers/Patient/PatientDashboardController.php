<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PatientDashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        
        $healthMetrics = \App\Models\HealthMetric::where('patient_id', $patient->id)
            ->orderBy('recorded_date', 'desc')
            ->get();
            
        $vaccinations = \App\Models\Vaccination::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $urgentBloodRequests = \App\Models\BloodRequest::where('blood_group_needed', $patient->blood_group)
            ->where('status', 'urgent')
            ->with('hospital')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('patient.dashboard', [
            'healthMetrics' => $healthMetrics,
            'vaccinations' => $vaccinations,
            'urgentBloodRequests' => $urgentBloodRequests
        ]);
    }

    public function scheduling()
    {
        $patient = Auth::user()->patient;
        
        $appointments = \App\Models\Appointment::where('patient_id', $patient->id)
            ->with(['doctor', 'hospital'])
            ->orderBy('date', 'desc')
            ->get();
        
        // Get all available doctors for scheduling, with hospital and user details
        $doctors = \App\Models\Doctor::with(['hospital', 'user'])
            ->orderBy('id', 'asc')
            ->get();
        
        return view('patient.scheduling', [
            'appointments' => $appointments,
            'doctors' => $doctors,
        ]);
    }

    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
        ]);

        $doctor = \App\Models\Doctor::with('hospital')->findOrFail($validated['doctor_id']);
        $hospitalId = $doctor->hospital_id;

        $patient = Auth::user()->patient;

        \App\Models\Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'hospital_id' => $hospitalId,
            'date' => $validated['date'],
            'time_slot' => $validated['time_slot'],
            'status' => 'Pending',
            'booking_id' => 'BK-' . Str::upper(Str::random(12)),
        ]);

        return redirect()->route('patient.scheduling')->with('success', 'Appointment requested successfully. The doctor will approve it shortly.');
    }

    public function medicalRecords()
    {
        $patient = Auth::user()->patient;
        $records = \App\Models\MedicalRecord::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('date', 'desc')
            ->get();
        
        return view('patient.medical_records', [
            'records' => $records
        ]);
    }

    public function consents()
    {
        $patient = Auth::user()->patient;
        $accessRequests = \App\Models\AccessRequest::where('patient_id', $patient->id)
            ->with('doctor')
            ->get();
        
        return view('patient.consents', [
            'accessRequests' => $accessRequests
        ]);
    }

    public function updateConsent(Request $request)
    {
        $validated = $request->validate([
            'consent_id' => 'required|exists:access_requests,id',
            'status' => 'required|in:approved,rejected',
        ]);

        $accessRequest = \App\Models\AccessRequest::findOrFail($validated['consent_id']);
        $patient = Auth::user()->patient;

        if ($accessRequest->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $accessRequest->update(['status' => $validated['status']]);

        return redirect()->route('patient.consents')->with('success', 'Access permission updated successfully!');
    }

    public function symptomAssessment(Request $request)
    {
        $patient = Auth::user()->patient;

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'symptoms' => 'required|string',
                'severity' => 'required|in:mild,moderate,severe',
            ]);

            // Store symptom assessment (you may need to create a model for this)
            \App\Models\MedicalRecord::create([
                'patient_id' => $patient->id,
                'record_type' => 'assessment',
                'diagnosis' => $validated['symptoms'],
                'medications_or_results' => 'Severity: ' . $validated['severity'],
                'date' => now(),
            ]);

            return redirect()->back()->with('success', 'Symptom assessment submitted!');
        }

        return view('patient.symptoms');
    }

    public function approveAccessRequest($id)
    {
        $request = \App\Models\AccessRequest::findOrFail($id);
        $patient = Auth::user()->patient;

        if ($request->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->update(['status' => 'approved']);

        return redirect()->back()->with('success', 'Access request approved!');
    }

    public function rejectAccessRequest($id)
    {
        $request = \App\Models\AccessRequest::findOrFail($id);
        $patient = Auth::user()->patient;

        if ($request->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Access request rejected!');
    }

    public function markVaccineTaken($id)
    {
        $vaccine = \App\Models\Vaccination::findOrFail($id);
        $patient = Auth::user()->patient;

        if ($vaccine->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $vaccine->update(['status' => 'taken']);

        return redirect()->back()->with('success', 'Vaccine marked as taken!');
    }

    public function triggerEmergency()
    {
        $patient = Auth::user()->patient;

        \App\Models\Emergency::create([
            'patient_id' => $patient->id,
            'hospital_id' => null,
            'status' => 'active',
            'latitude' => null,
            'longitude' => null,
        ]);

        return redirect()->back()->with('success', 'Emergency alert triggered! Help is on the way.');
    }

    public function storeEvaluation(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback_text' => 'nullable|string',
        ]);

        $appointment = \App\Models\Appointment::findOrFail($validated['appointment_id']);
        $patient = Auth::user()->patient;

        if ($appointment->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        \App\Models\DoctorEvaluation::create([
            'appointment_id' => $validated['appointment_id'],
            'rating' => $validated['rating'],
            'feedback_text' => $validated['feedback_text'],
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
}