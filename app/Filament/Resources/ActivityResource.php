<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource implements HasShieldPermissions 
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getNavigationLabel(): string
    {
        return __('Activity Log');
    }

    public static function getNavigationGroup(): string
    {
        return __('System');
    }

    public static function getModelLabel(): string
    {
        return __('Activity Log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Activity Logs');
    }

    protected static ?int $navigationSort = 99;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'inventory']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->label('Log Name')
                    ->disabled(),

                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->disabled(),

                Forms\Components\TextInput::make('subject_type')
                    ->label('Subject Type')
                    ->disabled(),

                Forms\Components\TextInput::make('subject_id')
                    ->label('Subject ID')
                    ->disabled(),

                Forms\Components\TextInput::make('causer_type')
                    ->label('Causer Type')
                    ->disabled(),

                Forms\Components\TextInput::make('causer_id')
                    ->label('Causer ID')
                    ->disabled(),

                Forms\Components\Textarea::make('properties')
                    ->label(__('properties'))
                    ->disabled()
                    ->columnSpanFull(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label(__('created at'))
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label(__('Log Type'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'invoice' => 'success',
                        'product' => 'info',
                        'expense' => 'warning',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Event'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label(__('Model'))
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label(__('ID'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label(__('User'))
                    ->default('System')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Date & Time'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label(__('Log Type'))
                    ->options([
                        'invoice' => 'Invoice',
                        'product' => 'Product',
                        'expense' => 'Expense',
                        'customer' => 'Customer',
                        'salary' => 'Salary',
                        'special_order' => 'Special Order',
                    ]),

                Tables\Filters\Filter::make('created_at')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // لا نسمح بالحذف الجماعي للـ Activity Log
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListActivities::route('/'),
            'view' => Pages\ViewActivity::route('/{record}'),
        ];
    }

    // منع الإنشاء والتعديل — Activity Log للقراءة فقط
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }
}
