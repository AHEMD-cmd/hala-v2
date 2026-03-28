<?php

namespace App\Filament\Resources\SalaryResource\Pages;

use App\Filament\Resources\SalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalary extends CreateRecord
{
    protected static string $resource = SalaryResource::class;

    // protected function preserveFormDataWhenCreatingAnother(array $data): array // in filament v4
    // {
    //     return $data;
    // }
}
