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
        
        $pendingLabs = \App\Models\LabOrder::where('hospital_id', $hospital->id)
            ->where('status', 'pending')
            ->with(['patient', 'doctor', 'labTestCatalog'])
            ->get();
        
        AuditLogService::logHospitalAction('hospital dashboard viewed');
        
        return view('hospital.dashboard', [
            'emergencies' => $emergencies,
            'resources' => $resources,
            'pendingLabs' => $pendingLabs
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

        AuditLogService::logHospitalAction('lab result uploaded', "Uploaded result for lab order #{$id}", \App\Models\LabOrder::class, $id);

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

        AuditLogService::logHospitalAction('hospital resource updated', ucfirst($actionWord) . " resource {$resource->resource_type}", \App\Models\HospitalResource::class, $id);

        return response()->json(['success' => true]);
    }

    public function dispatchAmbulance(Request $request, $id)
    {
        $emergency = \App\Models\Emergency::findOrFail($id);
        $hospital = Auth::user()->hospital;

        if ($emergency->hospital_id !== $hospital->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $emergency->update(['status' => 'dispatched']);

        AuditLogService::logHospitalAction('emergency status updated', "Dispatched ambulance for emergency #{$id}", \App\Models\Emergency::class, $id);

        return redirect()->back()->with('success', 'Ambulance dispatched!');
    }

    public function resolveEmergency(Request $request, $id)
    {
        $emergency = \App\Models\Emergency::findOrFail($id);
        $hospital = Auth::user()->hospital;

        if ($emergency->hospital_id !== $hospital->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $emergency->update(['status' => 'resolved']);

        AuditLogService::logHospitalAction('emergency status updated', "Resolved emergency #{$id}", \App\Models\Emergency::class, $id);

        return redirect()->back()->with('success', 'Emergency resolved!');
    }
}