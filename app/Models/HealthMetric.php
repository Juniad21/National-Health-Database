<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthMetric extends Model
{
    protected $fillable = [
        'patient_id',
        'bmi',
        'blood_pressure',
        'recorded_date',
    ];

    protected $casts = [
        'recorded_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
