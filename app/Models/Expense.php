<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Expense extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'type',
        'amount',
        'date',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['type', 'amount', 'date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('expense')
            ->setDescriptionForEvent(fn(string $eventName) => "Expense {$eventName}");
    }
}
