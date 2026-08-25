<?php

namespace App\Filament\Resources\Matches\Schemas;

use Filament\Schemas\Schema;

class MatchRecordForm
{
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // Intentionally left empty as we do not allow direct edits
        ]);
    }
}