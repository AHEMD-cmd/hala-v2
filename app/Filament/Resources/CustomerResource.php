<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class CustomerResource extends Resource implements HasShieldPermissions 
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    // protected static ?int $navigationSort = 2; // in order for it to work you need to add this line all the resources at in the group


    public static function getNavigationLabel(): string
    {
        return __('Customers');
    }

    public static function getNavigationGroup(): string
    {
        return __('Sales');
    }

    public static function getModelLabel(): string
    {
        return __('Customer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Customers');
    }

    public static function getNavigationBadge(): ?string
    {
        return Customer::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label(__('email'))
                    ->email()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label(__('phone'))
                    ->tel()
                    ->maxLength(255),

                Forms\Components\Textarea::make('address')
                    ->label(__('address'))
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('post_code')
                    ->label(__('post code'))
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('city')
                    ->label(__('city'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('post_code')
                    ->label(__('post code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('city')
                    ->label(__('city'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('email'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('phone'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('invoices_count')
                    ->label(__('invoices count'))
                    ->counts('invoices')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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
            // 'delete_any',
        ];
    }
}