<?php

namespace App\Filament\Resources\Commissions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CommissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Relation & Model Details')->schema([
                Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->label('Booking ID')
                    ->disabled(),
                Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->label('Provider')
                    ->disabled(),
                TextInput::make('model')
                    ->label('Commission Model')
                    ->disabled(),
                TextInput::make('status')
                    ->label('Status')
                    ->disabled(),
            ])->columns(2),

            Section::make('Calculation & Amounts')->schema([
                TextInput::make('base_amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('rate')
                    ->suffix('%')
                    ->disabled(),
                TextInput::make('amount')
                    ->label('Commission Amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('currency')
                    ->disabled(),
                DateTimePicker::make('earned_at')
                    ->disabled(),
            ])->columns(2),
        ]);
    }
}