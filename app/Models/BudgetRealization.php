<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetRealization extends Model
{
    protected $fillable = [
        'task_id',
        'amount',
        'description',
        'category',
        'transaction_type',
        'transaction_date',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
