<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\PatientHealthMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        $patient = Auth::user()->patient;
        return view('patient.profile', compact('patient'));
    }

    /**
     * Update the patient profile.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:1000',
            'blood_group' => 'nullable|string|max:10',
            'height_cm' => 'nullable|numeric|min:1|max:300',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'allergies' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'current_medications' => 'nullable|string',
            'past_surgeries' => 'nullable|string',
            'family_history' => 'nullable|string',
            'lifestyle_notes' => 'nullable|string',
            'smoking_status' => 'nullable|in:never,former,current',
            'alcohol_status' => 'nullable|in:none,occasional,moderate,heavy',
            'activity_level' => 'nullable|in:sedentary,light,moderate,active,very_active',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
        ]);

        $patient = Auth::user()->patient;
        $patient->update($validated);

        return redirect()->route('patient.profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the health analytics dashboard.
     */
    public function healthAnalytics()
    {
        $patient = Auth::user()->patient;

        $metrics = PatientHealthMetric::where('patient_id', $patient->id)
            ->orderBy('recorded_at', 'desc')
            ->get();

        $latestMetric = $metrics->first();

        // Get last 10 entries for charts (reversed to show oldest first)
        $chartData = PatientHealthMetric::where('patient_id', $patient->id)
            ->orderBy('recorded_at', 'asc')
            ->take(10)
            ->get();

        return view('patient.health_analytics', compact('patient', 'metrics', 'latestMetric', 'chartData'));
    }

    /**
     * Store a new health metric record.
     */
    public function storeMetric(Request $request)
    {
        $validated = $request->validate([
            'recorded_at' => 'required|date',
            'weight_kg' => 'nullable|numeric|min:1|max:500',
            'systolic_bp' => 'nullable|numeric|min:50|max:300',
            'diastolic_bp' => 'nullable|numeric|min:30|max:200',
            'heart_rate' => 'nullable|numeric|min:20|max:250',
            'glucose_level' => 'nullable|numeric|min:0|max:1000',
            'oxygen_saturation' => 'nullable|numeric|min:50|max:100',
            'temperature_c' => 'nullable|numeric|min:30|max:45',
            'notes' => 'nullable|string|max:1000',
        ]);

        $patient = Auth::user()->patient;

        // Auto-calculate BMI if weight is provided and patient has height
        $bmi = null;
        $weight = $validated['weight_kg'] ?? null;
        if ($weight && $patient->height_cm && $patient->height_cm > 0) {
            $heightM = $patient->height_cm / 100;
            $bmi = round($weight / ($heightM * $heightM), 1);
        }

        // Update patient weight if provided
        if ($weight) {
            $patient->update(['weight_kg' => $weight]);
        }

        PatientHealthMetric::create([
            'user_id' => Auth::id(),
            'patient_id' => $patient->id,
            'weight_kg' => $validated['weight_kg'],
            'systolic_bp' => $validated['systolic_bp'],
            'diastolic_bp' => $validated['diastolic_bp'],
            'heart_rate' => $validated['heart_rate'],
            'glucose_level' => $validated['glucose_level'],
            'oxygen_saturation' => $validated['oxygen_saturation'],
            'temperature_c' => $validated['temperature_c'],
            'bmi' => $bmi,
            'notes' => $validated['notes'],
            'recorded_at' => $validated['recorded_at'],
        ]);

        return redirect()->route('patient.health_analytics')->with('success', 'Health record added successfully!');
    }

    /**
     * Delete a health metric record.
     */
    public function destroyMetric($id)
    {
        $metric = PatientHealthMetric::findOrFail($id);
        $patient = Auth::user()->patient;

        if ($metric->patient_id !== $patient->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $metric->delete();
        return redirect()->back()->with('success', 'Health record deleted.');
    }
}
