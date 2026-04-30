<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;

class HospitalDashboardController extends Controller
{
    public function index()
    {
        $hospital = Auth::user()->hospital;
        
        $emergencies = \App\Models\Emergency::where('hospital_id', $hospital->id)
            ->where('status', '!=', 'resolved')
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $resources = \App\Models\HospitalResource::where('hospital_id', $hospital->id)->get();
        
        // Combined the fixes: Eager loading multiple relationships AND ordering them
        $pendingLabs = \App\Models\LabOrder::where('hospital_id', $hospital->id)
            ->where('status', 'pending')
            ->with(['patient', 'doctor', 'labTestCatalog'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Billing Statistics
        $totalRevenue = \App\Models\Bill::where('hospital_id', $hospital->id)->sum('paid_amount');
        $pendingBillsCount = \App\Models\Bill::where('hospital_id', $hospital->id)->where('payment_status', '!=', 'paid')->count();
        $activeClaimsCount = \App\Models\InsuranceClaim::where('hospital_id', $hospital->id)->where('claim_status', 'pending')->count();
        
        AuditLogService::logAction('hospital dashboard viewed');
        
        return view('hospital.dashboard', [
            'hospital' => $hospital,
            'emergencies' => $emergencies,
            'resources' => $resources,
            'pendingLabs' => $pendingLabs,
            'stats' => [
                'revenue' => $totalRevenue,
                'pending_bills' => $pendingBillsCount,
                'active_claims' => $activeClaimsCount
            ]
        ]);
    }

    public function completeLabOrder(Request $request, $id)
    {
        $labOrder = \App\Models\LabOrder::findOrFail($id);
        $hospital = Auth::user()->hospital;

        if ($labOrder->hospital_id !== $hospital->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'result_summary' => 'required|string',
        ]);

        $labOrder->update([
            'status' => 'completed',
            'result_summary' => $validated['result_summary'],
        ]);

        AuditLogService::logAction('lab result uploaded', "Uploaded result for lab order #{$id}", \App\Models\LabOrder::class, $id);

        return redirect()->back()->with('success', 'Lab order marked as completed!');
    }

    public function updateResource(Request $request, $id)
    {
        $resource = \App\Models\HospitalResource::findOrFail($id);
        $hospital = Auth::user()->hospital;

        if ($resource->hospital_id !== $hospital->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'action' => 'required|in:increment,decrement',
        ]);

        if ($validated['action'] === 'increment') {
            $resource->increment('currently_in_use');
            $actionWord = 'incremented';
        } elseif ($validated['action'] === 'decrement' && $resource->currently_in_use > 0) {
            $resource->decrement('currently_in_use');
            $actionWord = 'decremented';
        } else {
            $actionWord = 'attempted to update';
        }

        AuditLogService::logAction('hospital resource updated', ucfirst($actionWord) . " resource {$resource->resource_type}", \App\Models\HospitalResource::class, $id);

        return response()->json(['success' => true]);
    }

    public function emergencies()
    {
        $hospital = Auth::user()->hospital;
        $emergencies = \App\Models\Emergency::where('hospital_id', $hospital->id)
            ->orWhereNull('hospital_id') // Show sent alerts that aren't assigned yet
            ->with('patient')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hospital.emergencies.index', compact('emergencies'));
    }

    public function viewEmergency($id)
    {
        $emergency = \App\Models\Emergency::with(['patient', 'doctor', 'ambulanceStaff'])->findOrFail($id);
        $hospital = Auth::user()->hospital;

        // If assigned to another hospital, unauthorized
        if ($emergency->hospital_id && $emergency->hospital_id !== $hospital->id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $doctors = \App\Models\Doctor::where('hospital_id', $hospital->id)->get();
        $ambulances = \App\Models\User::where('role', 'ambulance')->get(); 

        return view('hospital.emergencies.view', compact('emergency', 'doctors', 'ambulances'));
    }

    public function acceptEmergency(Request $request, $id)
    {
        $emergency = \App\Models\Emergency::findOrFail($id);
        $hospital = Auth::user()->hospital;

        $emergency->update([
            'hospital_id' => $hospital->id,
            'status' => 'Accepted',
            'accepted_at' => now(),
            'accepted_by' => Auth::id(),
        ]);

        \App\Services\AuditLogService::logAction(
            action: 'emergency accepted',
            description: "Hospital accepted emergency alert from {$emergency->patient->last_name}",
            module: 'emergency',
            severity: 'high',
            targetType: \App\Models\Emergency::class,
            targetId: $emergency->id
        );

        return redirect()->back()->with('success', 'Emergency accepted successfully.');
    }

    public function rejectEmergency(Request $request, $id)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $emergency = \App\Models\Emergency::findOrFail($id);
        
        $emergency->update([
            'status' => 'Rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->back()->with('success', 'Emergency rejected.');
    }

    public function dispatchAmbulance(Request $request, $id)
    {
        $validated = $request->validate([
            'ambulance_id' => 'nullable|exists:users,id',
        ]);

        $emergency = \App\Models\Emergency::findOrFail($id);

        // Auto-assign first available ambulance if none was selected (e.g. from the banner)
        $ambulanceId = $validated['ambulance_id'] ?? null;
        if (!$ambulanceId) {
            $ambulanceUser = \App\Models\User::where('role', 'ambulance')->first();
            $ambulanceId = $ambulanceUser?->id;
        }

        $emergency->update([
            'assigned_ambulance_id' => $ambulanceId,
            'status' => 'Ambulance Assigned',
        ]);

        return redirect()->back()->with('success', 'Ambulance dispatched successfully.');
    }

    public function assignDoctor(Request $request, $id)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
        ]);

        $emergency = \App\Models\Emergency::findOrFail($id);
        $emergency->update([
            'assigned_doctor_id' => $validated['doctor_id'],
        ]);

        return redirect()->back()->with('success', 'Doctor assigned successfully.');
    }

    public function resolveEmergency(Request $request, $id)
    {
        $emergency = \App\Models\Emergency::findOrFail($id);
        $emergency->update([
            'status' => 'Resolved',
            'resolved_at' => now(),
            'resolved_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Emergency marked as resolved.');
    }
}
