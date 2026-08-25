<?php

namespace App\Filament\Resources\Matches\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\MatchStatus;
use App\Filament\Resources\Matches\MatchRecordResource;

class MatchRecordsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Match ID')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('lead.id')
                    ->label('Lead ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider.user.name')
                    ->label('Provider')
                    ->searchable(),

                Tables\Columns\TextColumn::make('offered_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(MatchStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name])->toArray()),
            ])
            ->actions([]) // Custom Action error avoid korar jonno faka rakha holo
            ->bulkActions([])
            ->recordUrl(
                fn ($record): string => MatchRecordResource::getUrl('view', ['record' => $record]),
            );
    }
}