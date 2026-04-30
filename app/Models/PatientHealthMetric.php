<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientHealthMetric extends Model
{
    protected $fillable = [
        'user_id', 'patient_id',
        'weight_kg', 'systolic_bp', 'diastolic_bp',
        'heart_rate', 'glucose_level', 'oxygen_saturation',
        'temperature_c', 'bmi', 'notes', 'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'date',
        'weight_kg' => 'decimal:1',
        'bmi' => 'decimal:1',
        'glucose_level' => 'decimal:1',
        'oxygen_saturation' => 'decimal:1',
        'temperature_c' => 'decimal:1',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get blood pressure status badge.
     */
    public function getBpStatusAttribute(): ?string
    {
        if (!$this->systolic_bp || !$this->diastolic_bp) return null;
        if ($this->systolic_bp >= 180 || $this->diastolic_bp >= 120) return 'Critical';
        if ($this->systolic_bp >= 140 || $this->diastolic_bp >= 90) return 'High';
        if ($this->systolic_bp >= 120 && $this->systolic_bp <= 129 && $this->diastolic_bp < 80) return 'Elevated';
        return 'Normal';
    }
}
