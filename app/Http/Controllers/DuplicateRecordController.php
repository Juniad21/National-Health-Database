<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

class DuplicateRecordController extends Controller
{
    /**
     * Identify duplicate patient records based on specific fields.
     */
    public function identifyDuplicates()
    {
        $duplicates = DB::table('patients')
            ->select('first_name', 'last_name', 'date_of_birth', DB::raw('COUNT(*) as count'))
            ->groupBy('first_name', 'last_name', 'date_of_birth')
            ->having('count', '>', 1)
            ->get();

        return response()->json($duplicates);
    }

    /**
     * Merge duplicate patient records.
     */
    public function mergeDuplicates(Request $request)
    {
        $primaryPatientId = $request->input('primary_patient_id');
        $duplicatePatientIds = $request->input('duplicate_patient_ids');

        $primaryPatient = Patient::find($primaryPatientId);

        if (!$primaryPatient) {
            return response()->json(['error' => 'Primary patient not found'], 404);
        }

        DB::transaction(function () use ($primaryPatient, $duplicatePatientIds) {
            foreach ($duplicatePatientIds as $duplicateId) {
                $duplicatePatient = Patient::find($duplicateId);

                if ($duplicatePatient) {
                    // Merge appointments
                    $duplicatePatient->appointments()->update(['patient_id' => $primaryPatient->id]);

                    // Merge medical records
                    $duplicatePatient->medicalRecords()->update(['patient_id' => $primaryPatient->id]);

                    // Delete duplicate record
                    $duplicatePatient->delete();
                }
            }
        });

        return response()->json(['message' => 'Duplicates merged successfully']);
    }
}