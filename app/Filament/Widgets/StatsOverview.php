<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    // public static function canView(): bool
    // {
    //     return auth()->user()->hasAnyRole(['super_admin', 'sales']);
    // }

    protected function getStats(): array
    {
        $todayInvoices = Invoice::whereDate('invoice_date', today()->toDateString())
            ->where('status', 'active')
            ->count();

        $todaySales = Invoice::whereDate('invoice_date', today()->toDateString())
            ->where('status', 'active')
            ->get()
            ->sum('total');

        $outOfStock = Product::where('stock_quantity', 0)->count();

        $lowStock = Product::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<', 10)
            ->count();

        return [
            Stat::make(__('Today\'s Invoices'), $todayInvoices)
                ->description(__('Invoices created today'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),

            Stat::make(__('Today\'s Sales'), '€ ' . number_format($todaySales, 2))
                ->description(__('Total sales today'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make(__('Out of Stock'), $outOfStock)
                ->description(__('Products with 0 stock'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make(__('Low Stock'), $lowStock)
                ->description(__('Products with less than 10 items'))
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),
        ];
    }
}
