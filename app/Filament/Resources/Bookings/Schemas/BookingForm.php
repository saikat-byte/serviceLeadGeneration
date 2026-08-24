<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('customer_id')
                ->relationship('customer', 'name')
                ->disabled(),
            Select::make('provider_id')
                ->relationship('provider', 'name')
                ->disabled(),
            Select::make('service_id')
                ->relationship('service', 'name')
                ->disabled(),
            TextInput::make('status')
                ->disabled(),
            TextInput::make('estimated_amount')
                ->numeric()
                ->prefix('₹')
                ->disabled(),
            TextInput::make('final_amount')
                ->numeric()
                ->prefix('₹')
                ->disabled(),
            DateTimePicker::make('scheduled_at')
                ->disabled(),
        ])->columns(2);
    }
}