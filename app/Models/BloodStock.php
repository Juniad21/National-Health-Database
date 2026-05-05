<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BloodStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id',
        'hospital_name',
        'district',
        'blood_group',
        'available_units',
        'reserved_units',
        'minimum_required_units',
        'last_updated_by',
        'notes',
    ];

    protected $appends = ['status', 'status_color', 'surplus'];

    public static function getBloodGroups()
    {
        return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    }

    public static function getRareBloodGroups()
    {
        return ['A-', 'B-', 'AB-', 'O-'];
    }

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function getStatusAttribute()
    {
        if ($this->available_units <= 0) {
            return 'Out of Stock';
        }
        if ($this->available_units <= $this->minimum_required_units) {
            return 'Low Stock';
        }
        return 'Available';
    }

    public function getStatusColorAttribute()
    {
        return [
            'Out of Stock' => 'red',
            'Low Stock' => 'yellow',
            'Available' => 'green',
        ][$this->status] ?? 'gray';
    }

    public static function normalizeBloodGroup($group)
    {
        if (!$group) return null;
        
        $group = strtoupper(trim($group));
        $group = str_replace([' POSITIVE', ' POS', '_POSITIVE', ' PLUS'], '+', $group);
        $group = str_replace([' NEGATIVE', ' NEG', '_NEGATIVE', ' MINUS'], '-', $group);
        $group = str_replace(' ', '+', $group); // Handle URL space issue
        
        // Final sanity check for common groups
        $map = [
            'AB+' => 'AB+', 'AB-' => 'AB-',
            'A+' => 'A+', 'A-' => 'A-',
            'B+' => 'B+', 'B-' => 'B-',
            'O+' => 'O+', 'O-' => 'O-'
        ];
        
        return $map[$group] ?? $group;
    }

    public static function normalizeDistrict($district)
    {
        if (!$district) return '';
        $district = strtolower(trim($district));
        $district = explode(',', $district)[0]; // "Dhaka, Bangladesh" -> "dhaka"
        return trim($district);
    }

    public function isRare()
    {
        return in_array($this->blood_group, self::getRareBloodGroups());
    }

    public function getSurplusAttribute()
    {
        return max(0, $this->available_units - $this->minimum_required_units);
    }
}
