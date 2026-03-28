<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Services\InvoiceNumberService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    // protected static bool $canCreateAnother = true;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['invoice_number'] = InvoiceNumberService::generate();
        $data['status'] = 'active';

        return $data;
    }

    // protected function handleRecordCreation(array $data): Model // we use it to override filament creation 
    // {
    //     return parent::handleRecordCreation($data);
    // } // last line must be object of the model

    protected function afterCreate(): void
    {
        DB::transaction(function () {
            $invoice = $this->record;

            // Update stock for each item
            foreach ($invoice->items as $item) {
                $product = $item->product;
                
                if ($product->stock_quantity < $item->quantity) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }

                $product->decrement('stock_quantity', $item->quantity);
                
                // Log stock change
                activity('product')
                    ->performedOn($product)
                    ->causedBy(auth()->user())
                    ->withProperties([
                        'old_stock' => $product->stock_quantity + $item->quantity,
                        'new_stock' => $product->stock_quantity,
                        'quantity_sold' => $item->quantity,
                        'invoice_number' => $invoice->invoice_number,
                    ])
                    ->log('Stock decreased due to sale');
            }
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function getCreatedNotificationTitle(): ?string // to override filament notification
    // {
    //     return 'Invoice created successfully';
    // }

    // protected function getCreatedNotification(): ?Notification // to override filament notification
    // {
    //     return Notification::make()
    //         ->success()
    //         ->title('Invoice created successfully')
    //         ->body('The invoice has been created successfully.')
    //         ->icon('heroicon-o-check-circle');
    // }
}