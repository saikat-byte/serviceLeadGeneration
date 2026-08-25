<?php

namespace App\Filament\Resources\Connections;

// Direct imports jate Intelephense error na dey
use App\Filament\Resources\Connections\Pages\ListConnections;
use App\Filament\Resources\Connections\Pages\ViewConnection;

use App\Filament\Resources\Connections\Schemas\ConnectionForm;
use App\Filament\Resources\Connections\Schemas\ConnectionInfolist;
use App\Filament\Resources\Connections\Tables\ConnectionsTable;
use App\Models\Connection;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ConnectionResource extends Resource
{
    protected static ?string $model = Connection::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-link';
    protected static \UnitEnum|string|null $navigationGroup = 'Matching & Connections'; 
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Connection';
    protected static ?string $pluralModelLabel = 'Connections';

    public static function form(Schema $schema): Schema
    {
        return ConnectionForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ConnectionsTable::table($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ConnectionInfolist::infolist($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        // Strictly read-only
        return [
            'index' => ListConnections::route('/'),
            'view'  => ViewConnection::route('/{record}'),
        ];
    }
}