<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalaryResource\Pages;
use App\Models\Salary;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class SalaryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Salary::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function getNavigationLabel(): string
    {
        return __('Salaries');
    }

    public static function getNavigationGroup(): string
    {
        return __('Finance');
    }

    public static function getModelLabel(): string
    {
        return __('Salary');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Salaries');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_name')
                    ->label(__('employee'))
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label(__('amount'))
                    ->required()
                    ->numeric()
                    ->prefix('€'),

                Forms\Components\DatePicker::make('month')
                    ->label(__('month'))
                    ->required()
                    ->displayFormat('F Y')
                    ->default(now()->startOfMonth()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_name')
                    ->label(__('employee'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('amount'))
                    ->money('EUR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('month')
                    ->label(__('month'))
                    ->date('F Y')
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
            'index' => Pages\ListSalaries::route('/'),
            'create' => Pages\CreateSalary::route('/create'), // if you want to use modal just comment this line
            'edit' => Pages\EditSalary::route('/{record}/edit'), // if you want to use modal just comment this line
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
