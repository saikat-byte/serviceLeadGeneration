<?php

namespace App\Filament\Resources\Connections\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConnectionInfolist
{
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Connection Details')
                ->description('Current state of this connection')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('id')
                            ->label('Connection ID')
                            ->weight('bold'),
                            
                        TextEntry::make('status')
                            ->badge(),
                    ]),
                ]),

            Section::make('Participants & Source')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('customer.name')
                            ->label('Customer Name')
                            ->icon('heroicon-m-user'),
                            
                        TextEntry::make('provider.user.name')
                            ->label('Provider Name')
                            ->icon('heroicon-m-briefcase')
                            ->default('Not Assigned'),
                            
                        TextEntry::make('lead.id')
                            ->label('Source Lead ID')
                            ->color('primary'),
                            
                        TextEntry::make('match_record_id')
                            ->label('Match Record ID')
                            ->color('primary')
                            ->default('N/A'),
                    ]),
                ]),

            Section::make('Timeline')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Created At'),
                            
                        TextEntry::make('unlocked_at')
                            ->dateTime()
                            ->placeholder('Not unlocked yet')
                            ->label('Unlocked At'),
                            
                        TextEntry::make('activated_at')
                            ->dateTime()
                            ->placeholder('Not activated yet')
                            ->label('Activated At'),
                    ]),
                ]),

            Section::make('Operational History')
                ->description('Chronological state transition events for this connection.')
                ->schema([
                    RepeatableEntry::make('history') // Nam change kore history dilam
                        ->hiddenLabel()
                        ->state(function ($record) {
                            // Backend e kono touch na kore amra directly data fetch korchi
                            return \App\Models\StateTransition::where('entity_type', $record->getMorphClass())
                                ->where('entity_id', $record->id)
                                ->orderBy('created_at', 'desc') // Latest ta upore thakbe
                                ->get()
                                ->toArray();
                        })
                        ->schema([
                            Grid::make(3)->schema([
                                TextEntry::make('created_at')
                                    ->label('Time')
                                    ->dateTime()
                                    ->icon('heroicon-m-clock'),
                                    
                                TextEntry::make('to_state') // Trait er nam onujayi to_state
                                    ->label('New State')
                                    ->badge()
                                    ->color('success'),
                                    
                                TextEntry::make('event') // Event nam tao dekhiye dilam
                                    ->label('Event')
                                    ->badge()
                                    ->color('info'),
                                    
                                TextEntry::make('reason')
                                    ->label('Action / Reason')
                                    ->default('System transition'),
                            ])
                        ])
                ])
                ->collapsible(),
        ]);
    }
}