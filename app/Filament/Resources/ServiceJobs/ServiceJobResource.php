<?php

namespace App\Filament\Resources\ServiceJobs;

use App\Filament\Resources\ServiceJobs\Pages;
use App\Filament\Resources\ServiceJobs\Schemas\ServiceJobForm;
use App\Filament\Resources\ServiceJobs\Schemas\ServiceJobInfolist;
use App\Filament\Resources\ServiceJobs\Tables\ServiceJobsTable;
use App\Models\ServiceJob;
use Filament\Schemas\Schema; // Only Schema is needed for both form and infolist
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ServiceJobResource extends Resource
{
    protected static ?string $model = ServiceJob::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';
    protected static \UnitEnum|string|null $navigationGroup = 'Service Operations'; 
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ServiceJobForm::form($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceJobsTable::table($table);
    }

    // Fix: Changed Infolist to Schema
    public static function infolist(Schema $schema): Schema
    {
        return ServiceJobInfolist::infolist($schema);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServiceJobs::route('/'),
            'view'  => Pages\ViewServiceJob::route('/{record}'),
        ];
    }
}