<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // public function getBreadcrumbs(): array
    // {
    //     return [
    //         ProductResource::getUrl('index') => __('filament-panels::resources/pages/create-record.forms.actions.create.label'),
    //         // or for the index label, use your resource label:
    //         // ProductResource::getModelLabel() 
    //     ];
    // }
}
