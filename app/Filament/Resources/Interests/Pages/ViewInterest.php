<?php

namespace App\Filament\Resources\Interests\Pages;

use App\Filament\Resources\Interests\InterestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewInterest extends ViewRecord
{
    protected static string $resource = InterestResource::class;

    protected function getHeaderActions(): array
    {
        return []; // No header actions for now
    }
}