<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $slug = 'users'; // to change the path in url from users to something else

    // protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    
    // protected static ?string $recordTitleAttribute = 'name'; // for golbal search and breadcrumbs and in edit page like (edit admin or edit ahmed it depends on name value)

    public static function getNavigationLabel(): string // to change the label in sidebar
    {
        return __('Users');
    }

    public static function getNavigationGroup(): string // to change the group in sidebar
    {
        return __('Settings');
    }

    public static function getModelLabel(): string
    {
        return __('User');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Users');
    }

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('name'))
                            ->required()
                            ->maxLength(255),
                            // ->helperText('under the name field')
                            // ->hiddenLabel()
                            // ->inlineLabel()
                            // ->default('')
                            // ->placeholder('')
                            // ->prefix('')
                            // ->suffix('')
                            // ->autofocus()
                            // ->disabled()
                            // ->disabledOn('create')
                            // ->disabledOn('edit')
                            // ->visibleOn('create')
                            // ->visibleOn('edit')
                            // ->hiddenOn('create')
                            // ->hiddenOn('edit')

                        Forms\Components\TextInput::make('email')
                            ->label(__('email'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label(__('password'))
                            ->password()
                            ->required(fn(string $context): bool => $context === 'create')
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->dehydrated(fn($state) => filled($state))
                            ->revealable()
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('Roles & Permissions'))
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label(__('role'))
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->helperText(__('Select the user role')),

                        Forms\Components\CheckboxList::make('permissions')
                            ->label(__('Additional Permissions'))
                            ->relationship('permissions', 'name', function ($query) {
                                return $query->orderBy('name');
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => __("$record->name"))
                            ->columns(3)
                            ->searchable()
                            ->helperText(__('Grant specific permissions beyond the role')),
                    ])
                    ->columns(1),
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

                Tables\Columns\TextColumn::make('email')
                    ->label(__('email'))
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('role'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'sales' => 'success',
                        'inventory' => 'info',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permissions_count')
                    ->label(__('extra permissions'))
                    ->counts('permissions')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('created at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label(__('email verified at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->relationship('roles', 'name')
                    ->label(__('Filter by Role')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(User $record) => $record->id !== auth()->id()), // لا يمكن حذف نفسك
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records) {
                            // منع حذف المستخدم الحالي
                            $records->filter(fn($record) => $record->id !== auth()->id())->each->delete();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
            // ->reorderableColumns()
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'), // if you want to use modal just comment this line
            'edit' => Pages\EditUser::route('/{record}/edit'), // if you want to use modal just comment this line
        ];
    }

    // public static function canAccess(): bool // to allow or deny access to the resource even if it is not shown in sidebar   
    // {
    //     return auth()->user()->hasRole('super_admin');
    // }

    // public static function shouldRegisterNavigation(): bool // to show or hide the resource in sidebar
    // {
    //     return auth()->user()->hasRole('super_admin');
    // }

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
