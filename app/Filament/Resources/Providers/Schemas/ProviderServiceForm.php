<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\ProviderServiceStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProviderServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('status')
                ->label('Service Status')
                ->options(ProviderServiceStatus::class)
                ->required(),
            TextInput::make('starting_price')
                ->numeric()
                ->prefix('₹')
                ->disabled(), // Pricing provider set korbe, Admin shudhu dekhbe
            Textarea::make('notes')
                ->label('Admin Remarks / Notes')
                ->placeholder('Add any remarks regarding approval or suspension...'),
        ]);
    }
}