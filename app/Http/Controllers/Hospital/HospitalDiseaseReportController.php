<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\DiseaseReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HospitalDiseaseReportController extends Controller
{
    public function index()
    {
        $hospitalId = Auth::user()->hospital->id ?? null;
        
        $reports = DiseaseReport::where('hospital_id', $hospitalId)
            ->orderBy('report_date', 'desc')
            ->paginate(15);

        return view('hospital.disease_reports.index', compact('reports'));
    }

    public function create()
    {
        return view('hospital.disease_reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'disease_name' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'suspected_cases' => 'required|integer|min:0',
            'confirmed_cases' => 'required|integer|min:0',
            'recovered_cases' => 'required|integer|min:0',
            'death_cases' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'report_date' => 'required|date|before_or_equal:today',
        ]);

        $hospital = Auth::user()->hospital;
        
        $validated['hospital_id'] = $hospital->id ?? null;
        $validated['hospital_name'] = $hospital->name ?? Auth::user()->name;
        $validated['reported_by'] = Auth::id();
        $validated['severity_level'] = DiseaseReport::calculateSeverity($validated['confirmed_cases']);
        $validated['status'] = 'New';

        DiseaseReport::create($validated);

        return redirect()->route('hospital.disease_reports.index')
            ->with('success', 'Disease report submitted successfully.');
    }

    public function show($id)
    {
        $hospitalId = Auth::user()->hospital->id ?? null;
        $report = DiseaseReport::where('hospital_id', $hospitalId)->findOrFail($id);

        return view('hospital.disease_reports.show', compact('report'));
    }
}
