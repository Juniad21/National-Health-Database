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
            ->where('status', 'pending')
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
        
        return view('doctor.dashboard', [
            'waitingQueue' => $waitingQueue,
            'pendingApprovals' => $pendingApprovals,
            'search' => $search,
            'patientResults' => $patientResults,
            'doctor' => $doctor,
        ]);
    }

    public function viewPatient($id)
    {
        $patient = \App\Models\Patient::findOrFail($id);
        $doctor = Auth::user()->doctor;

        $medicalRecords = \App\Models\MedicalRecord::where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->get();

        // Group records by type for display
        $records = $medicalRecords->groupBy('record_type');

        return view('doctor.patient_view', [
            'patient' => $patient,
            'records' => $records
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
        $patient = \App\Models\Patient::findOrFail($patient_id);
        $doctor = Auth::user()->doctor;

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
            'diagnosis' => 'required|string',
            'medications_or_results' => 'required|string',
        ]);

        $patient = \App\Models\Patient::findOrFail($patient_id);
        $doctor = Auth::user()->doctor;

        \App\Models\MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'record_type' => 'consultation',
            'diagnosis' => $validated['diagnosis'],
            'medications_or_results' => $validated['medications_or_results'],
            'date' => now(),
        ]);

        return redirect()->back()->with('success', 'Consultation notes saved!');
    }

    public function markVisited(Request $request, $appointment_id)
    {
        $appointment = \App\Models\Appointment::findOrFail($appointment_id);
        $doctor = Auth::user()->doctor;

        if ($appointment->doctor_id !== $doctor->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $appointment->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Patient marked as visited!');
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
}