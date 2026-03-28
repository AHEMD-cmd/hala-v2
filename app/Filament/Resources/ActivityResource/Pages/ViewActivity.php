<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Activity Details')
                    ->schema([
                        Infolists\Components\TextEntry::make('log_name')
                            ->label('Log Type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'invoice' => 'success',
                                'product' => 'info',
                                'expense' => 'warning',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Event'),

                        Infolists\Components\TextEntry::make('subject_type')
                            ->label('Model')
                            ->formatStateUsing(fn ($state) => class_basename($state)),

                        Infolists\Components\TextEntry::make('subject_id')
                            ->label('Record ID'),

                        Infolists\Components\TextEntry::make('causer.name')
                            ->label('Performed By')
                            ->default('System'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Date & Time')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Changes')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('properties.attributes')
                            ->label('New Values')
                            ->columnSpanFull()
                            ->hidden(fn ($record) => empty($record->properties['attributes'] ?? [])),

                        Infolists\Components\KeyValueEntry::make('properties.old')
                            ->label('Old Values')
                            ->columnSpanFull()
                            ->hidden(fn ($record) => empty($record->properties['old'] ?? [])),
                    ])
                    ->collapsible(),
            ]);
    }
}