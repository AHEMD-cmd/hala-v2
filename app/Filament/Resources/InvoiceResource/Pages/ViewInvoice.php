<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('print'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn() => route('invoice.print', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('download_pdf')
                ->label(__('download pdf'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn() => response()->streamDownload(
                    fn() => print($this->record->generatePdf()->output()),
                    "invoice-{$this->record->invoice_number}.pdf"
                )),

            Actions\Action::make('view_pdf')
                ->label(__('view pdf'))
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn() => route('invoice.pdf', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('send_email')
                ->label(__('send email'))
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('send email'))
                ->modalDescription(fn() => "Send invoice {$this->record->invoice_number} to {$this->record->customer->email}?")
                ->modalSubmitActionLabel(__('send email'))
                ->visible(fn() => $this->record->customer->email && $this->record->status === 'active')
                ->action(function () {
                    try {
                        \Illuminate\Support\Facades\Mail::to($this->record->customer->email)
                            ->send(new \App\Mail\InvoiceMail($this->record));

                        Notification::make()
                            ->title(__('email sent successfully'))
                            ->success()
                            ->body("Invoice sent to {$this->record->customer->email}")
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title(__('email failed'))
                            ->danger()
                            ->body("Error: {$e->getMessage()}")
                            ->send();
                    }
                }),

            Actions\EditAction::make()
                ->visible(
                    fn() =>
                    $this->record->status === 'active' &&
                        auth()->user()->hasRole('super_admin')
                ),
        ];
    }
}
