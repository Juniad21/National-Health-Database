<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Emergency extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'hospital_id',
        'assigned_doctor_id',
        'emergency_type',
        'severity',
        'symptoms',
        'latitude',
        'longitude',
        'address',
        'contact_number',
        'guardian_contact',
        'status',
        'rejection_reason',
        'created_by',
        'accepted_by',
        'resolved_by',
        'accepted_at',
        'resolved_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'assigned_doctor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acceptor()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    public function ambulanceAssignments()
    {
        return $this->hasMany(AmbulanceAssignment::class, 'emergency_alert_id');
    }
}
