<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SymptomAssessment extends Model
{
    protected $fillable = [
        'patient_id',
        'selected_symptoms',
        'additional_notes',
        'severity',
        'duration',
        'suggested_specialty',
        'analysis_results'
    ];

    protected $casts = [
        'selected_symptoms' => 'array',
        'analysis_results' => 'array'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
