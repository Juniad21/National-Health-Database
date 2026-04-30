<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_name',
        'district',
        'hospital_id',
        'hospital_name',
        'reported_by',
        'suspected_cases',
        'confirmed_cases',
        'recovered_cases',
        'death_cases',
        'severity_level',
        'status',
        'notes',
        'report_date',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * Calculate severity level based on confirmed cases.
     *
     * @param int $confirmedCases
     * @return string
     */
    public static function calculateSeverity(int $confirmedCases): string
    {
        if ($confirmedCases >= 100) {
            return 'Critical';
        } elseif ($confirmedCases >= 50) {
            return 'High';
        } elseif ($confirmedCases >= 20) {
            return 'Medium';
        }
        return 'Low';
    }
}
