<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Salary extends Model
{
    use HasFactory, LogsActivity;
    

    protected $fillable = [
        'employee_name',
        'amount',
        'month',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['employee_name', 'amount', 'month'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('salary')
            ->setDescriptionForEvent(fn(string $eventName) => "Salary {$eventName}");
    }
}