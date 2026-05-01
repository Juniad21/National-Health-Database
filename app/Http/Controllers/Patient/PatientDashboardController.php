<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PatientDashboardController extends Controller
{
    protected array $symptomSpecialties = [
        'Neurology' => ['headache', 'migraine', 'dizzy'],
        'General Medicine' => ['fever', 'cough', 'cold', 'flu'],
        'Orthopedics' => ['leg pain', 'arm pain', 'bone', 'joint'],
        'Cardiology' => ['chest pain', 'heart', 'breath'],
        'Gastroenterology' => ['stomach', 'digestion', 'vomit'],
        'Endocrinology' => ['diabetes', 'sugar', 'thyroid'],
        'Dermatology' => ['skin', 'rash', 'itch'],
    ];

    public function index()
    {
        $patient = Auth::user()->patient;
        
        $healthMetrics = \App\Models\PatientHealthMetric::where('patient_id', $patient->id)
            ->orderBy('recorded_at', 'desc')
            ->get();
            
        $latestHealthMetric = $healthMetrics->first();
            
        $vaccinations = \App\Models\Vaccination::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Patient is ONLY 'up to date' if they have ZERO 'pending', 'due', or 'overdue' doses
        $upcomingVaccine = \App\Models\Vaccination::where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'due', 'overdue'])
            ->orderBy('due_date', 'asc')
            ->first();
            
        $urgentBloodRequests = \App\Models\BloodRequest::where('blood_group_needed', $patient->blood_group)
            ->where('status', 'urgent')
            ->with('hospital')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('patient.dashboard', [
            'healthMetrics' => $healthMetrics,
            'latestHealthMetric' => $latestHealthMetric,
            'vaccinations' => $vaccinations,
            'upcomingVaccine' => $upcomingVaccine,
            'urgentBloodRequests' => $urgentBloodRequests
        ]);
    }

    public function vaccinations()
    {
        $patient = Auth::user()->patient;
        $vaccinations = \App\Models\Vaccination::where('patient_id', $patient->id)
            ->orderBy('due_date', 'asc')
            ->get();

        return view('patient.vaccinations', [
            'vaccinations' => $vaccinations
        ]);
    }

    public function scheduling()
    {
        $patient = Auth::user()->patient;
        
        $appointments = \App\Models\Appointment::where('patient_id', $patient->id)
            ->with(['doctor', 'hospital'])
            ->orderBy('date', 'desc')
            ->get();
        
        // Get all available doctors for scheduling, with hospital and user details, including average rating
        $doctors = \App\Models\Doctor::with(['hospital', 'user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
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
            'status' => 'pending',
            'booking_id' => 'BK-' . Str::upper(Str::random(12)),
        ]);

        return redirect()->route('patient.scheduling')->with('success', 'Appointment requested successfully. The doctor will approve it shortly.');
    }

    public function medicalRecords()
    {
        $patient = Auth::user()->patient;

        \App\Services\AuditLogService::logAction(
            action: 'patient record viewed',
            description: "Patient viewed their own medical records",
            module: 'medical',
            severity: 'low',
            targetType: \App\Models\Patient::class,
            targetId: $patient->id
        );

        $allRecords = \App\Models\MedicalRecord::where('patient_id', $patient->id)
            ->with('doctor')
            ->orderBy('date', 'desc')
            ->get();
        
        $labOrders = \App\Models\LabOrder::where('patient_id', $patient->id)
            ->where('status', 'completed')
            ->with(['doctor', 'hospital', 'labTestCatalog'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Group records by type
        $records = $allRecords->groupBy('record_type');
        
        return view('patient.medical_records', [
            'records' => $records,
            'labOrders' => $labOrders
        ]);
    }

    public function bills()
    {
        $patient = Auth::user()->patient;
        $bills = \App\Models\Bill::where('patient_id', $patient->id)
            ->with('hospital')
            ->orderBy('issued_date', 'desc')
            ->get();
            
        return view('patient.bills', [
            'bills' => $bills
        ]);
    }

    public function payBill(Request $request, $id)
    {
        $bill = \App\Models\Bill::findOrFail($id);
        $patient = Auth::user()->patient;

        if ($bill->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Mock payment processing
        $bill->update([
            'paid_amount' => $bill->total_amount,
            'due_amount' => 0,
            'payment_status' => 'paid',
        ]);

        \App\Services\AuditLogService::logAction(
            action: 'payment recorded',
            description: "Patient paid bill #{$bill->bill_number} of amount {$bill->total_amount}",
            module: 'billing',
            severity: 'medium',
            targetType: \App\Models\Bill::class,
            targetId: $bill->id
        );

        return redirect()->back()->with('success', 'Payment successful! Your bill has been marked as paid.');
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

        \App\Services\AuditLogService::logAction(
            action: 'compliance status changed',
            description: "Patient updated consent status to {$validated['status']} for Dr. {$accessRequest->doctor->first_name}",
            module: 'compliance',
            severity: 'medium',
            targetType: \App\Models\AccessRequest::class,
            targetId: $accessRequest->id
        );

        return redirect()->route('patient.consents')->with('success', 'Access permission updated successfully!');
    }

    public function symptomAssessment(Request $request)
    {
        $patient = Auth::user()->patient;
        $suggestedSpecialty = null;

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'description' => 'required|string',
                'severity' => 'required|in:mild,moderate,severe',
            ]);

            $description = strtolower($validated['description']);

            foreach ($this->symptomSpecialties as $specialty => $keywords) {
                foreach ($keywords as $keyword) {
                    if (str_contains($description, $keyword)) {
                        $suggestedSpecialty = $specialty;
                        break 2;
                    }
                }
            }

            if (! $suggestedSpecialty) {
                $suggestedSpecialty = 'General Medicine';
            }

            return view('patient.symptoms', [
                'suggestedSpecialty' => $suggestedSpecialty,
            ])->with('success', 'Symptom assessment submitted!');
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

    public function emergencyHistory()
    {
        $patient = Auth::user()->patient;
        $emergencies = \App\Models\Emergency::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patient.emergency.history', compact('emergencies'));
    }

    public function emergencySos()
    {
        return view('patient.emergency.sos');
    }

    public function triggerEmergency(Request $request)
    {
        $patient = Auth::user()->patient;

        $validated = $request->validate([
            'emergency_type' => 'required|string',
            'severity' => 'required|string',
            'symptoms' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string',
            'contact_number' => 'required|string',
            'guardian_contact' => 'nullable|string',
        ]);

        $emergency = \App\Models\Emergency::create([
            'patient_id' => $patient->id,
            'status' => 'Sent',
            'emergency_type' => $validated['emergency_type'],
            'severity' => $validated['severity'],
            'symptoms' => $validated['symptoms'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['address'],
            'contact_number' => $validated['contact_number'],
            'guardian_contact' => $validated['guardian_contact'],
            'created_by' => Auth::id(),
        ]);

        \App\Services\AuditLogService::logAction(
            action: 'emergency triggered',
            description: "Patient triggered a {$validated['severity']} emergency: {$validated['emergency_type']}",
            module: 'emergency',
            severity: 'critical',
            targetType: \App\Models\Emergency::class,
            targetId: $emergency->id
        );

        return redirect()->route('patient.emergency.view', $emergency->id)->with('success', 'Emergency alert triggered! Help is on the way.');
    }

    public function viewEmergency($id)
    {
        $emergency = \App\Models\Emergency::with(['hospital', 'doctor', 'ambulanceStaff'])->findOrFail($id);
        $patient = Auth::user()->patient;

        if ($emergency->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        return view('patient.emergency.view', compact('emergency'));
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
            'doctor_id' => $appointment->doctor_id,
            'patient_id' => $appointment->patient_id,
            'rating_1_to_5' => $validated['rating'],
            'feedback_text' => $validated['feedback_text'],
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    public function submitReview(Request $request, $appointmentId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $appointment = \App\Models\Appointment::findOrFail($appointmentId);
        $patient = \Illuminate\Support\Facades\Auth::user()->patient;

        if ($appointment->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if (strtolower($appointment->status) !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed appointments.');
        }

        \App\Models\DoctorReview::create([
            'patient_id' => $patient->id,
            'doctor_id' => $appointment->doctor_id,
            'appointment_id' => $appointment->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'Thank you for your review!');
    }
}