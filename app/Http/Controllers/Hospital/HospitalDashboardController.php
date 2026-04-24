<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // 👉 ADDED THIS: Fetch the pending lab orders for this hospital
        // I added with('labTestCatalog') because your blade file is trying to load the test_name!
        $pendingLabs = \App\Models\LabOrder::where('hospital_id', $hospital->id)
            ->where('status', 'pending') // Assuming your default status is 'pending'
            ->with('labTestCatalog')
            ->orderBy('created_at', 'asc') 
            ->get();
        
        return view('hospital.dashboard', [
            'hospital' => $hospital,
            'emergencies' => $emergencies,
            'resources' => $resources,
            'pendingLabs' => $pendingLabs // 👉 UPDATED THIS: Pass the variable to the view
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
            'results' => 'required|string',
        ]);

        $labOrder->update([
            'status' => 'completed',
            'results' => $validated['results'],
        ]);

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
            'currently_in_use' => 'required|integer|min:0',
        ]);

        $resource->update([
            'currently_in_use' => $validated['currently_in_use'],
        ]);

        return redirect()->back()->with('success', 'Resource updated successfully!');
    }

    public function dispatchAmbulance(Request $request, $id)
    {
        $emergency = \App\Models\Emergency::findOrFail($id);
        $hospital = Auth::user()->hospital;

        if ($emergency->hospital_id !== $hospital->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $emergency->update(['status' => 'dispatched']);

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

        return redirect()->back()->with('success', 'Emergency resolved!');
    }
}