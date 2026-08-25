<?php

namespace App\Filament\Resources\Matches\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MatchRecordInfolist
{
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Match Information')
                ->description('Core details of the match record')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('id')
                            ->label('Match ID')
                            ->weight('bold'),
                            
                        TextEntry::make('status')
                            ->badge(),
                            
                        TextEntry::make('lead.id')
                            ->label('Lead ID')
                            ->url(fn ($record) => $record->lead_id ? '/admin/leads/' . $record->lead_id : null)
                            ->color('primary'),
                    ]),
                ]),

            Section::make('Provider Information')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('provider.user.name')
                            ->label('Provider Name')
                            ->icon('heroicon-m-briefcase'),
                            
                        TextEntry::make('provider_id')
                            ->label('Provider System ID'),
                    ]),
                ]),

            Section::make('Match Timeline')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Match Created At'),
                            
                        TextEntry::make('offered_at')
                            ->dateTime()
                            ->placeholder('Not offered yet')
                            ->label('Offered At'),
                            
                        TextEntry::make('responded_at')
                            ->dateTime()
                            ->placeholder('No response yet')
                            ->label('Responded At'),
                    ]),
                ]),
        ]);
    }
}