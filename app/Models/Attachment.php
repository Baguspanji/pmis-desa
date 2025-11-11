<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'task_id',
        'program_id',
        'uploaded_by',
        'description',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
