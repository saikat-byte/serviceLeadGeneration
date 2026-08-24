<?php

namespace App\Filament\Resources\Transactions\Tables;

use App\Enums\TransactionStatus;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'payment', 'credit' => 'success',
                        'refund', 'payout', 'debit' => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('customer_id')
                    ->label('Customer ID')
                    ->sortable(),
                TextColumn::make('provider_id')
                    ->label('Provider ID')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('INR')
                    ->sortable(),
                TextColumn::make('gateway')
                    ->label('Gateway')
                    ->searchable(),
                TextColumn::make('reference_id')
                    ->label('Reference')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?TransactionStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?TransactionStatus $state): string => match ($state?->value) {
                        'success', 'completed' => 'success',
                        'pending', 'processing' => 'warning',
                        'failed', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Time')
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