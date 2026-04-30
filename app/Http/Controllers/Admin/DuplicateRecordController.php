<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\LabOrder;
use App\Models\Vaccination;
use App\Models\PatientConsent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DuplicateRecordController extends Controller
{
    public function index()
    {
        // Find potential duplicates by grouping by first_name, last_name and date_of_birth
        // We only want groups where count > 1
        $duplicates = Patient::select('first_name', 'last_name', 'date_of_birth', DB::raw('COUNT(*) as count'))
            ->groupBy('first_name', 'last_name', 'date_of_birth')
            ->having('count', '>', 1)
            ->get();

        $potentialDuplicateGroups = [];

        foreach ($duplicates as $duplicate) {
            $group = Patient::where('first_name', $duplicate->first_name)
                ->where('last_name', $duplicate->last_name)
                ->where('date_of_birth', $duplicate->date_of_birth)
                ->get();
            
            $potentialDuplicateGroups[] = [
                'criteria' => $duplicate->first_name . ' ' . $duplicate->last_name . ' (' . $duplicate->date_of_birth . ')',
                'patients' => $group
            ];
        }

        return view('admin.duplicates.index', compact('potentialDuplicateGroups'));
    }

    public function compare($id1, $id2)
    {
        $patient1 = Patient::with(['user', 'appointments', 'medicalRecords'])->findOrFail($id1);
        $patient2 = Patient::with(['user', 'appointments', 'medicalRecords'])->findOrFail($id2);

        return view('admin.duplicates.compare', compact('patient1', 'patient2'));
    }

    public function merge(Request $request)
    {
        $request->validate([
            'keep_id' => 'required|exists:patients,id',
            'merge_id' => 'required|exists:patients,id',
        ]);

        $keepId = $request->keep_id;
        $mergeId = $request->merge_id;

        if ($keepId == $mergeId) {
            return back()->with('error', 'Cannot merge a record into itself.');
        }

        DB::transaction(function () use ($keepId, $mergeId) {
            // Get patients first to access their user IDs
            $keepPatient = Patient::find($keepId);
            $mergePatient = Patient::find($mergeId);

            // Update all related records
            Appointment::where('patient_id', $mergeId)->update(['patient_id' => $keepId]);
            MedicalRecord::where('patient_id', $mergeId)->update(['patient_id' => $keepId]);
            LabOrder::where('patient_id', $mergeId)->update(['patient_id' => $keepId]);
            Vaccination::where('patient_id', $mergeId)->update(['patient_id' => $keepId]);
            
            if ($mergePatient->user_id && $keepPatient->user_id) {
                PatientConsent::where('patient_id', $mergePatient->user_id)->update(['patient_id' => $keepPatient->user_id]);
            }

            // Optionally merge some patient info if they are missing in the 'keep' record

            $fieldsToUpdate = [];
            foreach (['blood_group', 'phone', 'address', 'gender'] as $field) {
                if (empty($keepPatient->$field) && !empty($mergePatient->$field)) {
                    $fieldsToUpdate[$field] = $mergePatient->$field;
                }
            }

            if (!empty($fieldsToUpdate)) {
                $keepPatient->update($fieldsToUpdate);
            }

            // Delete the merged patient
            // Note: We might want to delete the user as well if they are not used elsewhere
            $mergeUser = $mergePatient->user;
            $mergePatient->delete();
            
            if ($mergeUser) {
                $mergeUser->delete();
            }
        });

        return redirect()->route('admin.duplicates.index')->with('success', 'Records merged successfully.');
    }
}
