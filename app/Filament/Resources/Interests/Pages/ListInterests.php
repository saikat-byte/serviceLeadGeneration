<?php

namespace App\Filament\Resources\Interests\Pages;

use App\Filament\Resources\Interests\InterestResource;
use Filament\Resources\Pages\ListRecords;

class ListInterests extends ListRecords
{
    protected static string $resource = InterestResource::class;

    protected function getHeaderActions(): array
    {
        return []; // No Create action
    }
}