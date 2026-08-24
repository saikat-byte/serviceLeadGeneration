<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Lead ID')
                    ->sortable()
                    ->searchable(),

                // Customer er nam ta relation theke tene ana holo
                TextColumn::make('serviceRequest.customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('serviceRequest.service.name')
                    ->label('Service Category')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?LeadStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?LeadStatus $state): string => match ($state?->value) {
                        'created', 'matching' => 'gray',
                        'qualified', 'distributed', 'responding' => 'warning',
                        'interested', 'selected' => 'primary',
                        'converted' => 'success',
                        'expired', 'cancelled', 'unfulfilled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('quality_score')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(LeadStatus::class),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}