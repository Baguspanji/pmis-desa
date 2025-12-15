<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskLogbook extends Model
{
    protected $fillable = [
        'task_id',
        'task_target_id',
        'title',
        'description',
        'log_date',
        'log_type',
        'progress_value',
        'status',
        'location',
        'activity_date',
        'created_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'activity_date' => 'datetime',
        'verified_at' => 'datetime',
        'progress_value' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function taskTarget()
    {
        return $this->belongsTo(TaskTarget::class, 'task_target_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'task_logbook_id');
    }

    // Scope untuk filtering
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('log_type', $type);
    }
}
