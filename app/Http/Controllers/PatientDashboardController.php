<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\PatientConsent;
use App\Models\HealthMetric;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'patient')
            abort(403);

        $patient = $user->patient;

        // Fetch Data for the Dashboard Views (HealthMetrics still keyed to user.id based on old schema)
        $healthMetrics = HealthMetric::where('patient_id', $user->id)->orderBy('recorded_date', 'desc')->take(5)->get();

        // Fetch matching BloodRequests if the patient has a blood group
        $urgentBloodRequests = [];
        if ($patient && $patient->blood_group) {
            $urgentBloodRequests = BloodRequest::with('hospital')
                ->where('blood_group_needed', $patient->blood_group)
                ->where('status', 'active')
                ->get();
        }

        // Return a combined dashboard view (could optionally split into multiple views later)
        return view('patient.dashboard', compact('patient', 'user', 'healthMetrics', 'urgentBloodRequests'));
    }

    // Feature 1: Appointment Scheduling & Search
    public function scheduling(Request $request)
    {
        // Fetch all doctors with their hospital to allow client-side filtering via Alpine.js
        $doctors = Doctor::with(['user', 'hospital'])->get();
        // Fetch hospitals
        $hospitals = Hospital::with('user')->get();

        $patientId = Auth::user()->patient->id;

        $appointments = Appointment::with(['doctor.user', 'hospital'])
            ->where('patient_id', $patientId)
            ->orderBy('date', 'asc')
            ->get();

        return view('patient.scheduling', compact('doctors', 'hospitals', 'appointments'));
    }

    public function storeAppointment(Request $request)
    {
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
            'patient_id' => Auth::user()->patient->id,
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
        $patientId = Auth::user()->patient->id;

        $records = MedicalRecord::with('doctor.user')
            ->where('patient_id', $patientId)
            ->orderBy('date', 'desc')
            ->get()
            ->groupBy('record_type');

        return view('patient.medical_records', compact('records'));
    }

    // Feature 3: Consent & Access Control
    public function consents()
    {
        $doctors = Doctor::with('user')->get();
        // Consents table still keyed to users(id) based on old schema unless migrated too
        $consents = PatientConsent::where('patient_id', Auth::id())->get()->keyBy('doctor_id');

        return view('patient.consents', compact('doctors', 'consents'));
    }

    public function updateConsent(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id', // or doctors,id depending on patient_consents table
            'status' => 'required|in:granted,revoked',
        ]);

        PatientConsent::updateOrCreate(
            ['patient_id' => Auth::id(), 'doctor_id' => $request->doctor_id],
            ['status' => $request->status]
        );

        return back()->with('success', 'Consent updated.');
    }

    // Feature 5: Symptom Assessment Tool
    public function symptomAssessment(Request $request)
    {
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
}
