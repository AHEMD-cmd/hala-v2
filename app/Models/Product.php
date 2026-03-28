<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    use HasFactory;
    use LogsActivity;

    
    
    protected $fillable = [
        'sku',
        'name',
        'description',
        'image',
        'cost_price',
        'selling_price',
        'stock_quantity',
    ];
    
    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['sku', 'name', 'stock_quantity', 'cost_price', 'selling_price'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('product')
            ->setDescriptionForEvent(fn(string $eventName) => "Product {$eventName}");
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getImagePathAttribute($value)
    {
        return asset('storage/' . $value);
    }
}