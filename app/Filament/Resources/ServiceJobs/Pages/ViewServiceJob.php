<?php

namespace App\Filament\Resources\ServiceJobs\Pages;

use App\Filament\Resources\ServiceJobs\ServiceJobResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action; 
use App\Services\JobService;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class ViewServiceJob extends ViewRecord
{
    protected static string $resource = ServiceJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('start_job')
                ->label('Start Job')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Start this Service Job?')
                ->modalDescription('Are you sure you want to mark this job as started?')
                ->visible(fn ($record) => $record->status?->value === 'created' || $record->status === 'created')
                ->action(function ($record) {
                    try {
                        // Sothik method: startWork() ebong auth()->id() pass kora holo as actor ID
                        app(JobService::class)->startWork($record, auth()->id());
                        
                        Notification::make()
                            ->title('Job Started Successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Invalid Transition')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('complete_job')
                ->label('Complete Job')
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->visible(fn ($record) => $record->status?->value === 'started' || $record->status === 'started')
                ->form([
                    TextInput::make('final_value')
                        ->label('Final Service Value Amount')
                        ->numeric()
                        ->required()
                        ->helperText('Enter the final amount for this completed job.'),
                        
                    Textarea::make('notes')
                        ->label('Completion Notes')
                ])
                ->action(function ($record, array $data) {
                    try {
                        // Sothik method: completeWork() ebong data array pass kora holo
                        app(JobService::class)->completeWork($record, auth()->id(), [
                            'final_value' => $data['final_value'],
                            'notes' => $data['notes'] ?? null,
                        ]);
                        
                        Notification::make()
                            ->title('Job Completed Successfully')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Invalid Transition')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}