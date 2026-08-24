<?php

namespace App\Filament\Resources\Providers\Tables;

use App\Enums\ProviderServiceStatus;
use Filament\Actions\EditAction; 
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProviderServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?ProviderServiceStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?ProviderServiceStatus $state): string => match ($state?->value) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'suspended'=> 'danger',
                        default    => 'gray',
                    }),
                TextColumn::make('starting_price')
                    ->money('INR')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Admin manually add korbe na, provider app theke apply korbe
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}