<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages;
use App\Models\Review;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-star';
    protected static \UnitEnum|string|null $navigationGroup = 'Trust & Safety';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_id')
                    ->label('Booking ID')
                    ->disabled(),
                TextInput::make('reviewer_name')
                    ->label('Reviewer (Customer)')
                    ->formatStateUsing(fn (?Review $record): string => $record?->reviewer?->name ?? 'N/A')
                    ->disabled(),
                TextInput::make('reviewee_name')
                    ->label('Reviewee (Provider)')
                    ->formatStateUsing(fn (?Review $record): string => $record?->reviewee?->name ?? 'N/A')
                    ->disabled(),
                TextInput::make('rating')
                    ->label('Rating')
                    ->numeric()
                    ->disabled(),
                TextInput::make('status')
                    ->disabled(),
                Textarea::make('comment')
                    ->label('Review Comment')
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('booking_id')
                    ->label('Booking ID')
                    ->searchable(),
                TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->searchable(),
                TextColumn::make('reviewee.name')
                    ->label('Reviewee')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->sortable()
                    ->formatStateUsing(fn (string $state): string => $state . ' ⭐'),
                TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => 
                        is_object($state) ? str($state->value)->title() : str($state)->title()
                    )
                    ->color(fn ($state): string => match (is_object($state) ? $state->value : $state) {
                        'published' => 'success',
                        'submitted', 'pending' => 'info',
                        'flagged' => 'warning',
                        'removed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 
            ])
            ->actions([
                ViewAction::make(),
                
                Action::make('flag')
                    ->label('Flag')
                    ->icon('heroicon-o-flag')
                    ->color('warning')
                    ->visible(fn (Review $record): bool => in_array(is_object($record->status) ? $record->status->value : $record->status, ['published', 'submitted']))
                    ->requiresConfirmation()
                    ->modalHeading('Flag Review')
                    ->modalDescription('Apni ki ei review ta flag korte chan? Eta pore investigate kora hobe.')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'flagged']);
                    }),

                Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn (Review $record): bool => !in_array(is_object($record->status) ? $record->status->value : $record->status, ['removed']))
                    ->requiresConfirmation()
                    ->modalHeading('Remove Review')
                    ->modalDescription('Ei review ta ki platform theke remove korte chan? Eta ar public thakbe na.')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'removed']);
                    }),

                Action::make('publish')
                    ->label('Publish / Restore')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record): bool => in_array(is_object($record->status) ? $record->status->value : $record->status, ['flagged', 'removed', 'pending', 'submitted']))
                    ->requiresConfirmation()
                    ->modalHeading('Publish Review')
                    ->modalDescription('Ei review ta ki abar platform e publish korte chan?')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'published']);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'view' => Pages\ViewReview::route('/{record}'),
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