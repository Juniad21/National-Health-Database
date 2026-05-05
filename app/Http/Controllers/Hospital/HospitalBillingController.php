<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\InsuranceClaim;
use App\Models\Patient;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalBillingController extends Controller
{
    public function index(Request $request)
    {
        $hospital = Auth::user()->hospital;

        $query = Bill::where('hospital_id', $hospital->id)->with('patient');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('bill_number', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('hospital.billing.index', compact('bills'));
    }

    public function create()
    {
        // For simplicity, fetch all patients. In a real system, you'd likely fetch admitted patients or search via AJAX.
        $patients = Patient::all();
        return view('hospital.billing.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $hospital = Auth::user()->hospital;

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'consultation_fee' => 'required|numeric|min:0',
            'lab_fee' => 'required|numeric|min:0',
            'medicine_fee' => 'required|numeric|min:0',
            'room_fee' => 'required|numeric|min:0',
            'emergency_fee' => 'required|numeric|min:0',
            'other_charges' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'issued_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        // Calculate totals
        $subtotal = $validated['consultation_fee'] + $validated['lab_fee'] + $validated['medicine_fee'] + 
                    $validated['room_fee'] + $validated['emergency_fee'] + $validated['other_charges'];
        $total = max(0, $subtotal - $validated['discount']);

        $bill = Bill::create([
            'hospital_id' => $hospital->id,
            'patient_id' => $validated['patient_id'],
            'bill_number' => 'BILL-' . strtoupper(uniqid()),
            'consultation_fee' => $validated['consultation_fee'],
            'lab_fee' => $validated['lab_fee'],
            'medicine_fee' => $validated['medicine_fee'],
            'room_fee' => $validated['room_fee'],
            'emergency_fee' => $validated['emergency_fee'],
            'other_charges' => $validated['other_charges'],
            'discount' => $validated['discount'],
            'total_amount' => $total,
            'paid_amount' => 0.00,
            'due_amount' => $total,
            'payment_status' => 'unpaid',
            'issued_date' => $validated['issued_date'],
            'notes' => $validated['notes'],
        ]);

        AuditLogService::logAction('bill created', "Created bill {$bill->bill_number} for total $total", 'billing', 'medium', Bill::class, $bill->id);

        // Auto-create insurance claim if patient has insurance
        $patient = \App\Models\Patient::find($validated['patient_id']);
        if ($patient && $patient->insurance_provider) {
            $claim = \App\Models\InsuranceClaim::create([
                'hospital_id' => $hospital->id,
                'patient_id' => $patient->id,
                'bill_id' => $bill->id,
                'claim_amount' => $total,
                'insurance_provider' => $patient->insurance_provider,
                'policy_number' => $patient->insurance_policy_number ?? 'N/A',
                'claim_status' => 'pending',
                'remarks' => "Auto-generated claim from patient profile policy: {$patient->insurance_policy_number}",
            ]);
            
            AuditLogService::logAction('claim submitted', "Auto-submitted claim for Bill {$bill->bill_number} to {$patient->insurance_provider}", 'billing', 'medium', \App\Models\InsuranceClaim::class, $claim->id);
            
            return redirect()->route('hospital.billing.index')->with('success', "Bill created successfully. An automatic insurance claim to {$patient->insurance_provider} has been generated.");
        }

        return redirect()->route('hospital.billing.index')->with('success', 'Bill created successfully.');
    }

    public function updatePayment(Request $request, $id)
    {
        $hospital = Auth::user()->hospital;
        $bill = Bill::where('hospital_id', $hospital->id)->findOrFail($id);

        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $newPaidAmount = $validated['paid_amount'];
        $dueAmount = max(0, $bill->total_amount - $newPaidAmount);
        
        $status = 'unpaid';
        if ($newPaidAmount > 0 && $dueAmount > 0) {
            $status = 'partially_paid';
        } elseif ($newPaidAmount > 0 && $dueAmount == 0) {
            $status = 'paid';
        }

        $bill->update([
            'paid_amount' => $newPaidAmount,
            'due_amount' => $dueAmount,
            'payment_status' => $status,
        ]);

        AuditLogService::logAction('payment status updated', "Updated payment for bill {$bill->bill_number} to $status", 'billing', 'low', Bill::class, $bill->id);

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    public function claims(Request $request)
    {
        $hospital = Auth::user()->hospital;

        $query = InsuranceClaim::where('hospital_id', $hospital->id)->with(['patient', 'bill']);

        if ($request->filled('status')) {
            $query->where('claim_status', $request->input('status'));
        }

        $claims = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('hospital.billing.claims', compact('claims'));
    }

    public function submitClaim(Request $request, $bill_id)
    {
        $hospital = Auth::user()->hospital;
        $bill = Bill::where('hospital_id', $hospital->id)->findOrFail($bill_id);

        $validated = $request->validate([
            'insurance_provider' => 'required|string|max:255',
            'policy_number' => 'required|string|max:255',
            'claim_amount' => 'required|numeric|min:1',
            'remarks' => 'nullable|string'
        ]);

        $claim = InsuranceClaim::create([
            'bill_id' => $bill->id,
            'patient_id' => $bill->patient_id,
            'hospital_id' => $hospital->id,
            'insurance_provider' => $validated['insurance_provider'],
            'policy_number' => $validated['policy_number'],
            'claim_amount' => $validated['claim_amount'],
            'claim_status' => 'pending',
            'remarks' => $validated['remarks'],
        ]);

        AuditLogService::logAction('insurance claim submitted', "Submitted claim for {$validated['insurance_provider']}", 'billing', 'medium', InsuranceClaim::class, $claim->id);

        return redirect()->back()->with('success', 'Insurance claim submitted successfully.');
    }

    public function updateClaimStatus(Request $request, $id)
    {
        $hospital = Auth::user()->hospital;
        $claim = InsuranceClaim::where('hospital_id', $hospital->id)->findOrFail($id);

        $validated = $request->validate([
            'claim_status' => 'required|in:pending,approved,rejected',
            'approved_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string'
        ]);

        $oldStatus = $claim->claim_status;
        $claim->update([
            'claim_status' => $validated['claim_status'],
            'approved_amount' => $validated['approved_amount'] ?? $claim->approved_amount,
            'remarks' => $validated['remarks'] ?? $claim->remarks,
        ]);

        // Automatically update bill payment if claim is approved
        if ($validated['claim_status'] === 'approved' && $oldStatus !== 'approved') {
            $bill = $claim->bill;
            if ($bill) {
                $approvedAmount = $validated['approved_amount'] ?? 0;
                $bill->paid_amount += $approvedAmount;
                $bill->due_amount = max(0, $bill->total_amount - $bill->paid_amount);
                
                if ($bill->due_amount <= 0) {
                    $bill->payment_status = 'paid';
                } elseif ($bill->paid_amount > 0) {
                    $bill->payment_status = 'partially_paid';
                }
                $bill->save();
            }
        }

        AuditLogService::logAction('insurance claim status updated', "Updated claim status to {$validated['claim_status']}", 'billing', 'medium', InsuranceClaim::class, $claim->id);

        return redirect()->back()->with('success', 'Insurance claim status updated successfully.');
    }
}
