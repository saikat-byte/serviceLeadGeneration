<?php

namespace App\Filament\Resources\Providers\RelationManagers;

use App\Filament\Resources\Providers\Schemas\ProviderServiceForm;
use App\Filament\Resources\Providers\Tables\ProviderServicesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProviderServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'providerServices';
    protected static ?string $title = 'Offered Services';

    public function form(Schema $schema): Schema
    {
        return ProviderServiceForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ProviderServicesTable::configure($table);
    }
}