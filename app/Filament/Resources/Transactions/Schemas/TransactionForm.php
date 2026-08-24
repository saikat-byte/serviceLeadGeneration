<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Transaction Overview')->schema([
                TextInput::make('type')
                    ->label('Type')
                    ->disabled(),
                TextInput::make('status')
                    ->label('Status')
                    ->disabled(),
                TextInput::make('customer_id')
                    ->label('Customer ID')
                    ->disabled(),
                TextInput::make('provider_id')
                    ->label('Provider ID')
                    ->disabled(),
            ])->columns(2),

            Section::make('Amount & Reference')->schema([
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('currency')
                    ->disabled(),
                TextInput::make('gateway')
                    ->label('Payment Gateway')
                    ->disabled(),
                TextInput::make('reference_id')
                    ->label('Reference / UTR')
                    ->disabled(),
                DateTimePicker::make('created_at')
                    ->label('Transaction Time')
                    ->disabled(),
            ])->columns(2),
        ]);
    }
}