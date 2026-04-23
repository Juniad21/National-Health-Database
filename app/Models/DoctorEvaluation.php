<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorEvaluation extends Model
{
    protected $fillable = ['appointment_id', 'doctor_id', 'patient_id', 'rating_1_to_5', 'feedback_text', 'consultation_time_minutes'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
