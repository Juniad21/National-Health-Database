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

        $allHospitals = Hospital::orderBy('name')->get();

        return view('govt_admin.blood_bank.index', compact('stats', 'stocks', 'requests', 'bloodGroups', 'lowStockAlerts', 'allHospitals'));
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

    public function transferStock(Request $request)
    {
        $validated = $request->validate([
            'source_hospital_id' => 'required|exists:hospitals,id',
            'destination_hospital_id' => 'required|exists:hospitals,id|different:source_hospital_id',
            'blood_group' => 'required|string',
            'transfer_units' => 'required|integer|min:1'
        ]);

        $sourceStock = BloodStock::where('hospital_id', $validated['source_hospital_id'])
            ->where('blood_group', $validated['blood_group'])
            ->firstOrFail();

        if ($sourceStock->available_units < $validated['transfer_units']) {
            return redirect()->back()->with('error', 'Source hospital does not have enough units available.');
        }

        // Deduct from source
        $sourceStock->decrement('available_units', $validated['transfer_units']);

        // Add to destination
        $destinationStock = BloodStock::firstOrCreate(
            ['hospital_id' => $validated['destination_hospital_id'], 'blood_group' => $validated['blood_group']],
            [
                'hospital_name' => Hospital::find($validated['destination_hospital_id'])->name,
                'available_units' => 0,
                'minimum_required_units' => 10,
            ]
        );
        $destinationStock->increment('available_units', $validated['transfer_units']);

        // Create an audit trail record in requests
        $sourceHospital = Hospital::find($validated['source_hospital_id']);
        $destinationHospital = Hospital::find($validated['destination_hospital_id']);

        $requestRecord = BloodRequest::create([
            'requesting_hospital_id' => $destinationHospital->id,
            'requesting_hospital_name' => $destinationHospital->name,
            'district' => 'Dhaka', // Keeping it simple for the audit trail
            'blood_group' => $validated['blood_group'],
            'requested_units' => $validated['transfer_units'],
            'urgency_level' => 'High',
            'request_reason' => 'Government Directed Surplus Transfer',
            'status' => 'Fulfilled',
            'matched_hospital_id' => $sourceHospital->id,
            'matched_hospital_name' => $sourceHospital->name,
            'approved_units' => $validated['transfer_units'],
            'admin_note' => 'Manual surplus transfer executed by Govt Admin.',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'fulfilled_at' => now(),
        ]);

        AuditLogService::logAction(
            "manual surplus transfer",
            "Transferred {$validated['transfer_units']} units of {$validated['blood_group']} from {$sourceHospital->name} to {$destinationHospital->name}",
            'blood_bank',
            'high',
            BloodRequest::class,
            $requestRecord->id
        );

        return redirect()->back()->with('success', "Successfully transferred {$validated['transfer_units']} units of {$validated['blood_group']} to {$destinationHospital->name}.");
    }

    public function getMatches(Request $request)
    {
        $rawGroup = $request->blood_group;
        $normalizedGroup = BloodStock::normalizeBloodGroup($rawGroup);
        $requestedUnits = (int) $request->input('requested_units', 1);
        $rawDistrict = $request->district;
        $normalizedDistrict = BloodStock::normalizeDistrict($rawDistrict);
        $excludeHospitalId = $request->exclude_hospital_id;

        $query = BloodStock::with('hospital')
            ->where('available_units', '>', 0);

        // Exact match on normalized group
        $query->where('blood_group', $normalizedGroup);

        if ($excludeHospitalId) {
            $query->where('hospital_id', '!=', $excludeHospitalId);
        }

        $allStocks = $query->get();

        $matches = $allStocks->map(function($stock) use ($normalizedDistrict, $requestedUnits) {
            $stockNormalizedDistrict = BloodStock::normalizeDistrict($stock->district);
            $isSameDistrict = ($stockNormalizedDistrict === $normalizedDistrict);
            
            $matchType = ($stock->available_units >= $requestedUnits) ? 'Full Match' : 'Partial Match';
            $matchPriority = ($matchType === 'Full Match' ? 2 : 1);
            
            // Score for sorting: Same District (100) + Match Type (50) + Units Ratio
            $score = ($isSameDistrict ? 100 : 0) + ($matchPriority * 50) + ($stock->available_units / 100);
            
            return [
                'id' => $stock->id,
                'hospital_id' => $stock->hospital_id,
                'hospital_name' => $stock->hospital->name,
                'district' => $stock->district,
                'blood_group' => $stock->blood_group,
                'available_units' => $stock->available_units,
                'match_type' => $matchType,
                'is_same_district' => $isSameDistrict,
                'score' => $score
            ];
        })->sortByDesc('score')->values();

        // Developer logging
        \Illuminate\Support\Facades\Log::info("Blood Match Execution", [
            'request_id' => $request->request_id,
            'original_group' => $rawGroup,
            'normalized_group' => $normalizedGroup,
            'requested_units' => $requestedUnits,
            'stocks_checked' => $allStocks->count(),
            'matches_found' => $matches->count()
        ]);

        return response()->json([
            'matches' => $matches,
            'debug' => [
                'normalized_group' => $normalizedGroup,
                'normalized_district' => $normalizedDistrict,
                'count' => $matches->count()
            ]
        ]);
    }
}
