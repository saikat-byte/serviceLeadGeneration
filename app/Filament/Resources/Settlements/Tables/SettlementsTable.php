<?php

namespace App\Filament\Resources\Settlements\Tables;

use App\Enums\SettlementStatus;
use App\Models\Settlement;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action; 
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
                Action::make('processPayout')
                    ->label('Process Payout')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Settlement $record): bool => $record->status?->value !== 'settled')
                    ->form([
                        TextInput::make('payout_reference')
                            ->label('UTR / Bank Reference')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Bank ba Gateway theke paoa successful transaction ID ekhane din.'),
                    ])
                    ->action(function (array $data, Settlement $record): void {
                        $record->update([
                            'status' => 'settled',
                            'payout_reference' => $data['payout_reference'],
                            'settled_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Process Settlement Payout')
                    ->modalDescription('Provider k taka pathanor por ei form ti puron korun. Eta click korar por status "Settled" hoye jabe.'),
            ]);
    }
}