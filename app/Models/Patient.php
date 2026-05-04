<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nid', 'first_name', 'last_name',
        'date_of_birth', 'gender', 'blood_group', 'phone', 'email', 'address',
        'height_cm', 'weight_kg',
        'emergency_contact_name', 'emergency_contact_phone',
        'allergies', 'medical_conditions', 'current_medications',
        'past_surgeries', 'family_history', 'lifestyle_notes',
        'smoking_status', 'alcohol_status', 'activity_level',
        'insurance_provider', 'insurance_policy_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'height_cm' => 'decimal:1',
        'weight_kg' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function labOrders()
    {
        return $this->hasMany(LabOrder::class);
    }

    public function healthMetrics()
    {
        return $this->hasMany(PatientHealthMetric::class);
    }

    /**
     * Calculate BMI from height_cm and weight_kg.
     */
    public function getBmiAttribute(): ?float
    {
        if (!$this->height_cm || !$this->weight_kg || $this->height_cm <= 0) return null;
        $heightM = $this->height_cm / 100;
        return round($this->weight_kg / ($heightM * $heightM), 1);
    }

    /**
     * Get BMI category string.
     */
    public function getBmiCategoryAttribute(): ?string
    {
        $bmi = $this->bmi;
        if (!$bmi) return null;
        if ($bmi < 18.5) return 'Underweight';
        if ($bmi < 25) return 'Normal';
        if ($bmi < 30) return 'Overweight';
        return 'Obese';
    }

    public function reviews()
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class);
    }
}
