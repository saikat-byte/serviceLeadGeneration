<?php

namespace App\Filament\Resources\ServiceRequests\Tables;

use App\Enums\ServiceRequestStatus;
use App\Models\ServiceRequest;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
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
                        $state ? str($state->value ?? $state)->title() : '—'
                    )
                    ->color(fn (?ServiceRequestStatus $state): string => match ($state?->value ?? $state) {
                        'draft', 'pending' => 'gray',
                        'submitted', 'validating' => 'warning',
                        'qualified' => 'primary',
                        'converted' => 'success',
                        'cancelled', 'rejected', 'expired' => 'danger',
                        default => 'gray',
                    }),

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
                
                Action::make('cancelRequest')
                    ->label('Cancel Request')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ServiceRequest $record): bool => in_array(
                        is_object($record->status) ? $record->status->value : $record->status,
                        ['pending', 'submitted', 'validating', 'qualified']
                    ))
                    ->form([
                        // FIX: Form field er nam 'reason' dewa holo jate model er sathe conflict na hoy
                        Textarea::make('reason')
                            ->label('Reason for Cancellation')
                            ->required()
                            ->helperText('Admin intervention: Ei request ti keno cancel kora hocche tar reason likhun.'),
                    ])
                    ->action(function (array $data, ServiceRequest $record): void {
                        $record->cancellation_reason = $data['reason'];
                        // FIX: Raw string er bodole Enum theke convert kora holo
                        $record->status = \App\Enums\ServiceRequestStatus::from('cancelled');
                        $record->save();

                        Notification::make()
                            ->title('Request Cancelled')
                            ->body('The service request has been manually cancelled by Admin.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Service Request')
                    ->modalDescription('Apni ki nishchit bhabe ei pending request ti cancel korte chan?'),

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