<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Record create howar pore optimize kora hobe
        if (!empty($this->record->avatar)) {
            $path = app(ImageService::class)->processUserAvatar(
                $this->record->avatar,
                $this->record->id,
                $this->record->name
            );

            $this->record->update(['avatar' => $path]);
        }
    }
}