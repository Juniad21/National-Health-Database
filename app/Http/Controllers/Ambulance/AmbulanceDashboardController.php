<?php

namespace App\Http\Controllers\Ambulance;

use App\Http\Controllers\Controller;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AmbulanceDashboardController extends Controller
{
    public function index()
    {
        $staff = Auth::user();
        $assignedEmergencies = Emergency::where('assigned_ambulance_id', $staff->id)
            ->whereNotIn('status', ['Resolved', 'Cancelled', 'Rejected'])
            ->with(['patient', 'hospital'])
            ->get();

        $completedEmergencies = Emergency::where('assigned_ambulance_id', $staff->id)
            ->whereIn('status', ['Resolved', 'Cancelled', 'Rejected'])
            ->with(['patient', 'hospital'])
            ->latest()
            ->take(10)
            ->get();

        return view('ambulance.dashboard', compact('assignedEmergencies', 'completedEmergencies'));
    }

    public function updateStatus(Request $request, $id)
    {
        $emergency = Emergency::findOrFail($id);
        
        if ($emergency->assigned_ambulance_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:On The Way,Arrived,Patient Picked Up,Reached Hospital,Completed',
        ]);

        $status = $validated['status'];
        
        // Map "Completed" to "Resolved" if that's the final state in DB, 
        // but user wanted "Resolved" as a state.
        if ($status === 'Completed') {
            $status = 'Resolved';
            $emergency->resolved_at = now();
            $emergency->resolved_by = Auth::id();
        }

        $emergency->update(['status' => $status]);

        \App\Services\AuditLogService::logAction(
            action: 'emergency status updated',
            description: "Ambulance staff updated emergency status to {$status}",
            module: 'emergency',
            severity: 'medium',
            targetType: Emergency::class,
            targetId: $emergency->id
        );

        return redirect()->back()->with('success', "Status updated to {$status}");
    }
}
