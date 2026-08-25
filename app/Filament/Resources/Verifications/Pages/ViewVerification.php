<?php

namespace App\Filament\Resources\Verifications\Pages;

use App\Filament\Resources\Verifications\VerificationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewVerification extends ViewRecord
{
    protected static string $resource = VerificationResource::class;
    
    protected function getHeaderActions(): array
    {
        return []; // Remove edit action from view page
    }
}