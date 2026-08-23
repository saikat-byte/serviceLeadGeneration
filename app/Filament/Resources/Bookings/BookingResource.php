<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Filament\Forms;
use Filament\Schemas\Schema; // 🟢 Form এর বদলে Schema
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    // 🟢 Updated type-hint for Filament 5.7+
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';
    
    // 🟢 Updated type-hint for Navigation Group
    protected static \UnitEnum|string|null $navigationGroup = 'Marketplace Operations';
    protected static ?int $navigationSort = 1;

    // 🟢 Form এর বদলে Schema ব্যবহার করা হয়েছে
    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->disabled(),
                Forms\Components\Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->disabled(),
                Forms\Components\Select::make('service_id')
                    ->relationship('service', 'name')
                    ->disabled(),
                Forms\Components\TextInput::make('status')
                    ->disabled(),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->prefix('₹')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('scheduled_at')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable(),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    // 🟢 Enum type handle করা হয়েছে
                    ->color(fn (?BookingStatus $state): string => match ($state?->value) {
                        'pending' => 'warning',
                        'confirmed', 'scheduled', 'provider_arrived' => 'info',
                        'work_started' => 'primary',
                        'work_completed', 'paid', 'closed' => 'success',
                        'cancelled_by_customer', 'cancelled_by_provider' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BookingStatus::class),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Avoid bulk deletes for core business data
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBooking::route('/{record}'),
        ];
    }
}