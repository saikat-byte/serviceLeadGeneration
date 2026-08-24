<?php

namespace App\Filament\Resources\Skills\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle; // 🟢 Toggle import kora holo
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SkillForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($set, ?string $state) => $set('slug', Str::slug($state))), 
                
            TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
                
            Toggle::make('is_active') 
                ->label('Active Status')
                ->default(true)
                ->inline(false),
                
        ])->columns(2);
    }
}