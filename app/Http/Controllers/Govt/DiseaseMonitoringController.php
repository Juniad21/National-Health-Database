<?php

namespace App\Http\Controllers\Govt;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiseaseMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = DiseaseReport::with('hospital');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('disease_name', 'like', "%{$search}%")
                    ->orWhere('district', 'like', "%{$search}%")
                    ->orWhere('hospital_name', 'like', "%{$search}%");
            });
        }

        // Filters
        if ($request->filled('disease_name')) {
            $query->where('disease_name', $request->disease_name);
        }

        if ($request->filled('district')) {
            $query->where('district', $request->district);
        }

        if ($request->filled('severity')) {
            $query->where('severity_level', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('report_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('report_date', '<=', $request->date_to);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(15)->withQueryString();

        // Summary Stats
        $stats = [
            'total_reports' => DiseaseReport::count(),
            'total_confirmed' => DiseaseReport::sum('confirmed_cases'),
            'high_risk_reports' => DiseaseReport::whereIn('severity_level', ['High', 'Critical'])->count(),
            'critical_districts' => DiseaseReport::where('severity_level', 'Critical')->distinct('district')->count('district'),
        ];

        // District-wise summary
        $districtSummary = DiseaseReport::select('district', 
                DB::raw('SUM(confirmed_cases) as total_confirmed'),
                DB::raw('COUNT(*) as report_count'),
                DB::raw('MAX(CASE 
                    WHEN severity_level = "Critical" THEN 4 
                    WHEN severity_level = "High" THEN 3 
                    WHEN severity_level = "Medium" THEN 2 
                    ELSE 1 END) as max_severity_rank')
            )
            ->groupBy('district')
            ->orderBy('total_confirmed', 'desc')
            ->get()
            ->map(function($item) {
                $severities = [1 => 'Low', 2 => 'Medium', 3 => 'High', 4 => 'Critical'];
                $item->highest_severity = $severities[$item->max_severity_rank];
                return $item;
            });

        // Critical Alerts
        $alerts = DiseaseReport::whereIn('severity_level', ['High', 'Critical'])
            ->where('status', '!=', 'Resolved')
            ->orderBy('confirmed_cases', 'desc')
            ->take(5)
            ->get();

        // Trends (By Date)
        $trends = DiseaseReport::select(DB::raw('DATE(report_date) as date'), DB::raw('SUM(confirmed_cases) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->take(10)
            ->get();

        return view('govt_admin.disease_monitoring.index', compact('reports', 'stats', 'districtSummary', 'alerts', 'trends'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:New,Monitoring,Notice Sent,Hospital Alerted,Resolved',
        ]);

        $report = DiseaseReport::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return back()->with('success', 'Disease report status updated successfully.');
    }
}
