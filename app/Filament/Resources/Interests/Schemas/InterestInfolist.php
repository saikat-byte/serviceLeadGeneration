<?php

namespace App\Filament\Resources\Interests\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InterestInfolist
{
    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Interest Status')
                ->description('Current state of this provider interest')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('id')
                            ->label('Interest ID')
                            ->weight('bold'),
                            
                        TextEntry::make('status')
                            ->badge(),
                            
                        TextEntry::make('actor_type')
                            ->label('Action By')
                            ->badge()
                            ->color('gray'),
                    ]),
                ]),

            Section::make('Related Entities')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('lead.id')
                            ->label('Lead ID')
                            ->url(fn ($record) => $record->lead_id ? '/admin/leads/' . $record->lead_id : null)
                            ->color('primary'),
                            
                        TextEntry::make('provider.user.name')
                            ->label('Provider Name')
                            ->icon('heroicon-m-briefcase'),
                            
                        TextEntry::make('match_record_id')
                            ->label('Match Record ID')
                            ->url(fn ($record) => $record->match_record_id ? '/admin/matches/' . $record->match_record_id : null)
                            ->color('primary'),
                    ]),
                ]),

            Section::make('Timeline')
                ->schema([
                    Grid::make(2)->schema([
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label('Expressed At'),
                            
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->label('Last Updated'),
                    ]),
                ]),
        ]);
    }
}