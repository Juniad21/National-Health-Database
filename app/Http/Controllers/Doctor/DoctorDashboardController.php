<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\AccessRequest;
use App\Models\MedicalRecord;
use App\Models\LabTestCatalog;
use App\Models\LabOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'doctor')
            abort(403);
        $doctor = $user->doctor;

        $search = $request->input('search');
        $patientResults = null;
        if ($search) {
            $patientResults = Patient::where('nid', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->get();
        }

        // Fetch today's queue
        $queue = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'pending')
            ->orderBy('token_number', 'asc')
            ->get();

        return view('doctor.dashboard', compact('doctor', 'queue', 'patientResults', 'search'));
    }

    public function requestAccess(Request $request, $id)
    {
        $doctor = Auth::user()->doctor;
        $patient = Patient::findOrFail($id);

        AccessRequest::updateOrCreate(
            ['doctor_id' => $doctor->id, 'patient_id' => $patient->id],
            ['status' => 'pending']
        );

        return back()->with('success', 'Access request sent to patient.');
    }

    public function viewPatient($id)
    {
        $doctor = Auth::user()->doctor;
        $patient = Patient::findOrFail($id);

        $hasAccess = AccessRequest::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('status', 'approved')
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('doctor.dashboard')->with('error', 'You do not have access to this patient record. Please request access first.');
        }

        $records = MedicalRecord::where('patient_id', $patient->id)->orderBy('date', 'desc')->get()->groupBy('record_type');

        return view('doctor.patient_view', compact('patient', 'records', 'doctor'));
    }

    public function consultation($patient_id)
    {
        $doctor = Auth::user()->doctor;
        $patient = Patient::findOrFail($patient_id);

        $hasAccess = AccessRequest::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->where('status', 'approved')
            ->exists();

        if (!$hasAccess)
            return abort(403, 'Unauthorized access.');

        $labTests = LabTestCatalog::all();

        return view('doctor.consultation', compact('patient', 'labTests'));
    }

    public function storeConsultation(Request $request, $patient_id)
    {
        $doctor = Auth::user()->doctor;
        $patient = Patient::findOrFail($patient_id);

        $request->validate([
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required|string',
            'medications.*.dosage' => 'required|string',
            'medications.*.duration' => 'required|string',
            'medications.*.instructions' => 'required|string',
            'lab_test_ids' => 'nullable|array',
            'lab_test_ids.*' => 'exists:lab_test_catalogs,id'
        ]);

        $medicationString = "Clinical Notes: \n" . ($request->notes ?? 'None') . "\n\nPrescription:\n";
        if ($request->has('medications') && is_array($request->medications) && count($request->medications) > 0) {
            foreach ($request->medications as $med) {
                $medicationString .= "- {$med['name']} ({$med['dosage']}) for {$med['duration']}. {$med['instructions']}\n";
            }
        } else {
            $medicationString .= "No specific medications prescribed.\n";
        }

        // Create Medical Record (E-Prescription)
        MedicalRecord::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'record_type' => 'prescription',
            'diagnosis' => $request->diagnosis,
            'medications_or_results' => $medicationString,
            'date' => Carbon::today()
        ]);

        // Place Lab Orders if requested
        if ($request->has('lab_test_ids') && is_array($request->lab_test_ids)) {
            foreach ($request->lab_test_ids as $test_id) {
                LabOrder::create([
                    'patient_id' => $patient->id,
                    'doctor_id' => $doctor->id,
                    'hospital_id' => $doctor->hospital_id,
                    'lab_test_catalog_id' => $test_id,
                    'status' => 'pending'
                ]);
            }
        }

        // Mark today's appointment as completed
        $appointment = Appointment::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'pending')
            ->first();

        if ($appointment) {
            $appointment->update(['status' => 'completed']);
        }

        return redirect()->route('doctor.dashboard')->with('success', 'Consultation completed and saved successfully.');
    }
}
