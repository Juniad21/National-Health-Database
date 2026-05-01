<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\BloodStock;
use App\Models\BloodRequest;
use App\Models\Patient;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BloodBankController extends Controller
{
    public function index(Request $request)
    {
        $hospital = Auth::user()->hospital;
        $bloodGroups = BloodStock::getBloodGroups();

        // Ensure stock records exist for all blood groups
        foreach ($bloodGroups as $group) {
            BloodStock::firstOrCreate(
                ['hospital_id' => $hospital->id, 'blood_group' => $group],
                ['hospital_name' => $hospital->name, 'district' => $hospital->address] // Using address as district for now if district field isn't separate
            );
        }

        $query = BloodStock::where('hospital_id', $hospital->id);

        if ($request->filled('blood_group')) {
            $query->where('blood_group', $request->blood_group);
        }

        $stocks = $query->get();

        $stats = [
            'total_units' => $stocks->sum('available_units'),
            'rare_units' => $stocks->whereIn('blood_group', BloodStock::getRareBloodGroups())->sum('available_units'),
            'low_stock' => $stocks->filter(fn($s) => $s->status === 'Low Stock')->count(),
            'out_of_stock' => $stocks->filter(fn($s) => $s->status === 'Out of Stock')->count(),
        ];

        $requestQuery = BloodRequest::where('requesting_hospital_id', $hospital->id);

        if ($request->filled('request_status')) {
            $requestQuery->where('status', $request->request_status);
        }
        if ($request->filled('urgency')) {
            $requestQuery->where('urgency_level', $request->urgency);
        }

        $requests = $requestQuery->latest()->paginate(10);
        $patients = Patient::select('id', 'first_name', 'last_name', 'nid')->get();

        return view('hospital.blood_bank.index', compact('stocks', 'stats', 'requests', 'bloodGroups', 'patients'));
    }

    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'blood_group' => 'required|string',
            'available_units' => 'required|integer|min:0',
            'minimum_required_units' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $hospital = Auth::user()->hospital;
        $stock = BloodStock::where('hospital_id', $hospital->id)
            ->where('blood_group', $validated['blood_group'])
            ->firstOrFail();

        $oldUnits = $stock->available_units;
        $stock->update([
            'available_units' => $validated['available_units'],
            'minimum_required_units' => $validated['minimum_required_units'],
            'notes' => $validated['notes'],
            'last_updated_by' => Auth::id(),
            'hospital_name' => $hospital->name,
            'district' => $hospital->address,
        ]);

        AuditLogService::logAction(
            'blood stock updated',
            "Updated {$stock->blood_group} stock from {$oldUnits} to {$validated['available_units']} units",
            'blood_bank',
            'low',
            BloodStock::class,
            $stock->id
        );

        return redirect()->back()->with('success', "Blood stock for {$stock->blood_group} updated successfully.");
    }

    public function storeRequest(Request $request)
    {
        $hospital = Auth::user()->hospital;
        
        $validated = $request->validate([
            'blood_group' => 'required|string',
            'requested_units' => 'required|integer|min:1',
            'urgency_level' => 'required|in:Low,Medium,High,Critical',
            'required_by' => 'nullable|date|after_or_equal:now',
            'patient_id' => 'nullable|exists:patients,id',
            'request_reason' => 'nullable|string',
        ]);

        $bloodRequest = BloodRequest::create([
            'requesting_hospital_id' => $hospital->id,
            'requesting_hospital_name' => $hospital->name,
            'district' => $hospital->address,
            'blood_group' => $validated['blood_group'],
            'requested_units' => $validated['requested_units'],
            'urgency_level' => $validated['urgency_level'],
            'required_by' => $validated['required_by'],
            'patient_id' => $validated['patient_id'],
            'request_reason' => $validated['request_reason'],
            'status' => 'Pending',
        ]);

        AuditLogService::logAction(
            'blood request created',
            "Requested {$validated['requested_units']} units of {$validated['blood_group']} blood",
            'blood_bank',
            'medium',
            BloodRequest::class,
            $bloodRequest->id
        );

        return redirect()->back()->with('success', 'Blood request submitted successfully.');
    }

    public function cancelRequest($id)
    {
        $hospital = Auth::user()->hospital;
        $bloodRequest = BloodRequest::where('requesting_hospital_id', $hospital->id)
            ->where('status', 'Pending')
            ->findOrFail($id);

        $bloodRequest->update([
            'status' => 'Cancelled',
            'cancelled_at' => now(),
        ]);

        AuditLogService::logAction(
            'blood request cancelled',
            "Cancelled request ID #{$id}",
            'blood_bank',
            'low',
            BloodRequest::class,
            $bloodRequest->id
        );

        return redirect()->back()->with('success', 'Blood request cancelled.');
    }
}
