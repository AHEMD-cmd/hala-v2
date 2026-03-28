<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Models\Product;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class InvoiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('Invoices');
    }

    public static function getNavigationGroup(): string
    {
        return __('Sales');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

    public static function getNavigationBadge(): ?string
    {
        return Invoice::where('status', 'active')->count();
    }

    /**
     * Recalculate to_be_paid_upon_delivery from root-level Get context.
     * Call this from fields OUTSIDE the repeater (delivery_fee, initial_payment).
     */
    protected static function recalcToBePaid(Get $get, Set $set): void
    {
        $items    = $get('items') ?? [];
        $subtotal = collect($items)->sum(
            fn($item) => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)
        );
        $delivery = (float) ($get('delivery_fee') ?? 0);
        $initial  = (float) ($get('initial_payment') ?? 0);

        $set('to_be_paid_upon_delivery', round($subtotal + $delivery - $initial, 2));
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Invoice Information'))
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('invoice number'))
                            ->disabled()
                            ->dehydrated(false)
                            ->default('Auto-generated')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('invoice_date')
                            ->label(__('invoice date'))
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\Select::make('customer_id')
                            ->label(__('customer'))
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('address')
                                    ->maxLength(65535),
                                Forms\Components\TextInput::make('post_code'),
                                Forms\Components\TextInput::make('city'),
                            ])
                            ->columnSpan(2),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Invoice Items'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(__('product'))
                                    ->options(function () {
                                        return Product::where('stock_quantity', '>', 0)
                                            ->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->selling_price);
                                                $set('available_stock', $product->stock_quantity);
                                            }
                                        }
                                        // Recalc to_be_paid from root using ../../ to escape repeater scope
                                        $delivery = (float) ($get('../../delivery_fee') ?? 0);
                                        $initial  = (float) ($get('../../initial_payment') ?? 0);
                                        $items    = $get('../../items') ?? [];
                                        $subtotal = collect($items)->sum(
                                            fn($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0)
                                        );
                                        $set('../../to_be_paid_upon_delivery', round($subtotal + $delivery - $initial, 2));
                                    })
                                    ->columnSpan(3),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function (Get $get, Set $set) {
                                        // Recalc to_be_paid from root using ../../ to escape repeater scope
                                        $delivery = (float) ($get('../../delivery_fee') ?? 0);
                                        $initial  = (float) ($get('../../initial_payment') ?? 0);
                                        $items    = $get('../../items') ?? [];
                                        $subtotal = collect($items)->sum(
                                            fn($item) => (float) ($item['quantity'] ?? 0) * (float) ($item['unit_price'] ?? 0)
                                        );
                                        $set('../../to_be_paid_upon_delivery', round($subtotal + $delivery - $initial, 2));
                                    })
                                    ->rules([
                                        function (Get $get) {
                                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                                $productId = $get('product_id');
                                                if ($productId) {
                                                    $product = \App\Models\Product::find($productId);
                                                    if ($product && $value > $product->stock_quantity) {
                                                        $fail("Only {$product->stock_quantity} items available in stock.");
                                                    }
                                                }
                                            };
                                        },
                                    ])
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label(__('unit price'))
                                    ->required()
                                    ->numeric()
                                    ->prefix('€')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\Placeholder::make('line_total')
                                    ->label(__('total'))
                                    ->content(function (Get $get) {
                                        $quantity = (float) ($get('quantity') ?? 0);
                                        $price    = (float) ($get('unit_price') ?? 0);
                                        return '€ ' . number_format($quantity * $price, 2, ',', '.');
                                    })
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('available_stock'),
                            ])
                            ->columns(6)
                            ->defaultItems(1)
                            ->addActionLabel(__('add product'))
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make(__('Additional Information'))
                    ->schema([
                        Forms\Components\TextInput::make('initial_payment')
                            ->label(__('initial payment'))
                            ->numeric()
                            ->prefix('€')
                            ->default(0)
                            ->debounce(500)          // ← wait 500ms after user stops typing
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::recalcToBePaid($get, $set);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('to_be_paid_upon_delivery')
                            ->label(__('to be paid upon delivery'))
                            ->numeric()
                            ->prefix('€')
                            ->default(0)
                            ->disabled()
                            ->dehydrated()  // still saves to DB despite being disabled
                            ->columnSpan(1),

                        Forms\Components\Select::make('payment_method')
                            ->label(__('payment method'))
                            ->options([
                                __('cash') => 'contant',
                                __('via bank') => 'via bank',
                            ])
                            ->default('cash')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('delivery_fee')
                            ->label(__('delivery fee'))
                            ->numeric()
                            ->prefix('€')
                            ->default(0)
                            ->debounce(500)          // ← wait 500ms after user stops typing
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                self::recalcToBePaid($get, $set);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('LEVERING')
                            ->label(__('levering'))
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('total_display')
                            ->label(__('invoice total'))
                            ->content(function (Get $get) {
                                $items    = $get('items') ?? [];
                                $subtotal = collect($items)->sum(function ($item) {
                                    return (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
                                });
                                $delivery = (float)($get('delivery_fee') ?? 0);
                                $total    = (float) $subtotal + (float) $delivery;
                                return '€ ' . number_format($total, 2);
                            })
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('notes'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('invoice number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label(__('invoice date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('customer'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('seller'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(__('total'))
                    ->money('EUR')
                    ->sortable()
                    ->getStateUsing(function (Invoice $record) {
                        return $record->total;
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('status'))
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending_delete',
                        'danger'  => 'deleted',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('status'))
                    ->options([
                        'active'         => 'Active',
                        'pending_delete' => 'Pending Delete',
                    ])
                    ->default('active'),

                Tables\Filters\Filter::make('created_at')
                    ->label(__('created at'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('from date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('until date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('invoice_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('invoice_date', '<=', $date),
                            );
                    }),
            ])->filtersTriggerAction(
                fn(Action $action) =>
                $action->button()->label(__('filter'))
            )
            ->headerActions([
                ExportAction::make()
                    ->label(__('export'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('invoices-' . date('Y-m-d'))
                            ->withWriterType(\Maatwebsite\Excel\Excel::XLSX)
                            ->modifyQueryUsing(function (Builder $query) {
                                return $query->with(['customer', 'user']);
                            }),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('print'))
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(Invoice $record) => route('invoice.print', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('download_pdf')
                    ->label(__('download pdf'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn(Invoice $record) => $record->downloadPdf()),

                Tables\Actions\Action::make('view_pdf')
                    ->label(__('view pdf'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('send_email')
                    ->label(__('send email'))
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('send email'))
                    ->modalDescription(fn(Invoice $record) => "Send invoice {$record->invoice_number} to {$record->customer->email}?")
                    ->modalSubmitActionLabel(__('send email'))
                    ->visible(fn(Invoice $record) => $record->customer->email && $record->status === 'active')
                    ->action(function (Invoice $record) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($record->customer->email)
                                ->send(new \App\Mail\InvoiceMail($record));

                            \Filament\Notifications\Notification::make()
                                ->title('Email Sent Successfully')
                                ->success()
                                ->body("Invoice sent to {$record->customer->email}")
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Email Failed')
                                ->danger()
                                ->body("Error: {$e->getMessage()}")
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('request_delete')
                    ->label(__('request delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(
                        fn(Invoice $record) =>
                        $record->status === 'active' &&
                            !auth()->user()->hasRole('super_admin')
                    )
                    ->action(function (Invoice $record) {
                        $record->update(['status' => 'pending_delete']);
                    }),

                Tables\Actions\Action::make('approve_delete')
                    ->label(__('approve delete'))
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(
                        fn(Invoice $record) =>
                        $record->status === 'pending_delete' &&
                            auth()->user()->hasRole('super_admin')
                    )
                    ->action(function (Invoice $record) {
                        foreach ($record->items as $item) {
                            $product = $item->product;
                            $product->increment('stock_quantity', $item->quantity);
                        }
                        $record->update(['status' => 'deleted']);
                    }),

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(
                        fn(Invoice $record) =>
                        $record->status === 'active' &&
                            auth()->user()->hasRole('super_admin')
                    ),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->label(__('export selected'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('selected-invoices-' . date('Y-m-d')),
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view'   => Pages\ViewInvoice::route('/{record}'),
            'edit'   => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}
