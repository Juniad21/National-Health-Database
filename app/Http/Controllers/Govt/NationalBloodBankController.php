<?php

namespace App\Http\Controllers\Govt;

use App\Http\Controllers\Controller;
use App\Models\BloodStock;
use App\Models\BloodRequest;
use App\Models\Hospital;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NationalBloodBankController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_units' => BloodStock::sum('available_units'),
            'reporting_hospitals' => BloodStock::distinct('hospital_id')->count(),
            'pending_requests' => BloodRequest::where('status', 'Pending')->count(),
            'critical_requests' => BloodRequest::where('urgency_level', 'Critical')->whereNotIn('status', ['Fulfilled', 'Cancelled'])->count(),
            'rare_units' => BloodStock::whereIn('blood_group', BloodStock::getRareBloodGroups())->sum('available_units'),
            'low_stock_hospitals' => BloodStock::all()->filter(fn($s) => $s->status === 'Low Stock')->unique('hospital_id')->count(),
            'out_of_stock_groups' => BloodStock::where('available_units', 0)->count(),
        ];

        // Blood Availability Table
        $stockQuery = BloodStock::with('hospital');
        if ($request->filled('hospital')) {
            $stockQuery->whereHas('hospital', fn($q) => $q->where('name', 'like', "%{$request->hospital}%"));
        }
        if ($request->filled('district')) {
            $stockQuery->where('district', 'like', "%{$request->district}%");
        }
        if ($request->filled('blood_group')) {
            $stockQuery->where('blood_group', $request->blood_group);
        }
        $stocks = $stockQuery->paginate(15, ['*'], 'stock_page');

        // Blood Requests Table
        $requestQuery = BloodRequest::with(['requestingHospital', 'matchedHospital']);
        if ($request->filled('req_hospital')) {
            $requestQuery->whereHas('requestingHospital', fn($q) => $q->where('name', 'like', "%{$request->req_hospital}%"));
        }
        if ($request->filled('req_status')) {
            $requestQuery->where('status', $request->req_status);
        }
        if ($request->filled('req_urgency')) {
            $requestQuery->where('urgency_level', $request->req_urgency);
        }
        $requests = $requestQuery->latest()->paginate(10, ['*'], 'req_page');

        $bloodGroups = BloodStock::getBloodGroups();

        // Shortage Alerts
        $lowStockAlerts = BloodStock::with('hospital')
            ->get()
            ->filter(fn($s) => $s->status === 'Low Stock' || $s->status === 'Out of Stock')
            ->take(10);

        return view('govt_admin.blood_bank.index', compact('stats', 'stocks', 'requests', 'bloodGroups', 'lowStockAlerts'));
    }

    public function updateRequestStatus(Request $request, $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:Pending,Under Review,Matched,Approved,Partially Approved,Rejected,Fulfilled,Cancelled',
        ]);

        $oldStatus = $bloodRequest->status;
        $bloodRequest->update([
            'status' => $validated['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        if ($validated['status'] === 'Fulfilled' && $oldStatus !== 'Fulfilled') {
            $bloodRequest->update(['fulfilled_at' => now()]);
            // Logic to reduce stock if matched hospital exists
            if ($bloodRequest->matched_hospital_id && $bloodRequest->approved_units > 0) {
                $stock = BloodStock::where('hospital_id', $bloodRequest->matched_hospital_id)
                    ->where('blood_group', $bloodRequest->blood_group)
                    ->first();
                
                if ($stock) {
                    $stock->decrement('available_units', $bloodRequest->approved_units);
                }
            }
        }

        AuditLogService::logAction(
            "blood request status changed",
            "Request ID #{$id} status changed from {$oldStatus} to {$validated['status']}",
            'blood_bank',
            'medium',
            BloodRequest::class,
            $bloodRequest->id
        );

        return redirect()->back()->with('success', "Request status updated to {$validated['status']}.");
    }

    public function matchHospital(Request $request, $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $validated = $request->validate([
            'matched_hospital_id' => 'required|exists:hospitals,id',
            'approved_units' => 'required|integer|min:1|max:' . $bloodRequest->requested_units,
            'is_partial' => 'boolean',
        ]);

        $hospital = Hospital::findOrFail($validated['matched_hospital_id']);
        
        $status = $validated['approved_units'] < $bloodRequest->requested_units ? 'Partially Approved' : 'Approved';

        $bloodRequest->update([
            'matched_hospital_id' => $validated['matched_hospital_id'],
            'matched_hospital_name' => $hospital->name,
            'approved_units' => $validated['approved_units'],
            'status' => $status,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        AuditLogService::logAction(
            "blood request matched",
            "Matched request ID #{$id} with {$hospital->name} for {$validated['approved_units']} units",
            'blood_bank',
            'medium',
            BloodRequest::class,
            $bloodRequest->id
        );

        return redirect()->back()->with('success', "Request matched with {$hospital->name}. Status: {$status}");
    }

    public function updateAdminNote(Request $request, $id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $validated = $request->validate([
            'admin_note' => 'required|string',
        ]);

        $bloodRequest->update(['admin_note' => $validated['admin_note']]);

        return redirect()->back()->with('success', 'Admin note updated.');
    }
}
