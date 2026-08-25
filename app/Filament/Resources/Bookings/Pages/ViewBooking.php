<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use App\Services\BookingService;
use App\Enums\BookingStatus;
use Filament\Notifications\Notification;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_booking')
                ->label('Confirm Booking')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm this Booking?')
                ->modalDescription('This will confirm the booking, convert the lead, and automatically generate the Service Job for the provider.')
                // Button sudhumatro 'pending' booking er jonnoi dekhabe
                ->visible(fn ($record) => $record->status === BookingStatus::PENDING)
                ->action(function ($record) {
                    try {
                        // Securely call the domain service
                        app(BookingService::class)->confirm($record, auth()->id());
                        
                        Notification::make()
                            ->title('Booking Confirmed Successfully')
                            ->body('A new Service Job has been generated.')
                            ->success()
                            ->send();
                            
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Failed to Confirm Booking')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

                Action::make('cancel_booking')
                ->label('Cancel Booking')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel this Booking?')
                ->modalDescription('This will cancel the booking and record the cancellation reason.')
                // Sudhu oi booking gulo cancel kora jabe jehglu ekhono completed ba closed hoyni
                ->visible(fn ($record) => !in_array($record->status->value, ['completed', 'closed', 'cancelled', 'paid']))
                ->form([
                    Textarea::make('reason')
                        ->label('Cancellation Reason')
                        ->required()
                        ->helperText('Please specify why this booking is being cancelled.'),
                        
                    TextInput::make('fee')
                        ->label('Cancellation Fee (if any)')
                        ->numeric()
                        ->default(0.00)
                ])
                ->action(function ($record, array $data) {
                    try {
                        app(BookingService::class)->cancel(
                            $record, 
                            auth()->id(), 
                            $data['reason'], 
                            $data['fee'] ?? 0.00
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Booking Cancelled Successfully')
                            ->danger()
                            ->send();
                            
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Failed to Cancel Booking')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}