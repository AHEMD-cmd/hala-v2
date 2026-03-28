<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Barryvdh\DomPDF\Facade\Pdf;

class Invoice extends Model
{
    use HasFactory;
    use LogsActivity;



    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_id',
        'user_id',
        'delivery_fee',
        'notes',
        'status',
        'initial_payment',
        'payment_method',
        'to_be_paid_upon_delivery',
        'LEVERING'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'delivery_fee' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['invoice_number', 'status', 'customer_id', 'delivery_fee'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('invoice')
            ->setDescriptionForEvent(fn(string $eventName) => "Invoice {$eventName}");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function getTotalAttribute(): float
    {
        $itemsTotal = $this->items->sum(fn($item) => $item->quantity * $item->unit_price);
        return $itemsTotal + ($this->delivery_fee ?? 0);
    }


    public function generatePdf()
    {
        $currentLocale = app()->getLocale();
        app()->setLocale('nl');

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $this,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('defaultFont', 'DejaVu Sans')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);
            
        app()->setLocale($currentLocale);
        return $pdf;
    }

    public function downloadPdf()
    {
        $pdf = $this->generatePdf();

        $filename = "invoice-{$this->invoice_number}.pdf";

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function streamPdf()
    {
        return $this->generatePdf()->stream("invoice-{$this->invoice_number}.pdf");
    }
}
