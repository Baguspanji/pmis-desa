<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'kk_number',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'address',
        'rt',
        'rw',
        'dusun',
        'religion',
        'marital_status',
        'occupation',
        'education',
        'phone',
        'is_active',
        'is_head',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'is_head' => 'boolean',
    ];

    /**
     * Get the resident's age
     */
    public function getAgeAttribute()
    {
        if (! $this->birth_date) {
            return null;
        }

        return $this->birth_date->age;
    }

    /**
     * Get formatted birth date
     */
    public function getFormattedBirthDateAttribute()
    {
        if (! $this->birth_date) {
            return '-';
        }

        return $this->birth_date->format('d/m/Y');
    }

    /**
     * Get gender label
     */
    public function getGenderLabelAttribute()
    {
        return match ($this->gender) {
            'male' => 'Laki-laki',
            'female' => 'Perempuan',
            default => '-',
        };
    }

    /**
     * Scope for active residents
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for filtering by dusun
     */
    public function scopeByDusun($query, $dusun)
    {
        return $query->where('dusun', $dusun);
    }

    /**
     * Scope for filtering by gender
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }
}
