<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramTarget extends Model
{
    protected $fillable = [
        'program_id',
        'target_name',
        'target_value',
        'achieved_value',
        'target_unit',
        'description',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
