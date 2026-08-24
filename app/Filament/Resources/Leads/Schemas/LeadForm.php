<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('service_request_id')
                ->label('Linked Service Request')
                ->relationship('serviceRequest', 'id')
                ->getOptionLabelFromRecordUsing(fn ($record) => 'REQ-' . $record->id . ' (' . $record->service?->name . ')')
                ->disabled() 
                ->required(),

            Select::make('status')
                ->label('Lead Status')
                ->options(LeadStatus::class)
                ->disabled() // STATE MACHINE PROCTECTION: Must not be manually edited
                ->required(),

            TextInput::make('quality_score')
                ->label('Lead Quality Score')
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->suffix('%')
                ->disabled(), // System generated

            DateTimePicker::make('distributed_at')
                ->label('Distributed to Providers At')
                ->disabled(),

            DateTimePicker::make('expires_at')
                ->label('Lead Expiry Time')
                ->disabled(),

            DateTimePicker::make('converted_at')
                ->label('Converted to Booking At')
                ->disabled(),
                
        ])->columns(2);
    }
}