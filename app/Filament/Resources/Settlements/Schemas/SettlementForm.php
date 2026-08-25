<?php

namespace App\Filament\Resources\Settlements\Schemas;

use App\Enums\SettlementStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SettlementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Provider Info')->schema([
                Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->label('Provider')
                    ->disabled(),
            ])->columns(1), // Ekta field, tai column 1 kora holo

            Section::make('Financial Breakdown')->schema([
                TextInput::make('gross_amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('platform_fee')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('net_amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('currency')
                    ->disabled(),
            ])->columns(2),

            Section::make('Payout & Status Management')->schema([
                Select::make('status')
                    ->label('Settlement Status')
                    ->options(SettlementStatus::class)
                    ->required(),
                TextInput::make('payout_reference')
                    ->label('Bank UTR / Payout Reference')
                    ->placeholder('Enter bank UTR number'),
                DateTimePicker::make('settled_at')
                    ->label('Settled At'),
            ])->columns(3),
        ]);
    }
}