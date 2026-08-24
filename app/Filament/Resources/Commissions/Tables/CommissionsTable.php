<?php

namespace App\Filament\Resources\Commissions\Tables;

use App\Enums\CommissionStatus; // 🟢 Enum import korun
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CommissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('booking_id')
                    ->label('Booking ID')
                    ->sortable(),
                TextColumn::make('model')
                    ->label('Model')
                    ->badge(),
                TextColumn::make('base_amount')
                    ->money('INR')
                    ->label('Base Amount')
                    ->sortable(),
                TextColumn::make('rate')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('INR')
                    ->label('Commission')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?CommissionStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?CommissionStatus $state): string => match ($state?->value) {
                        'earned', 'settled' => 'success',
                        'pending', 'calculated' => 'warning',
                        'adjusted', 'reversed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('earned_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }
}