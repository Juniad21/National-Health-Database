<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BloodRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requesting_hospital_id',
        'requesting_hospital_name',
        'district',
        'patient_id',
        'blood_group',
        'requested_units',
        'urgency_level',
        'request_reason',
        'required_by',
        'status',
        'matched_hospital_id',
        'matched_hospital_name',
        'approved_units',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
        'fulfilled_at',
        'cancelled_at',
    ];

    protected $casts = [
        'required_by' => 'datetime',
        'reviewed_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function requestingHospital()
    {
        return $this->belongsTo(Hospital::class, 'requesting_hospital_id');
    }

    public function matchedHospital()
    {
        return $this->belongsTo(Hospital::class, 'matched_hospital_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getUrgencyColorAttribute()
    {
        return [
            'Low' => 'gray',
            'Medium' => 'yellow',
            'High' => 'orange',
            'Critical' => 'red',
        ][$this->urgency_level] ?? 'gray';
    }

    public function getStatusColorAttribute()
    {
        return [
            'Pending' => 'yellow',
            'Under Review' => 'blue',
            'Matched' => 'purple',
            'Approved' => 'green',
            'Partially Approved' => 'indigo',
            'Rejected' => 'red',
            'Fulfilled' => 'gray',
            'Cancelled' => 'gray',
        ][$this->status] ?? 'gray';
    }
}
