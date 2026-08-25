<?php

namespace App\Filament\Resources\Interests;

use App\Filament\Resources\Interests\Pages\ListInterests;
use App\Filament\Resources\Interests\Pages\ViewInterest;

use App\Filament\Resources\Interests\Schemas\InterestForm;
use App\Filament\Resources\Interests\Schemas\InterestInfolist;
use App\Filament\Resources\Interests\Tables\InterestsTable;
use App\Models\Interest;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class InterestResource extends Resource
{
    protected static ?string $model = Interest::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-hand-raised';
    protected static \UnitEnum|string|null $navigationGroup = 'Matching & Connections'; 
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Provider Interest';
    protected static ?string $pluralModelLabel = 'Provider Interests';

    public static function form(Schema $schema): Schema
    {
        return InterestForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return InterestsTable::table($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InterestInfolist::infolist($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        // Fix: Used the direct class names instead of Pages\...
        return [
            'index' => ListInterests::route('/'),
            'view'  => ViewInterest::route('/{record}'),
        ];
    }
}