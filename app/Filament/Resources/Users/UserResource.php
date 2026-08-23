<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static \UnitEnum|string|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\FileUpload::make('avatar')
                ->label('Profile Photo')
                ->image()
                ->disk('public')
                ->directory('users/avatars') // Ebar Filament ekhanei save korbe prothome
                ->imageEditor()
                ->imageCropAspectRatio('1:1')
                ->maxSize(6000)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('mobile')
                ->label('Mobile Number')
                ->tel()
                ->maxLength(20),

            Forms\Components\TextInput::make('password')
                ->label(fn (string $operation): string => $operation === 'edit' ? 'New Password (leave blank to keep current)' : 'Password')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->maxLength(255),

            Forms\Components\Select::make('role')
                ->label('User Role')
                ->options(UserRole::class)
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Account Status')
                ->options(UserStatus::class)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->label('Photo')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=User&color=7F9CF5&background=EBF4FF')),

                Tables\Columns\TextColumn::make('id')->label('ID')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('mobile')->label('Mobile')->searchable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (?UserRole $state): string => $state ? str($state->value)->replace('_', ' ')->title() : '—')
                    ->color(fn (?UserRole $state): string => match ($state?->value) {
                        'customer' => 'info',
                        'provider' => 'success',
                        'admin' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?UserStatus $state): string => $state ? str($state->value)->replace('_', ' ')->title() : '—')
                    ->color(fn (?UserStatus $state): string => match ($state?->value) {
                        'registered', 'active' => 'success',
                        'profile_incomplete' => 'warning',
                        'suspended', 'blocked' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')->label('Created')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')->label('Role')->options(UserRole::class),
                Tables\Filters\SelectFilter::make('status')->label('Status')->options(UserStatus::class),
            ])
            ->actions([
                EditAction::make(), // Perfect Import Use Kora Holo
                
                DeleteAction::make()
                    ->action(function ($record, $action) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            if ($e->getCode() === '23000') {
                                Notification::make()
                                    ->danger()
                                    ->title('Cannot delete user')
                                    ->body('This user has active bookings or records. Deletion is blocked.')
                                    ->send();
                                $action->halt();
                            } else {
                                throw $e;
                            }
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete Selected Users'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}