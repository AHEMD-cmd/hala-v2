<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpecialOrderResource\Pages;
use App\Models\SpecialOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class SpecialOrderResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = SpecialOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    // protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('Special Orders');
    }

    public static function getNavigationGroup(): string
    {
        return __('Sales');
    }

    public static function getModelLabel(): string
    {
        return __('Special Order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Special Orders');
    }

    public static function getNavigationBadge(): ?string
    {
        return SpecialOrder::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->label(__('customer'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\Textarea::make('address'),
                        Forms\Components\TextInput::make('post_code'),
                        Forms\Components\TextInput::make('city'),
                    ]),

                Forms\Components\Textarea::make('product_description')
                    ->label(__('product description'))
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('quantity')
                    ->label(__('quantity'))
                    ->required()
                    ->numeric()
                    ->default(1),

                Forms\Components\Select::make('status')
                    ->label(__('status'))
                    ->options([
                        'pending' => __('pending'),
                        'received' => __('received'),
                        'cancelled' => __('cancelled'),
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label(__('notes'))
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label(__('customer'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('product_description')
                    ->label(__('product'))
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('quantity'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('created by'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ]),
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
            'index' => Pages\ListSpecialOrders::route('/'),
            'create' => Pages\CreateSpecialOrder::route('/create'), // if you want to use modal just comment this line
            'edit' => Pages\EditSpecialOrder::route('/{record}/edit'), // if you want to use modal just comment this line
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
