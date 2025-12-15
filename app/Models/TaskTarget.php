<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskTarget extends Model
{
    protected $fillable = [
        'task_id',
        'target_name',
        'target_value',
        'achieved_value',
        'target_date',
        'target_unit',
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

    public function logbooks()
    {
        return $this->hasMany(TaskLogbook::class);
    }
}
