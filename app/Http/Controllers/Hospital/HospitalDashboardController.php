<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Hospital;
use App\Models\LabOrder;
use App\Models\MedicalRecord;
use App\Models\HospitalResource;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HospitalDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'hospital')
            abort(403);
        $hospital = $user->hospital;

        // Fetch resources
        $resources = HospitalResource::where('hospital_id', $hospital->id)->get();

        // Fetch pending lab orders
        $pendingLabs = LabOrder::with(['patient', 'doctor', 'labTestCatalog'])
            ->where('hospital_id', $hospital->id)
            ->where('status', 'pending')
            ->get();

        // Fetch active emergencies
        $emergencies = Emergency::with('patient')
            // Either specifically to this hospital or unassigned (all nearby hospitals should see it in real life, we just show all unassigned or assigned here)
            ->where(function ($query) use ($hospital) {
                $query->where('hospital_id', $hospital->id)->orWhereNull('hospital_id');
            })
            ->whereIn('status', ['active', 'dispatched'])
            ->orderBy('timestamp', 'desc')
            ->get();

        return view('hospital.dashboard', compact('hospital', 'resources', 'pendingLabs', 'emergencies'));
    }

    public function completeLabOrder(Request $request, $id)
    {
        $hospital = Auth::user()->hospital;
        $labOrder = LabOrder::findOrFail($id);

        if ($labOrder->hospital_id !== $hospital->id)
            abort(403);

        $request->validate(['result_summary' => 'required|string']);

        $labOrder->update([
            'status' => 'completed',
            'result_summary' => $request->result_summary
        ]);

        // Sync to patient medical records automatically
        MedicalRecord::create([
            'patient_id' => $labOrder->patient_id,
            'doctor_id' => $labOrder->doctor_id,
            'record_type' => 'lab',
            'diagnosis' => $labOrder->labTestCatalog->test_name . ' Result',
            'medications_or_results' => $request->result_summary,
            'date' => Carbon::today()
        ]);

        return back()->with('success', 'Lab Test completed and synced to patient records.');
    }

    public function updateResource(Request $request, $id)
    {
        $hospital = Auth::user()->hospital;
        $resource = HospitalResource::findOrFail($id);

        if ($resource->hospital_id !== $hospital->id)
            abort(403);

        $request->validate(['action' => 'required|in:increment,decrement']);

        if ($request->action === 'increment') {
            if ($resource->currently_in_use < $resource->total_capacity) {
                $resource->increment('currently_in_use');
            }
        } else {
            if ($resource->currently_in_use > 0) {
                $resource->decrement('currently_in_use');
            }
        }

        return response()->json(['success' => true, 'currently_in_use' => $resource->currently_in_use]);
    }

    public function dispatchAmbulance(Request $request, $id)
    {
        $emergency = Emergency::findOrFail($id);
        $emergency->update(['status' => 'dispatched', 'hospital_id' => Auth::user()->hospital->id]);
        return back()->with('success', 'Ambulance dispatched.');
    }

    public function resolveEmergency(Request $request, $id)
    {
        $emergency = Emergency::findOrFail($id);
        $emergency->update(['status' => 'resolved']);
        return back()->with('success', 'Emergency resolved.');
    }
}
