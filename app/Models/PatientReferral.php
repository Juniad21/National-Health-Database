<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'referred_by_doctor_id',
        'referred_to_doctor_id',
        'referred_to_hospital_id',
        'referral_type',
        'department',
        'priority',
        'reason',
        'clinical_summary',
        'recommended_tests',
        'status',
    ];

    /**
     * Relationship with the Patient (User record).
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Relationship with the Referring Doctor (User record).
     */
    public function referredByDoctor()
    {
        return $this->belongsTo(User::class, 'referred_by_doctor_id');
    }

    /**
     * Relationship with the Assigned Doctor (User record).
     */
    public function referredToDoctor()
    {
        return $this->belongsTo(User::class, 'referred_to_doctor_id');
    }

    /**
     * Relationship with the Assigned Hospital (User record).
     */
    public function referredToHospital()
    {
        return $this->belongsTo(User::class, 'referred_to_hospital_id');
    }
}
