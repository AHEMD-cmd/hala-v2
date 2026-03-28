<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->id !== auth()->id())
                ->requiresConfirmation()
                ->modalHeading('Delete User')
                ->modalDescription('Are you sure you want to delete this user? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete user'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User updated')
            ->body('The user has been updated successfully.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // إذا كان Password فاضي، احذفه عشان ميتحدثش
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}