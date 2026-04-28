<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'patient_id',
        'hospital_id',
        'insurance_provider',
        'policy_number',
        'claim_amount',
        'approved_amount',
        'claim_status',
        'remarks',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
