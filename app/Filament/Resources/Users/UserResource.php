<?php

namespace App\Filament\Resources\Users;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Filament\Resources\Users\Pages; 
use App\Models\TrustProfile;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
    protected static \UnitEnum|string|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            FileUpload::make('avatar')
                ->label('Profile Photo')
                ->image()
                ->disk('public')
                ->directory('users/avatars') 
                ->imageEditor()
                ->imageCropAspectRatio('1:1')
                ->maxSize(6000)
                ->columnSpanFull(),

            TextInput::make('name')
                ->label('Name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('mobile')
                ->label('Mobile Number')
                ->tel()
                ->maxLength(20),

            TextInput::make('password')
                ->label(fn (string $operation): string => $operation === 'edit' ? 'New Password (leave blank to keep current)' : 'Password')
                ->password()
                ->revealable()
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->maxLength(255),

            Select::make('role')
                ->label('User Role')
                ->options(UserRole::class)
                ->required(),

            Select::make('status')
                ->label('Account Status')
                ->options(UserStatus::class)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Photo')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('https://ui-avatars.com/api/?name=User&color=7F9CF5&background=EBF4FF')),

                TextColumn::make('id')->label('ID')->sortable()->searchable(),
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('mobile')->label('Mobile')->searchable(),

                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (?UserRole $state): string => $state ? str($state->value)->replace('_', ' ')->title() : '—')
                    ->color(fn (?UserRole $state): string => match ($state?->value) {
                        'customer' => 'info',
                        'provider' => 'success',
                        'admin' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?UserStatus $state): string => $state ? str($state->value)->replace('_', ' ')->title() : '—')
                    ->color(fn (?UserStatus $state): string => match ($state?->value) {
                        'registered', 'active' => 'success',
                        'profile_incomplete' => 'warning',
                        'suspended', 'blocked' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')->label('Created')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')->label('Role')->options(UserRole::class),
                SelectFilter::make('status')->label('Status')->options(UserStatus::class),
            ])
            ->actions([
                Action::make('viewTrustProfile')
                    ->label('Trust Profile')
                    ->icon('heroicon-o-shield-check')
                    ->color('info')
                    ->modalHeading('Trust & Safety Profile')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->fillForm(function (User $record): array {
                        $profile = TrustProfile::where('user_id', $record->id)->first();
                        return [
                            'trust_score' => $profile?->trust_score ?? '0.00',
                            'rating_average' => $profile?->rating_average ?? '0.00',
                            'completed_jobs' => $profile?->completed_jobs ?? 0,
                            'cancellation_rate' => $profile?->cancellation_rate ?? '0.00',
                            'complaints_count' => $profile?->complaints_count ?? 0,
                        ];
                    })
                    ->form([
                        TextInput::make('trust_score')->label('Trust Score / 100')->disabled(),
                        TextInput::make('rating_average')->label('Average Rating')->disabled(),
                        TextInput::make('completed_jobs')->label('Completed Jobs')->disabled(),
                        TextInput::make('cancellation_rate')->label('Cancel Rate (%)')->disabled(),
                        TextInput::make('complaints_count')->label('Total Complaints')->disabled(),
                    ]),

                Action::make('safetyAction')
                    ->label('Suspend')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->visible(fn (User $record): bool => in_array($record->status?->value ?? $record->status, ['registered', 'active', 'profile_incomplete']))
                    ->form([
                        Select::make('new_status')
                            ->label('Action Type')
                            ->options([
                                'suspended' => 'Suspend User (Temporary)',
                                'blocked' => 'Block User (Permanent)',
                            ])
                            ->required(),
                        Textarea::make('reason')
                            ->label('Reason for action')
                            ->required()
                            ->helperText('Ei reason ti internal audit er jonno save kora hobe.'),
                    ])
                    ->action(function (array $data, User $record): void {
                        $record->update(['status' => $data['new_status']]);
                        Notification::make()->title("User successfully {$data['new_status']}")->success()->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Take Safety Action'),

                Action::make('restoreUser')
                    ->label('Restore Access')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record): bool => in_array($record->status?->value ?? $record->status, ['suspended', 'blocked']))
                    ->requiresConfirmation()
                    ->modalHeading('Restore User')
                    ->action(function (User $record): void {
                        $record->update(['status' => 'active']);
                        Notification::make()->title("User access restored")->success()->send();
                    }),

                EditAction::make(), 
                
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