<?php

namespace App\Filament\Resources\Providers;

use App\Filament\Resources\Providers\Pages;
use App\Filament\Resources\Providers\RelationManagers\ProviderServiceAreasRelationManager;
use App\Filament\Resources\Providers\RelationManagers\ProviderServicesRelationManager;
use App\Filament\Resources\Providers\RelationManagers\ProviderSkillsRelationManager;
use App\Filament\Resources\Providers\Schemas\ProviderForm;
use App\Filament\Resources\Providers\Tables\ProvidersTable;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProviderResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Provider';
    protected static ?string $pluralModelLabel = 'Providers';
    protected static ?string $slug = 'providers';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static \UnitEnum|string|null $navigationGroup = 'Provider Management';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->hasPermissionTo('ViewAny:User');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasRole('super_admin') || auth()->user()->hasPermissionTo('Update:User');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'provider');
    }

    public static function form(Schema $schema): Schema
    {
        return ProviderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvidersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ProviderServicesRelationManager::class,
            ProviderServiceAreasRelationManager::class,
            ProviderSkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviders::route('/'),
            'edit' => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}