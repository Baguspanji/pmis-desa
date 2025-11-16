<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'task_name',
        'task_description',
        'program_id',
        'parent_task_id',
        'assigned_user_id',
        'status',
        'progress_type',
        'priority',
        'start_date',
        'end_date',
        'estimated_budget',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'estimated_budget' => 'decimal:2',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(TaskTarget::class);
    }

    public function budgetRealizations(): HasMany
    {
        return $this->hasMany(BudgetRealization::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'task_id');
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(TaskLogbook::class);
    }
}
