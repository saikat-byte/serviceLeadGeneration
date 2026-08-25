<?php

namespace App\Filament\Resources\ServiceJobs\Pages;

use App\Filament\Resources\ServiceJobs\ServiceJobResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceJob extends EditRecord
{
    protected static string $resource = ServiceJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
