<?php

namespace App\Filament\Pages\Reports;

use App\Models\ProviderProfile;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ProviderPerformanceReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-star';
    protected static \UnitEnum|string|null $navigationGroup = 'Reports & Analytics';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Provider Performance';

    protected string $view = 'filament.pages.reports.provider-performance-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProviderProfile::query()->with('user')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Provider Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('availability_status')
                    ->label('Status')
                    ->badge()
                    // FIX: Removed strict 'string' type hint and handled Enum value safely
                    ->color(fn ($state): string => match ($state->value ?? $state) {
                        'available' => 'success',
                        'busy' => 'warning',
                        'unavailable' => 'danger',
                        'offline' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('experience_years')
                    ->label('Experience (Yrs)')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('rating_average')
                    ->label('Avg Rating')
                    ->numeric(2)
                    ->sortable()
                    ->icon('heroicon-m-star')
                    ->color('warning'),

                TextColumn::make('completed_jobs_count')
                    ->label('Completed Jobs')
                    ->numeric()
                    ->sortable()
                    ->color('success'),

                TextColumn::make('cancellation_count')
                    ->label('Cancellations')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),
            ])
            ->filters([
                SelectFilter::make('availability_status')
                    ->label('Status Filter')
                    ->options([
                        'available' => 'Available',
                        'busy' => 'Busy',
                        'unavailable' => 'Unavailable',
                        'offline' => 'Offline',
                    ]),
            ])
            ->defaultSort('rating_average', 'desc');
    }
}