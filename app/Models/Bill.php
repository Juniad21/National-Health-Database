<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'hospital_id',
        'appointment_id',
        'bill_number',
        'consultation_fee',
        'lab_fee',
        'medicine_fee',
        'room_fee',
        'emergency_fee',
        'other_charges',
        'discount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'issued_date',
        'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function claims()
    {
        return $this->hasMany(InsuranceClaim::class);
    }
}
