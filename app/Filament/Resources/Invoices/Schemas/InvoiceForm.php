<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Invoice References')->schema([
                TextInput::make('invoice_number')
                    ->label('Invoice Number')
                    ->disabled(),
                Select::make('booking_id')
                    ->relationship('booking', 'id')
                    ->label('Booking ID')
                    ->disabled(),
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->label('Customer')
                    ->disabled(),
                Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->label('Provider')
                    ->disabled(),
            ])->columns(2),

            Section::make('Amount & Financials')->schema([
                TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('platform_fee')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('tax_amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('total_amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                TextInput::make('currency')
                    ->disabled(),
            ])->columns(2),

            Section::make('Status & Timing')->schema([
                Select::make('status')
                    ->label('Invoice Status')
                    ->options([
                        'draft' => 'Draft',
                        'issued' => 'Issued',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->required(),
                DateTimePicker::make('issued_at')
                    ->label('Issued At')
                    ->disabled(),
            ])->columns(2),
        ]);
    }
}