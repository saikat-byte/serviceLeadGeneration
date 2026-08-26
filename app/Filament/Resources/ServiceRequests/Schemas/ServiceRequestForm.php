<?php

namespace App\Filament\Resources\ServiceRequests\Schemas;

use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;
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
                ->preload()
                ->live(),

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

            // FIX: Removed unnecessary formatting that was breaking the View page
            DateTimePicker::make('preferred_at')
                ->label('Preferred Date & Time'),
                
            // FIX: Perfected visibility logic
            Textarea::make('cancellation_reason')
                ->label('Cancellation Reason')
                ->disabled()
                ->columnSpanFull()
                ->visible(fn (?ServiceRequest $record): bool => 
                    $record !== null && 
                    in_array($record->status?->value ?? $record->status, ['cancelled', 'rejected'])
                ),

          Select::make('location_id')
                ->label('Service Location')
                ->relationship('location', 'label', function (\Illuminate\Database\Eloquent\Builder $query, $get) {
                    $customerId = $get('customer_id');
                    if ($customerId) {
                        // NOTE: Jodi apnar locations table e customer er jonno 'customer_id' column thake, tahole nicher 'user_id' ta change kore 'customer_id' kore diben.
                        $query->where('user_id', $customerId); 
                    }
                })
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->label . ($record->city ? ' (' . $record->city . ')' : ''))
                ->searchable()
                ->preload()
                // FIX: Sothik function use kora holo jeta nirdishto customer er sathe address link korbe
                ->createOptionUsing(function (array $data, $get) {
                    // Ekhan theke data create kora hobe 
                    $data['user_id'] = $get('customer_id'); // Ekhaneo 'user_id' er bodole 'customer_id' dite paren jodi DB te tai thake
                    
                    // Location model call kore directly save kora hocche
                    return \App\Models\Location::create($data)->id;
                })
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

            Placeholder::make('full_address')
                ->label('Complete Address Details')
                ->content(function (?ServiceRequest $record) {
                    if (! $record || ! $record->location) {
                        return 'Address not available.';
                    }
                    
                    $loc = $record->location;
                    
                    return collect([
                        $loc->address, 
                        $loc->locality, 
                        $loc->city, 
                        $loc->state, 
                        $loc->postal_code
                    ])->filter()->join(', ') ?: 'No detailed address provided.';
                })
                ->visible(fn (string $operation): bool => $operation === 'view')
                ->columnSpanFull(),

            Textarea::make('description')
                ->label('Customer Requirements')
                ->columnSpanFull(),
        ])->columns(2);
    }
}