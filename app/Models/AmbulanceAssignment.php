<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbulanceAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'ambulance_id',
        'emergency_alert_id',
        'hospital_id',
        'patient_id',
        'assigned_by',
        'accepted_at',
        'started_at',
        'arrived_patient_at',
        'picked_up_at',
        'arrived_hospital_at',
        'completed_at',
        'status',
        'pickup_lat',
        'pickup_lng',
        'pickup_address',
        'destination_hospital_id',
        'destination_address',
        'estimated_distance_km',
        'eta_minutes',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'arrived_patient_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'arrived_hospital_at' => 'datetime',
        'completed_at' => 'datetime',
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
    ];

    public function ambulance()
    {
        return $this->belongsTo(Ambulance::class);
    }

    public function emergency()
    {
        return $this->belongsTo(Emergency::class, 'emergency_alert_id');
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function destinationHospital()
    {
        return $this->belongsTo(Hospital::class, 'destination_hospital_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
