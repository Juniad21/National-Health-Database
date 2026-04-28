<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalAuditLogController extends Controller
{
    public function index(Request $request)
    {
        $hospital = Auth::user()->hospital;

        if (!$hospital) {
            return redirect()->route('dashboard')->with('error', 'Hospital not found.');
        }

        $query = AccessLog::where('hospital_id', $hospital->id)
            ->with('user');

        // Filtering
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get unique actions and roles for the filter dropdowns
        $uniqueActions = AccessLog::where('hospital_id', $hospital->id)
            ->select('action')
            ->distinct()
            ->pluck('action');
            
        $uniqueRoles = AccessLog::where('hospital_id', $hospital->id)
            ->select('role')
            ->distinct()
            ->pluck('role');

        return view('hospital.logs', [
            'logs' => $logs,
            'actions' => $uniqueActions,
            'roles' => $uniqueRoles,
        ]);
    }
}
