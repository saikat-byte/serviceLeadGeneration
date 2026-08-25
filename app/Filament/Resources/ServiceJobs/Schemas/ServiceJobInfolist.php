<?php

namespace App\Filament\Resources\ServiceJobs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceJobInfolist
{
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Job Information')
                ->description('Core details of the service job')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('id')
                            ->label('Job ID')
                            ->weight('bold'),
                            
                        TextEntry::make('status')
                            ->badge(),
                            
                        TextEntry::make('booking.id')
                            ->label('Booking ID')
                            ->url(fn ($record) => $record->booking ? '/admin/bookings/' . $record->booking_id : null)
                            ->color('primary'),
                    ]),
                ]),

            Section::make('Participants & Service')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('booking.customer.name')
                            ->label('Customer Name')
                            ->icon('heroicon-m-user'),
                            
                        TextEntry::make('booking.provider.name')
                            ->label('Provider Name')
                            ->icon('heroicon-m-briefcase'),
                            
                        TextEntry::make('booking.service.name')
                            ->label('Service')
                            ->icon('heroicon-m-wrench-screwdriver'),
                    ]),
                ]),

                Section::make('Operational Timestamps')
                ->schema([
                    Grid::make(3)->schema([
                        // FIX: scheduled_at ke booking.scheduled_at kora holo
                        TextEntry::make('booking.scheduled_at')
                            ->dateTime()
                            ->label('Scheduled Time'),
                            
                        TextEntry::make('started_at')
                            ->dateTime()
                            ->placeholder('Not started yet')
                            ->label('Started At'),
                            
                        TextEntry::make('completed_at')
                            ->dateTime()
                            ->placeholder('Not completed yet')
                            ->label('Completed At'),
                    ]),
                ]),
        ]);
    }
}