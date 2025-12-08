<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadResident extends Model
{
    protected $fillable = [
        'kk_number',
        'head_nik',
        'head_name',
        'resident_count',
    ];

    public function residents()
    {
        return $this->hasMany(Resident::class, 'kk_number', 'kk_number');
    }

    public function headResident()
    {
        return $this->belongsTo(Resident::class, 'head_nik', 'nik');
    }
}
