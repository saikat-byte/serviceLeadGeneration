<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\UserStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProviderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->disabled(),
            TextInput::make('email')
                ->disabled(),
            TextInput::make('mobile')
                ->disabled(),
            Select::make('status')
                ->options(UserStatus::class)
                ->required(),
        ])->columns(2);
    }
}