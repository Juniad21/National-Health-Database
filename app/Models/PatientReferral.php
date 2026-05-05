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
     * Relationship to the Patient profile.
     */
    public function patient()
    {
        // Linking to Patient model via the user_id (stored in patient_id column)
        return $this->belongsTo(Patient::class, 'patient_id', 'user_id');
    }

    /**
     * Relationship to the referring Doctor profile.
     */
    public function referredByDoctor()
    {
        // Linking to Doctor model via the user_id (stored in referred_by_doctor_id column)
        return $this->belongsTo(Doctor::class, 'referred_by_doctor_id', 'user_id');
    }

    /**
     * Relationship to the destination Doctor profile.
     */
    public function referredToDoctor()
    {
        return $this->belongsTo(Doctor::class, 'referred_to_doctor_id', 'user_id');
    }

    /**
     * Relationship to the destination Hospital profile.
     */
    public function referredToHospital()
    {
        return $this->belongsTo(Hospital::class, 'referred_to_hospital_id', 'user_id');
    }
}
