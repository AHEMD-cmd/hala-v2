<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ExpenseResource extends Resource implements HasShieldPermissions 
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationLabel(): string
    {
        return __('Expenses');
    }

    public static function getNavigationGroup(): string
    {
        return __('Finance');
    }

    public static function getModelLabel(): string
    {
        return __('Expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Expenses');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->label(__('type'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label(__('amount'))
                    ->required()
                    ->numeric()
                    ->prefix('€'),

                Forms\Components\DatePicker::make('date')
                    ->label(__('date'))
                    ->required()
                    ->default(now()),

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
                Tables\Columns\TextColumn::make('type')
                    ->label(__('type'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('amount'))
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('added by'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('from date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('until date')),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('date', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('date', '<=', $data['until']));
                    }),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
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