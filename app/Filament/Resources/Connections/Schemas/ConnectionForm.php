<?php

namespace App\Filament\Resources\Connections\Schemas;

use Filament\Schemas\Schema;

class ConnectionForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Intentionally left empty as we do not allow direct edits
        ]);
    }
}