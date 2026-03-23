<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    protected $fillable = [
        'hospital_id',
        'blood_group_needed',
        'urgency_level',
        'status',
    ];

    public function hospital()
    {
        return $this->belongsTo(User::class, 'hospital_id');
    }
}
