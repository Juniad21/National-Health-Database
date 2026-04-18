<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;
        
        $queue = \App\Models\Appointment::where('doctor_id', $doctor->id)
            ->whereDate('date', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->with('patient')
            ->orderBy('time_slot', 'asc')
            ->get();
            
        // Add token numbers
        $queue = $queue->each(function ($appointment, $key) {
            $appointment->token_number = $key + 1;
        });
        
        return view('doctor.dashboard', [
            'queue' => $queue
        ]);
    }

    public function viewPatient($id)
    {
        $patient = \App\Models\Patient::findOrFail($id);
        $doctor = Auth::user()->doctor;

        $medicalRecords = \App\Models\MedicalRecord::where('patient_id', $patient->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('doctor.patient_view', [
            'patient' => $patient,
            'medicalRecords' => $medicalRecords
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
}