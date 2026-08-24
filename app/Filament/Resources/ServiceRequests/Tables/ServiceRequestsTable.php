<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Enums\ServiceRequestStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Request ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('service.name')
                    ->label('Service')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?ServiceRequestStatus $state): string => 
                        $state ? str($state->value)->title() : '—'
                    )
                    ->color(fn (?ServiceRequestStatus $state): string => match ($state?->value) {
                        'draft', 'pending' => 'gray',
                        'submitted', 'validating' => 'warning',
                        'qualified' => 'primary',
                        'converted' => 'success',
                        'cancelled', 'rejected', 'expired' => 'danger',
                        default => 'gray',
                    }),

                // Ekhaneo correct database column dewa holo
                TextColumn::make('preferred_at')
                    ->label('Preferred Date')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ServiceRequestStatus::class), 
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