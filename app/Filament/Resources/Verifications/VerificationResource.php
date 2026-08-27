<?php

namespace App\Filament\Resources\Verifications;

use App\Enums\VerificationStatus;
use App\Filament\Resources\Verifications\Pages;
use App\Models\Verification;
use Filament\Actions\Action;
use Filament\Actions\ViewAction as PageViewAction; // Avoid naming conflict
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VerificationResource extends Resource
{
    protected static ?string $model = Verification::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';
    protected static \UnitEnum|string|null $navigationGroup = 'Trust & Safety';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user.name')
                    ->label('User')
                    ->disabled(),
                TextInput::make('type')
                    ->label('Verification Type')
                    ->disabled(),
                TextInput::make('document_type')
                    ->label('Document Type')
                    ->disabled(),
                TextInput::make('document_reference')
                    ->label('Document Reference / Link')
                    ->disabled(),
                TextInput::make('status')
                    ->disabled(),
                Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->disabled()
                    ->columnSpanFull(),
                DatePicker::make('verified_at')
                    ->disabled(),
                DatePicker::make('expires_at')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable(),
                TextColumn::make('document_type')
                    ->label('Doc Type'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?VerificationStatus $state): string => 
                        $state ? str($state->value ?? $state)->title() : '—'
                    )
                    ->color(fn (?VerificationStatus $state): string => match ($state?->value ?? $state) {
                        'verified' => 'success',
                        'pending', 'submitted', 'under_review' => 'warning',
                        'rejected', 'expired', 'suspended' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // FIX: Added Operational Queue Filter
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        'pending' => 'Pending',
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                
                Action::make('verify')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Verification $record): bool => !in_array($record->status?->value ?? $record->status, ['verified', 'not_required']))
                    ->requiresConfirmation()
                    ->modalHeading('Verify Document')
                    ->modalDescription('Apni ki ei document ti verify korte chan? Eta user er trust score e impact felbe.')
                    ->action(function (Verification $record): void {
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                            'rejection_reason' => null,
                        ]);
                        
                        // FIX: Added Notification for Admin
                        Notification::make()
                            ->title('Document Verified')
                            ->body('The provider document has been successfully verified.')
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Verification $record): bool => !in_array($record->status?->value ?? $record->status, ['rejected']))
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->helperText('User ke ki karone reject kora hocche seta janiye din.'),
                    ])
                    ->action(function (array $data, Verification $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'verified_at' => null,
                        ]);

                        // FIX: Added Notification for Admin
                        Notification::make()
                            ->title('Document Rejected')
                            ->body('The document has been rejected and reason saved.')
                            ->danger()
                            ->send();
                    })
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifications::route('/'),
            'view' => Pages\ViewVerification::route('/{record}'),
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
