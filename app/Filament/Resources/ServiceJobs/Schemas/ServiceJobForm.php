<?php

namespace App\Filament\Resources\ServiceJobs\Schemas;

use Filament\Schemas\Schema; // Ekhane Form er bodole Schema import kora holo

class ServiceJobForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // form faka rakhchi karon admin directly create/edit korbe na
        ]);
    }
}