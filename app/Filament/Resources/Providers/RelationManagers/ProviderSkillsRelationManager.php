<?php

namespace App\Filament\Resources\Providers\RelationManagers;

use App\Filament\Resources\Providers\Schemas\ProviderSkillForm;
use App\Filament\Resources\Providers\Tables\ProviderSkillsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProviderSkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'providerSkills';
    protected static ?string $title = 'Skills & Verification';

    public function form(Schema $schema): Schema
    {
        return ProviderSkillForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ProviderSkillsTable::configure($table);
    }
}