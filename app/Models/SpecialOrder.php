<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SpecialOrder extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'customer_id',
        'user_id',
        'product_description',
        'quantity',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['customer_id', 'user_id', 'product_description', 'quantity', 'status', 'notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('special_order')
            ->setDescriptionForEvent(fn(string $eventName) => "Special Order {$eventName}");
    }
}