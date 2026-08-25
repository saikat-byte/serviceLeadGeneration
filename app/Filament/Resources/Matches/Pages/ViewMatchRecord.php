<?php

namespace App\Filament\Resources\Matches\Pages;

use App\Filament\Resources\Matches\MatchRecordResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMatchRecord extends ViewRecord
{
    protected static string $resource = MatchRecordResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Amra pore jodi kono specific admin control lage tokhon Action add korbo
    }
}