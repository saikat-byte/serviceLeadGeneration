<?php

namespace App\Filament\Resources\Matches;

use App\Filament\Resources\Matches\Pages;
use App\Filament\Resources\Matches\Schemas\MatchRecordForm;
use App\Filament\Resources\Matches\Schemas\MatchRecordInfolist;
use App\Filament\Resources\Matches\Tables\MatchRecordsTable;
use App\Models\MatchRecord;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MatchRecordResource extends Resource
{
    protected static ?string $model = MatchRecord::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static \UnitEnum|string|null $navigationGroup = 'Matching & Connections'; 
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Match';
    protected static ?string $pluralModelLabel = 'Matches';

    public static function form(Schema $schema): Schema
    {
        return MatchRecordForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return MatchRecordsTable::table($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MatchRecordInfolist::infolist($schema);
    }

    public static function getRelations(): array
    {
        return [
            // Relation Managers (jodi pore dorkar hoy) amra ekhane add korbo
        ];
    }

    public static function getPages(): array
    {
        // Strictly read-only pages
        return [
            'index' => Pages\ListMatchRecords::route('/'),
            'view'  => Pages\ViewMatchRecord::route('/{record}'),
        ];
    }
}