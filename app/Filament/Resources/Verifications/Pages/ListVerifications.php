<?php

namespace App\Filament\Resources\Verifications\Pages;

use App\Filament\Resources\Verifications\VerificationResource;
use Filament\Resources\Pages\ListRecords;

class ListVerifications extends ListRecords
{
    protected static string $resource = VerificationResource::class;

    protected function getHeaderActions(): array
    {
        return []; // strictly read-only view
    }
}