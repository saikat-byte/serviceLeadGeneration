<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Booking;
use App\Models\ServiceRequest;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(function ($record) {
                    $hasBookings = Booking::where('service_id', $record->id)->exists();
                    $hasRequests = ServiceRequest::where('service_id', $record->id)->exists();
                    
                    return $hasBookings || $hasRequests;
                }),
        ];
    }
}