<?php

namespace App\Filament\Resources\Connections\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\ConnectionStatus;
use App\Filament\Resources\Connections\ConnectionResource;

class ConnectionsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name') // Customer usually User model er sathe related thake
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('provider.user.name')
                    ->label('Provider')
                    ->searchable(),

                Tables\Columns\TextColumn::make('lead.id')
                    ->label('Lead ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(ConnectionStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name])->toArray()),
            ])
            ->actions([]) // Row actions faka
            ->bulkActions([])
            ->recordUrl(
                fn ($record): string => ConnectionResource::getUrl('view', ['record' => $record]),
            );
    }
}