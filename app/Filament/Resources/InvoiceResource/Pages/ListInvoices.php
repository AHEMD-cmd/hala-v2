<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\Database\Query\Builder;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // public ?string $activeTab = 'pending deleting'; // the tab that will be active by default

    // public function getTabs(): array // to show tabs above the table in index 
    // {
    //     return [
    //         "all" => Tab::make()
    //         ->ModifyQueryUsing(function (Builder $query) {
    //             return $query->where('status', 'active');
    //         })->Label('All Invoices')
    //           ->Icon('heroicon-o-document-text')
    //           ->iconPosition(IconPosition::Before)
    //           ->badge(Invoice::count())
    //           ->badgeColor('success'),
              
    //         "pending deleting" => Tab::make()
    //         ->ModifyQueryUsing(function (Builder $query) {
    //             return $query->where('status', 'pending_delete');
    //         })->Label('Pending Deleting')
    //           ->Icon('heroicon-o-trash')
    //     ];
    // }
}
