<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalResource extends Model
{
    protected $fillable = ['hospital_id', 'resource_type', 'total_capacity', 'currently_in_use'];
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
