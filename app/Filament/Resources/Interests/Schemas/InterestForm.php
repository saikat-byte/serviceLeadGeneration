<?php

namespace App\Filament\Resources\Interests\Schemas;

use Filament\Schemas\Schema;

class InterestForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Intentionally left empty as we do not allow direct edits
        ]);
    }
}