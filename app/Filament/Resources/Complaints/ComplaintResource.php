<?php

namespace App\Filament\Resources\Complaints;

use App\Enums\ComplaintPriority;
use App\Enums\ComplaintStatus;
use App\Filament\Resources\Complaints\Pages;
use App\Models\Complaint;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
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
                Textarea::make('resolution')->columnSpanFull(),
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
                    ->color(fn ($state): string => match ($state?->value ?? $state) {
                        'urgent', 'high' => 'danger',
                        'medium' => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state): string => match ($state?->value ?? $state) {
                        'resolved', 'closed' => 'success',
                        'under_review', 'investigating' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable(),
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
        return false; // Complaints user/system theke generate hoy, admin theke manually banate hobe na
    }

    public static function canEdit($record): bool
    {
        return false; // Strict secure update via Actions only
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}