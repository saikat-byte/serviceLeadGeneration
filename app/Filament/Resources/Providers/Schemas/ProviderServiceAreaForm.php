<?php

namespace App\Filament\Resources\Providers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProviderServiceAreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('locality'),
            TextInput::make('city'),
            TextInput::make('postal_code'),
            TextInput::make('radius_km')
                ->numeric()
                ->suffix('km'),
            TextInput::make('latitude')
                ->numeric(),
            TextInput::make('longitude')
                ->numeric(),
        ])->columns(2);
    }
}