<?php

namespace App\Filament\Resources\Complaints;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Filament\Resources\Complaints\Pages;
use App\Models\Complaint;
use Filament\Actions\Action;
use Filament\Actions\ViewAction as PageViewAction; 
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static \UnitEnum|string|null $navigationGroup = 'Trust & Safety';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')->disabled(),
                TextInput::make('type')->disabled(),
                TextInput::make('category')->disabled(),
                Select::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])->disabled(),
                Textarea::make('description')->columnSpanFull()->disabled(),
                Textarea::make('resolution')->columnSpanFull()->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('complainant.name')->label('Complainant')->searchable(),
                TextColumn::make('againstUser.name')->label('Against User')->searchable(),
                TextColumn::make('category')->searchable()->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state?->value ?? $state))
                    ->color(fn ($state): string => match ($state?->value ?? $state) {
                        'urgent', 'high' => 'danger',
                        'medium' => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state?->value ?? $state)))
                    ->color(fn ($state): string => match ($state?->value ?? $state) {
                        'resolved', 'closed' => 'success',
                        'under_review', 'investigating' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                // FIX: Added Operational Status Filter
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'pending' => 'Pending',
                        'investigating' => 'Investigating',
                        'resolved' => 'Resolved',
                        'closed' => 'Closed',
                    ]),
                // FIX: Added Operational Priority Filter to spot Urgent issues quickly
                SelectFilter::make('priority')
                    ->label('Filter by Priority')
                    ->options([
                        'urgent' => 'Urgent',
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                
                Action::make('resolveComplaint')
                    ->label('Resolve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Complaint $record): bool => !in_array($record->status?->value ?? $record->status, ['resolved', 'closed']))
                    ->form([
                        Textarea::make('resolution')
                            ->label('Resolution Notes')
                            ->required()
                            ->helperText('Ki bhabe problem solve kora holo tar details ekhane likhun.'),
                    ])
                    ->action(function (array $data, Complaint $record): void {
                        $record->update([
                            'status' => 'resolved',
                            'resolution' => $data['resolution'],
                            'resolved_by' => auth()->id(),
                            'resolved_at' => now(),
                        ]);

                        // FIX: Added Notification for Admin
                        Notification::make()
                            ->title('Complaint Resolved')
                            ->body('The dispute has been marked as resolved.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'view' => Pages\ViewComplaint::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}