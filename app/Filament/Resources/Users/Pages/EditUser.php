<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\ImageService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        // Jodi notun photo dey tobei compress hobe
        if ($this->record->wasChanged('avatar') && !empty($this->record->avatar)) {
            $path = app(ImageService::class)->processUserAvatar(
                $this->record->avatar,
                $this->record->id,
                $this->record->name
            );

            $this->record->update(['avatar' => $path]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->action(function ($record, $action) {
                    try {
                        $record->delete();
                    } catch (QueryException $e) {
                        if ($e->getCode() === '23000') {
                            Notification::make()
                                ->danger()
                                ->title('Cannot delete user')
                                ->body('This user has active bookings or records. Deletion is blocked.')
                                ->send();
                            $action->halt();
                        } else {
                            throw $e;
                        }
                    }
                }),
        ];
    }
}