<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInvoices extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    // public static function canView(): bool
    // {
    //     return auth()->user()->hasAnyRole(['super_admin', 'sales']);
    // }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where('status', 'active')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('invoice number')),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label(__('invoice date'))
                    ->date(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('customer')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('seller')),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('total'))
                    ->money('EUR')
                    ->getStateUsing(fn(Invoice $record) => $record->total),
            ]);
    }
}
