<?php

namespace App\Filament\Resources\Settlements;

use App\Filament\Resources\Settlements\Pages;
use App\Filament\Resources\Settlements\Schemas\SettlementForm;
use App\Filament\Resources\Settlements\Tables\SettlementsTable;
use App\Models\Settlement;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SettlementResource extends Resource
{
    protected static ?string $model = Settlement::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-banknotes';
    protected static \UnitEnum|string|null $navigationGroup = 'Finance & Revenue';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SettlementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettlementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettlements::route('/'),
            'create' => Pages\CreateSettlement::route('/create'),
            'view' => Pages\ViewSettlement::route('/{record}'),
            'edit' => Pages\EditSettlement::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Settlements auto-calculated hoy, tai manual create bondho thakbe
    }
}