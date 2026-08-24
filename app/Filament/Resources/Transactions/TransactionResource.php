<?php

namespace App\Filament\Resources\Transactions;

use App\Filament\Resources\Transactions\Pages;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;
use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static \UnitEnum|string|null $navigationGroup = 'Finance & Revenue';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Transactions are system-logged, manual entry disabled
    }

    public static function canEdit($record): bool
    {
        return false; // Immutable audit log, editing disabled
    }
}