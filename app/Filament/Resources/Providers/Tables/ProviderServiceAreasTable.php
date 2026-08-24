<?php

namespace App\Filament\Resources\Providers\Tables;

use Filament\Actions\EditAction; 
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProviderServiceAreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('city')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locality')
                    ->searchable(),
                TextColumn::make('postal_code')
                    ->searchable(),
                TextColumn::make('radius_km')
                    ->label('Radius')
                    ->numeric()
                    ->suffix(' km')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}