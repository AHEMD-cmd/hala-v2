<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ProductResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationLabel(): string
    {
        return __('Products');
    }

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getModelLabel(): string
    {
        return __('Product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Products');
    }

    public static function getNavigationBadge(): ?string
    {
        return Product::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('sku')
                    ->label(__('sku'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('name')
                    ->label(__('product name'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label(__('description'))
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('image')
                    ->label(__('product image'))
                    ->image()
                    ->directory('products')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('cost_price')
                    ->label(__('cost price'))
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->visible(fn() => auth()->user()->hasAnyRole(['super_admin', 'inventory'])),

                Forms\Components\TextInput::make('selling_price')
                    ->label(__('selling price'))
                    ->required()
                    ->numeric()
                    ->prefix('€'),

                Forms\Components\TextInput::make('stock_quantity')
                    ->label(__('stock quantity'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('sku'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label(__('product image')),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('product name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cost_price')
                    ->label(__('cost price'))
                    ->money('EUR')
                    ->sortable()
                    ->visible(fn() => auth()->user()->hasAnyRole(['super_admin', 'inventory'])),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label(__('selling price'))
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(__('stock quantity'))
                    ->sortable()
                    ->badge()
                    ->color(fn(int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('out_of_stock')
                    ->label(__('out of stock'))
                    ->query(fn(Builder $query): Builder => $query->where('stock_quantity', 0)),

                Tables\Filters\Filter::make('low_stock')
                    ->label(__('low stock (< 10)'))
                    ->query(fn(Builder $query): Builder => $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<', 10)),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'), // if you want to use modal just comment this line
            'edit' => Pages\EditProduct::route('/{record}/edit'), // if you want to use modal just comment this line
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
