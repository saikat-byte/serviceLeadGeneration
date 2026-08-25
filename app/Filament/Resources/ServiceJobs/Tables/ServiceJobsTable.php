<?php

namespace App\Filament\Resources\ServiceJobs\Tables;

use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\ServiceJobStatus;
use App\Filament\Resources\ServiceJobs\ServiceJobResource;
use Illuminate\Database\Eloquent\Builder;

class ServiceJobsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Job ID')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.customer.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking.provider.name')
                    ->label('Provider')
                    ->searchable(),

                Tables\Columns\TextColumn::make('booking.service.name')
                    ->label('Service')
                    ->searchable()
                    ->limit(20),

                // FIX: scheduled_at ke booking.scheduled_at kora holo
                Tables\Columns\TextColumn::make('booking.scheduled_at')
                    ->label('Scheduled At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(ServiceJobStatus::cases())->mapWithKeys(fn ($status) => [$status->value => $status->name])->toArray()),
                
                Tables\Filters\Filter::make('scheduled_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('scheduled_from'),
                        \Filament\Forms\Components\DatePicker::make('scheduled_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // FIX: relation filter use kora holo
                        return $query->whereHas('booking', function ($q) use ($data) {
                            $q->when(
                                $data['scheduled_from'],
                                fn (Builder $q, $date): Builder => $q->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $q, $date): Builder => $q->whereDate('scheduled_at', '<=', $date),
                            );
                        });
                    })
            ])
            ->actions([])
            ->bulkActions([])
            ->recordUrl(
                fn ($record): string => ServiceJobResource::getUrl('view', ['record' => $record]),
            );
    }
}