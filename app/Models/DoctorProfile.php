<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'full_name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'profile_photo',
        'license_number',
        'license_expiry_date',
        'specialization',
        'qualifications',
        'medical_college',
        'years_of_experience',
        'hospital_id',
        'hospital_name',
        'department',
        'designation',
        'consultation_fee',
        'consultation_type',
        'available_days',
        'available_time_slots',
        'languages_spoken',
        'biography',
        'services_offered',
        'awards_certifications',
        'emergency_availability',
        'verification_status',
        'admin_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'license_expiry_date' => 'date',
        'emergency_availability' => 'boolean',
        'years_of_experience' => 'integer',
        'consultation_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
