<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\ServiceRequestStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('customer_id')
                ->label('Customer')
                ->relationship('customer', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('service_id')
                ->label('Service Requested')
                ->relationship('service', 'name')
                ->required()
                ->searchable()
                ->preload(),

            Select::make('status')
                ->label('Request Status')
                ->options(ServiceRequestStatus::class)
                ->default('draft')
                ->required(),

            DateTimePicker::make('preferred_at')
                ->label('Preferred Date & Time'),

            Select::make('location_id')
                ->label('Service Location')
                ->relationship('location', 'label')
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->label . ($record->city ? ' (' . $record->city . ')' : ''))
                ->searchable()
                ->preload()
                // Dropdown er pashei notun address toiri korar feature
                ->createOptionForm([
                    TextInput::make('label')
                        ->label('Address Label')
                        ->placeholder('e.g. Home, Office')
                        ->required(),
                        
                    Textarea::make('address')
                        ->label('Full Address Details')
                        ->required(),
                        
                    TextInput::make('locality')
                        ->label('Locality / Area'),
                        
                    TextInput::make('city')
                        ->label('City')
                        ->required(),
                        
                    TextInput::make('postal_code')
                        ->label('PIN / Postal Code'),
                ]),

            // View korar somoy Full Address dekhanor Magic
            Placeholder::make('full_address')
                ->label('Complete Address Details')
                ->content(function ($record) {
                    // Record ba location data na thakle blank dekhabe
                    if (! $record || ! $record->location) {
                        return 'Address not available.';
                    }
                    
                    $loc = $record->location;
                    
                    // Faka data gulo baad diye comma diye jure sundor vabe dekhano
                    return collect([
                        $loc->address, 
                        $loc->locality, 
                        $loc->city, 
                        $loc->state, 
                        $loc->postal_code
                    ])->filter()->join(', ') ?: 'No detailed address provided.';
                })
                // Shudhumatro View page-e eta drisshoman hobe
                ->visible(fn (string $operation): bool => $operation === 'view')
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Customer Requirements')
                ->columnSpanFull(),
        ])->columns(2);
    }
}