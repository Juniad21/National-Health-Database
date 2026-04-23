<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = ['patient_id', 'vaccine_name', 'due_date', 'status'];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
