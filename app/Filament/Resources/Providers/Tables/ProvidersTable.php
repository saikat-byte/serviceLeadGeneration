<?php

namespace App\Filament\Resources\Providers\Tables;

use App\Enums\UserStatus; // 🟢 Enum import kora holo
use Filament\Actions\EditAction; 
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProvidersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('mobile')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?UserStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?UserStatus $state): string => match ($state?->value) {
                        'active' => 'success',
                        'registered' => 'info',
                        'suspended', 'inactive' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('providerProfile.rating_average')
                    ->label('Rating')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(UserStatus::class), // 🟢 Filter-eo Enum use kora holo
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}