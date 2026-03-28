<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    public static function generate(): string
    {
        return DB::transaction(function () {
            $prefix = now()->format('Y.m'); // e.g., 2026.03

            $count = Invoice::whereMonth('invoice_date', now()->month)
                ->whereYear('invoice_date', now()->year)
                ->lockForUpdate()
                ->count();

            $sequence = $count + 1;

            return "{$prefix}-{$sequence}";
        });
    }

    // public static function generate(): string
    // {
    //     return DB::transaction(function () {
    //         $prefix = now()->format('Y.m'); // e.g., 2026.03

    //         $last = Invoice::where('invoice_number', 'like', "{$prefix}-%")
    //             ->lockForUpdate()
    //             ->orderByDesc('invoice_number')
    //             ->value('invoice_number');

    //         $sequence = $last ? ((int) str_replace("{$prefix}-", '', $last)) + 1 : 1;

    //         return "{$prefix}-{$sequence}";
    //     });
    // }
}
