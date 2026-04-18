<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\PatientConsent;
use App\Models\HealthMetric;
use App\Models\BloodRequest;
use App\Models\AccessRequest;
use App\Models\Vaccination;
use App\Models\Emergency;
use App\Models\DoctorEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientDashboardController extends Controller
{
    /**
     * Helper to safely retrieve the patient profile or abort with a redirect.
     */
    private function getPatientProfile()
    {
        $user = Auth::user();
        
        // If they are not a patient, send them to their role dashboard
        if ($user->role !== 'patient') {
            abort(redirect()->route($user->role . '.dashboard')->send());
        }

        $patient = $user->patient;
        
        // Ensure patient profile exists
        if (!$patient) {
            abort(redirect()->route('dashboard')->with('error', 'No patient profile found. Please contact administration.')->send());
        }

        return $patient;
    }

    public function index(Request $request)
    {
        $patient = $this->getPatientProfile();
        $user = Auth::user();

        // Fetch Data for the Dashboard Views (HealthMetrics still keyed to user.id based on old schema)
        $healthMetrics = HealthMetric::where('patient_id', $user->id)->orderBy('recorded_date', 'desc')->take(5)->get();

        // Fetch matching BloodRequests if the patient has a blood group
        $urgentBloodRequests = [];
        if ($patient->blood_group) {
            $urgentBloodRequests = BloodRequest::with('hospital')
                ->where('blood_group_needed', $patient->blood_group)
                ->where('status', 'active')
                ->get();
        }

        // Fetch Vaccinations
        $vaccinations = Vaccination::where('patient_id', $patient->id)->orderBy('due_date', 'asc')->get();

        // Return a combined dashboard view (could optionally split into multiple views later)
        return view('patient.dashboard', compact('patient', 'user', 'healthMetrics', 'urgentBloodRequests', 'vaccinations'));
    }

    // Feature 1: Appointment Scheduling & Search
    public function scheduling(Request $request)
    {
        $patient = $this->getPatientProfile();

        // Fetch all doctors with their hospital to allow client-side filtering via Alpine.js
        $doctors = Doctor::with(['user', 'hospital'])->get();
        // Fetch hospitals
        $hospitals = Hospital::with('user')->get();

        $appointments = Appointment::with(['doctor.user', 'hospital'])
            ->where('patient_id', $patient->id)
            ->orderBy('date', 'asc')
            ->get();

        return view('patient.scheduling', compact('doctors', 'hospitals', 'appointments'));
    }

    public function storeAppointment(Request $request)
    {
        $patient = $this->getPatientProfile();

        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'appointment_date' => 'required|date',
            'time_slot' => 'required|string',
        ]);

        // Check for conflicts
        $conflict = Appointment::where('doctor_id', $request->doctor_id)
            ->where('date', $request->appointment_date)
            ->where('time_slot', $request->time_slot)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            return back()->with('error', 'This time slot is already booked for the selected doctor.');
        }

        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'hospital_id' => $request->hospital_id,
            'date' => $request->appointment_date,
            'time_slot' => $request->time_slot,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Appointment successfully requested.');
    }

    // Feature 2: Medical Records & Prescriptions
    public function medicalRecords()
    {
        $patient = $this->getPatientProfile();

        $records = MedicalRecord::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('record_type');

        return view('patient.medical_records', compact('records'));
    }

    // Feature 1: Consent & Access Control
    public function consents()
    {
        $patient = $this->getPatientProfile();
        $accessRequests = AccessRequest::with('doctor.user')
            ->where('patient_id', $patient->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('patient.consents', compact('accessRequests'));
    }

    public function updateConsent(Request $request)
    {
        $patient = $this->getPatientProfile();
        
        $request->validate([
            'doctor_id' => 'required|exists:users,id', // or doctors,id depending on patient_consents table
            'status' => 'required|in:granted,revoked',
        ]);

        // Using Auth::id() or patient id depending on model schema. Assuming the intent was user id or patient id.
        PatientConsent::updateOrCreate(
            ['patient_id' => Auth::id(), 'doctor_id' => $request->doctor_id],
            ['status' => $request->status]
        );

        return back()->with('success', 'Consent updated.');
    }

    // Feature 5: Symptom Assessment Tool
    public function symptomAssessment(Request $request)
    {
        // Require patient profile
        $this->getPatientProfile();

        $suggestion = null;
        if ($request->isMethod('post')) {
            $symptoms = strtolower($request->input('symptoms'));

            // Robust dictionary mapping keywords to diseases and specialties
            // Order is important: more complex/critical combinations first
            $mapping = [
                ['keywords' => ['chest pain', 'shortness of breath'], 'suggestion' => 'Urgent: Possible Cardiac or Severe Respiratory Issue. Recommended Specialty: Cardiology / Pulmonology'],
                ['keywords' => ['chest pain'], 'suggestion' => 'Urgent: Possible Cardiac Issue. Recommended Specialty: Cardiology'],
                ['keywords' => ['shortness of breath'], 'suggestion' => 'Urgent: Possible Respiratory Issue. Recommended Specialty: Pulmonology'],
                ['keywords' => ['fever', 'headache', 'joint pain'], 'suggestion' => 'Possible Dengue or Chikungunya. Recommended Specialty: Internal Medicine'],
                ['keywords' => ['fever', 'headache'], 'suggestion' => 'Possible Viral Infection or Dengue. Recommended Specialty: Internal Medicine'],
                ['keywords' => ['vomiting', 'stomach ache'], 'suggestion' => 'Possible Food Poisoning or Gastroenteritis. Recommended Specialty: Gastroenterology'],
                ['keywords' => ['dizzy', 'vomiting'], 'suggestion' => 'Possible Food Poisoning, Dehydration, or Vertigo. Recommended Specialty: Gastroenterology / Neurology'],
                ['keywords' => ['fever', 'cough'], 'suggestion' => 'Possible Respiratory Infection or Flu. Recommended Specialty: Internal Medicine / Pulmonology'],
                ['keywords' => ['joint pain'], 'suggestion' => 'Possible Arthritis or Viral after-effects. Recommended Specialty: Orthopedics / Rheumatology'],
                ['keywords' => ['fever'], 'suggestion' => 'Possible Viral Infection. Recommended Specialty: Internal Medicine'],
                ['keywords' => ['headache'], 'suggestion' => 'Possible Tension Headache or Migraine. Recommended Specialty: Neurology or General Medicine'],
                ['keywords' => ['dizzy'], 'suggestion' => 'Possible Vertigo or Low Blood Pressure. Recommended Specialty: General Medicine'],
                ['keywords' => ['vomiting'], 'suggestion' => 'Possible Gastric Issue or Infection. Recommended Specialty: Gastroenterology'],
                ['keywords' => ['stomach ache'], 'suggestion' => 'Possible Gastric Issue. Recommended Specialty: Gastroenterology'],
                ['keywords' => ['cough'], 'suggestion' => 'Possible Respiratory Infection. Recommended Specialty: Pulmonology or Internal Medicine'],
            ];

            foreach ($mapping as $rule) {
                $allMatched = true;
                foreach ($rule['keywords'] as $keyword) {
                    if (!str_contains($symptoms, $keyword)) {
                        $allMatched = false;
                        break;
                    }
                }

                if ($allMatched) {
                    $suggestion = $rule['suggestion'];
                    break;
                }
            }

            if (!$suggestion) {
                $suggestion = 'Symptoms unclear. Recommended Specialty: General Practice (Please consult a doctor for a proper diagnosis)';
            }
        }

        return view('patient.symptoms', compact('suggestion'));
    }

    // Feature 1: Patient Consent & Data Access Control
    public function approveAccessRequest(Request $request, $id)
    {
        $patient = $this->getPatientProfile();
        $accessRequest = AccessRequest::findOrFail($id);

        if ($accessRequest->patient_id !== $patient->id)
            abort(403);

        $accessRequest->update(['status' => 'approved']);
        return back()->with('success', 'Access Request Approved.');
    }

    public function rejectAccessRequest(Request $request, $id)
    {
        $patient = $this->getPatientProfile();
        $accessRequest = AccessRequest::findOrFail($id);

        if ($accessRequest->patient_id !== $patient->id)
            abort(403);

        $accessRequest->update(['status' => 'revoked']);
        return back()->with('success', 'Access Request Rejected.');
    }

    // Feature 4: Vaccination & Immunization Tracker
    public function markVaccineTaken(Request $request, $id)
    {
        $patient = $this->getPatientProfile();
        $vaccine = Vaccination::findOrFail($id);

        if ($vaccine->patient_id !== $patient->id)
            abort(403);

        $vaccine->update(['status' => 'taken']);
        return back()->with('success', 'Vaccine marked as taken.');
    }

    // Feature 7: Emergency Alert
    public function triggerEmergency(Request $request)
    {
        $patient = $this->getPatientProfile();

        Emergency::create([
            'patient_id' => $patient->id,
            'status' => 'active'
        ]);

        return back()->with('success', 'SOS Alert sent to nearby hospitals.');
    }

    // Feature 8: Doctor Performance Evaluation
    public function storeEvaluation(Request $request)
    {
        $patient = $this->getPatientProfile();

        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback_text' => 'nullable|string'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        if ($appointment->patient_id !== $patient->id)
            abort(403);

        DoctorEvaluation::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'patient_id' => $patient->id,
            'rating_1_to_5' => $request->rating,
            'feedback_text' => $request->feedback_text
        ]);

        return back()->with('success', 'Thank you for rating your visit!');
    }
}
