<?php

namespace App\Filament\Resources\Interests\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\InterestStatus;
use App\Enums\InterestActorType;
use App\Filament\Resources\Interests\InterestResource;

class InterestsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Interest ID')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('actor_type')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('lead.id')
                    ->label('Lead ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider.user.name')
                    ->label('Provider')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false), // Default vabei dekhanor jonno false rakhlam
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(InterestStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name])->toArray()),
                Tables\Filters\SelectFilter::make('actor_type')
                    ->options(collect(InterestActorType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->name])->toArray()),
            ])
            ->actions([]) // Disable per-row buttons
            ->bulkActions([])
            ->recordUrl(
                fn ($record): string => InterestResource::getUrl('view', ['record' => $record]),
            );
    }
}