<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTarget extends Model
{
    protected $fillable = [
        'task_id',
        'target_value',
        'achieved_value',
        'target_date',
        'notes',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
        'target_date' => 'date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
