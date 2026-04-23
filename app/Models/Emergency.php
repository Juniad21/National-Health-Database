<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    protected $fillable = ['patient_id', 'hospital_id', 'status', 'timestamp'];
    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
