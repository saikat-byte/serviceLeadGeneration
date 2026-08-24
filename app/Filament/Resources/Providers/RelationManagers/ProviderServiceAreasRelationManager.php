<?php

namespace App\Filament\Resources\Providers\RelationManagers;

use App\Filament\Resources\Providers\Schemas\ProviderServiceAreaForm;
use App\Filament\Resources\Providers\Tables\ProviderServiceAreasTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProviderServiceAreasRelationManager extends RelationManager
{
    protected static string $relationship = 'providerServiceAreas';
    protected static ?string $title = 'Service Areas';

    public function form(Schema $schema): Schema
    {
        return ProviderServiceAreaForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ProviderServiceAreasTable::configure($table);
    }
}