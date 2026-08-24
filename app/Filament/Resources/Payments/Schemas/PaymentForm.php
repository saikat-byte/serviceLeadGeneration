<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
// 🟢 Section-er sothik namespace (Filament 5.7 / Schema architecture onujayi)
use Filament\Schemas\Components\Section; 
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Transaction Details')->schema([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label('Customer')
                    ->disabled(),
                Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->label('Booking ID')
                    ->disabled(),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('currency')
                    ->disabled(),
            ])->columns(2),

            Section::make('Payment Status & Gateway')->schema([
                Select::make('status')
                    ->label('Payment Status')
                    ->options(PaymentStatus::class)
                    ->required(),
                TextInput::make('payment_method')
                    ->disabled(),
                TextInput::make('gateway')
                    ->disabled(),
                TextInput::make('gateway_reference')
                    ->label('Transaction Reference')
                    ->disabled(),
                DateTimePicker::make('paid_at')
                    ->disabled(),
            ])->columns(2),
        ]);
    }
}