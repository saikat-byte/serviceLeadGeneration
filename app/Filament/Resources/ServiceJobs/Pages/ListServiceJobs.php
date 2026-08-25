<?php

namespace App\Filament\Resources\ServiceJobs\Pages;

use App\Filament\Resources\ServiceJobs\ServiceJobResource;
use Filament\Resources\Pages\ListRecords;

class ListServiceJobs extends ListRecords
{
    protected static string $resource = ServiceJobResource::class;

    protected function getHeaderActions(): array
    {
        // No CreateAction
        return [];
    }
}