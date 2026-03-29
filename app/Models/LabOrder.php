<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $fillable = ['patient_id', 'doctor_id', 'hospital_id', 'lab_test_catalog_id', 'status', 'result_summary'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
    public function labTestCatalog()
    {
        return $this->belongsTo(LabTestCatalog::class);
    }
}
