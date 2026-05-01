<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AmbulanceFleetController extends Controller
{
    public function index()
    {
        $hospital = Auth::user()->hospital;
        $ambulances = Ambulance::where('hospital_id', $hospital->id)->get();

        $stats = [
            'total' => $ambulances->count(),
            'available' => $ambulances->where('current_status', 'Available')->count(),
            'assigned' => $ambulances->whereNotIn('current_status', ['Available', 'Maintenance', 'Out Of Service'])->count(),
            'unavailable' => $ambulances->whereIn('current_status', ['Maintenance', 'Out Of Service'])->count(),
        ];

        $activeAssignments = AmbulanceAssignment::where('hospital_id', $hospital->id)
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->with(['ambulance', 'emergency.patient'])
            ->latest()
            ->get();

        return view('hospital.ambulance_fleet.index', compact('ambulances', 'stats', 'activeAssignments'));
    }

    public function store(Request $request)
    {
        $hospital = Auth::user()->hospital;
        
        $validated = $request->validate([
            'ambulance_code' => 'required|string|unique:ambulances,ambulance_code,NULL,id,hospital_id,' . $hospital->id,
            'vehicle_number' => 'required|string',
            'ambulance_type' => 'required|in:Basic Life Support,Advanced Life Support,ICU Ambulance,Neonatal Ambulance,Patient Transport',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['hospital_id'] = $hospital->id;
        $validated['current_status'] = 'Available';

        Ambulance::create($validated);

        \App\Services\AuditLogService::logAction(
            action: 'ambulance created',
            description: "Added ambulance {$validated['ambulance_code']} to fleet",
            module: 'fleet',
            severity: 'low'
        );

        return redirect()->back()->with('success', 'Ambulance added to fleet successfully.');
    }

    public function update(Request $request, $id)
    {
        $ambulance = Ambulance::where('hospital_id', Auth::user()->hospital->id)->findOrFail($id);
        
        $validated = $request->validate([
            'ambulance_code' => 'required|string|unique:ambulances,ambulance_code,' . $id . ',id,hospital_id,' . $ambulance->hospital_id,
            'vehicle_number' => 'required|string',
            'ambulance_type' => 'required|in:Basic Life Support,Advanced Life Support,ICU Ambulance,Neonatal Ambulance,Patient Transport',
            'capacity' => 'required|integer|min:1',
            'driver_name' => 'nullable|string',
            'driver_phone' => 'nullable|string',
            'current_status' => 'required|in:Available,Maintenance,Out Of Service',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $ambulance->current_status;
        $ambulance->update($validated);

        if ($oldStatus !== $validated['current_status']) {
            \App\Services\AuditLogService::logAction(
                action: 'ambulance status changed',
                description: "Ambulance {$ambulance->ambulance_code} status changed from {$oldStatus} to {$validated['current_status']}",
                module: 'fleet',
                severity: 'low',
                targetType: Ambulance::class,
                targetId: $ambulance->id
            );
        }

        return redirect()->back()->with('success', 'Ambulance details updated.');
    }

    public function updateAssignmentStatus(Request $request, $id)
    {
        $hospital = Auth::user()->hospital;
        $assignment = AmbulanceAssignment::where('hospital_id', $hospital->id)->findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:Accepted,Completed,Cancelled',
        ]);

        $status = $validated['status'];
        $updates = ['status' => $status];
        
        if ($status === 'Accepted') $updates['accepted_at'] = now();
        if ($status === 'On The Way') $updates['started_at'] = now();
        if ($status === 'At Patient Location') $updates['arrived_patient_at'] = now();
        if ($status === 'Patient Picked Up') $updates['picked_up_at'] = now();
        if ($status === 'Arrived At Hospital') $updates['arrived_hospital_at'] = now();
        
        if ($status === 'Completed') {
            $updates['completed_at'] = now();
            if ($assignment->ambulance) {
                $assignment->ambulance->update(['current_status' => 'Available']);
            }
            if ($assignment->emergency) {
                $assignment->emergency->update([
                    'status' => 'Resolved',
                    'resolved_at' => now(),
                    'resolved_by' => Auth::id()
                ]);
            }
        } elseif ($status === 'Cancelled') {
             if ($assignment->ambulance) {
                $assignment->ambulance->update(['current_status' => 'Available']);
            }
        } else {
            if ($assignment->ambulance) {
                $assignment->ambulance->update(['current_status' => $status]);
            }
            if ($assignment->emergency) {
                $assignment->emergency->update(['status' => $status]);
            }
        }

        $assignment->update($updates);

        return redirect()->back()->with('success', "Mission status updated to {$status}");
    }

    public function history()
    {
        $hospital = Auth::user()->hospital;
        $completedAssignments = AmbulanceAssignment::where('hospital_id', $hospital->id)
            ->whereIn('status', ['Completed', 'Cancelled'])
            ->with(['ambulance', 'emergency.patient'])
            ->latest()
            ->paginate(15);

        return view('hospital.ambulance_fleet.history', compact('completedAssignments'));
    }
    public function resetStatus($id)
    {
        $ambulance = Ambulance::where('hospital_id', Auth::user()->hospital->id)->findOrFail($id);
        
        $oldStatus = $ambulance->current_status;
        $ambulance->update(['current_status' => 'Available']);

        \App\Services\AuditLogService::logAction(
            action: 'ambulance manual reset',
            description: "Ambulance {$ambulance->ambulance_code} manually reset to Available from {$oldStatus}",
            module: 'fleet',
            severity: 'medium',
            targetType: Ambulance::class,
            targetId: $ambulance->id
        );

        return redirect()->back()->with('success', 'Ambulance status reset to Available.');
    }
}
