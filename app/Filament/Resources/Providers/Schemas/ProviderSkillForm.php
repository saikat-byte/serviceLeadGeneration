<?php

namespace App\Filament\Resources\Providers\Schemas;

use App\Enums\SkillVerificationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProviderSkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Select::make('skill_id')
                ->relationship('skill', 'name')
                ->label('Skill')
                ->required(),
            TextInput::make('experience_years')
                ->label('Experience (Years)')
                ->numeric(),
            Select::make('verification_status')
                ->label('Verification Status')
                ->options(SkillVerificationStatus::class)
                ->required(),
        ])->columns(2);
    }
}