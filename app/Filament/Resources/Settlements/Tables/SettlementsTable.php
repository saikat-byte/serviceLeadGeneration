<?php

namespace App\Filament\Resources\Settlements\Tables;

use App\Enums\SettlementStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettlementsTable
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
                TextColumn::make('gross_amount')
                    ->money('INR')
                    ->label('Gross')
                    ->sortable(),
                TextColumn::make('platform_fee')
                    ->money('INR')
                    ->label('Fee')
                    ->sortable(),
                TextColumn::make('net_amount')
                    ->money('INR')
                    ->label('Net Payout')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?SettlementStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?SettlementStatus $state): string => match ($state?->value) {
                        'settled' => 'success',
                        'pending', 'eligible', 'processing' => 'warning',
                        'failed', 'reversed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payout_reference')
                    ->label('UTR / Ref')
                    ->searchable(),
                TextColumn::make('settled_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}