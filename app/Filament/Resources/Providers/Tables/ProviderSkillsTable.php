<?php

namespace App\Filament\Resources\Providers\Tables;

use App\Enums\SkillVerificationStatus;
use Filament\Actions\CreateAction; // 🟢 Corrected Namespace
use Filament\Actions\EditAction;   // 🟢 Corrected Namespace
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProviderSkillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('skill.name')
                    ->label('Skill')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('experience_years')
                    ->label('Experience (Years)')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('verification_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?SkillVerificationStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?SkillVerificationStatus $state): string => match ($state?->value) {
                        'verified' => 'success',
                        'pending'  => 'warning',
                        'unverified' => 'gray',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Skill'),
            ])
            ->actions([
                EditAction::make(),
            ]);
    }
}