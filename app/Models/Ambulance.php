<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambulance extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'ambulance_code',
        'vehicle_number',
        'ambulance_type',
        'capacity',
        'driver_name',
        'driver_phone',
        'current_status',
        'current_location_lat',
        'current_location_lng',
        'current_location_address',
        'last_location_updated_at',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'last_location_updated_at' => 'datetime',
        'is_active' => 'boolean',
        'current_location_lat' => 'decimal:8',
        'current_location_lng' => 'decimal:8',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function assignments()
    {
        return $this->hasMany(AmbulanceAssignment::class);
    }

    public function currentAssignment()
    {
        return $this->hasOne(AmbulanceAssignment::class)->whereNotIn('status', ['Completed', 'Cancelled'])->latestOfMany();
    }
}
