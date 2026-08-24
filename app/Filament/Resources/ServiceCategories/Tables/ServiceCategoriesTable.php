<?php

namespace App\Filament\Resources\ServiceCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class ServiceCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('parent.name')->label('Parent Category')->sortable()->badge()->color('info')->searchable(),
                IconColumn::make('is_active')->boolean()->sortable(),
                TextColumn::make('sort_order')->numeric()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                
                // Delete Action exception handle kora holo
                DeleteAction::make()
                    ->using(function ($record) {
                        try {
                            return $record->delete();
                        } catch (QueryException $e) {
                            if ($e->getCode() === '23000') {
                                Notification::make()
                                    ->danger()
                                    ->title('Cannot delete category')
                                    ->body('This category has active services. Please delete or reassign them first.')
                                    ->send();
                                
                                throw new Halt();
                            }
                            throw $e;
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}