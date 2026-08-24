<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?BookingStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?BookingStatus $state): string => match ($state?->value) {
                        'pending', 'payment_pending' => 'warning',
                        'confirmed', 'provider_assigned', 'scheduled' => 'info',
                        'on_the_way', 'arrived', 'work_started' => 'primary',
                        'work_completed', 'paid', 'closed' => 'success',
                        'cancelled', 'no_show', 'disputed', 'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('final_amount')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(BookingStatus::class),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                // Avoid bulk deletes for core business data
            ]);
    }
}