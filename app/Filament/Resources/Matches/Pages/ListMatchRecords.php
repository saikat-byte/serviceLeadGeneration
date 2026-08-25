<?php

namespace App\Filament\Resources\Matches\Pages;

use App\Filament\Resources\Matches\MatchRecordResource;
use Filament\Resources\Pages\ListRecords;

class ListMatchRecords extends ListRecords
{
    protected static string $resource = MatchRecordResource::class;

    protected function getHeaderActions(): array
    {
        return []; // No Create action
    }
}