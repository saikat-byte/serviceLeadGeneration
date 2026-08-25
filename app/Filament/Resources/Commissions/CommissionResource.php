<?php

namespace App\Filament\Resources\Commissions;

use App\Filament\Resources\Commissions\Pages;
use App\Filament\Resources\Commissions\Schemas\CommissionForm;
use App\Filament\Resources\Commissions\Tables\CommissionsTable;
use App\Models\Commission;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CommissionResource extends Resource
{
    protected static ?string $model = Commission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static \UnitEnum|string|null $navigationGroup = 'Finance & Revenue';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return CommissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommissionsTable::configure($table);
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
            'index' => Pages\ListCommissions::route('/'),
            'view' => Pages\ViewCommission::route('/{record}'),
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
        return false; // Added to strictly prevent deletion
    }
}