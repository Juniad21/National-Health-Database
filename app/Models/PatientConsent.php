<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientConsent extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'status',
        'last_accessed_log',
    ];

    protected $casts = [
        'last_accessed_log' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
