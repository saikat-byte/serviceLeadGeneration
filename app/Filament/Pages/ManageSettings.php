<?php

namespace App\Filament\Pages;

use App\Services\SettingsService;
// FIX: Filament 5.x architecture onujayi layout component gulo Schemas theke import kora holo
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'System Settings';

    protected string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Page load howar somoy cache theke data form e fill korbe
        $this->form->fill(SettingsService::getAll());
    }

    public function form($form)
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        // FIX: Tabs\Tab er bodole shudhu Tab::make use kora holo
                        Tab::make('General')
                            ->schema([
                                TextInput::make('platform_name')
                                    ->label('Platform Name')
                                    ->default('Local Service Platform')
                                    ->required(),
                                TextInput::make('support_email')
                                    ->label('Support Email')
                                    ->email()
                                    ->default('support@example.com')
                                    ->required(),
                                TextInput::make('support_phone')
                                    ->label('Support Phone')
                                    ->tel()
                                    ->default('+91-0000000000')
                                    ->required(),
                            ])->icon('heroicon-o-information-circle'),
                            
                        Tab::make('Lead & Booking')
                            ->schema([
                                TextInput::make('default_matching_radius')
                                    ->label('Default Matching Radius (km)')
                                    ->numeric()
                                    ->default(15)
                                    ->required(),
                                TextInput::make('provider_response_timeout')
                                    ->label('Provider Response Timeout (minutes)')
                                    ->numeric()
                                    ->default(30)
                                    ->required(),
                            ])->icon('heroicon-o-calendar-days'),
                            
                        Tab::make('Finance')
                            ->schema([
                                TextInput::make('default_commission_rate')
                                    ->label('Default Commission Rate (%)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->default(10)
                                    ->required(),
                                TextInput::make('minimum_settlement_amount')
                                    ->label('Minimum Settlement Amount (₹)')
                                    ->numeric()
                                    ->default(1000)
                                    ->required(),
                            ])->icon('heroicon-o-banknotes'),
                    ])
                    ->columnSpanFull()
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->color('primary'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            // Key er upor bhitti kore group define kora
            $group = 'general';
            if (in_array($key, ['default_commission_rate', 'minimum_settlement_amount'])) {
                $group = 'finance';
            } elseif (in_array($key, ['default_matching_radius', 'provider_response_timeout'])) {
                $group = 'booking';
            }

            // Database ebong cache update kora
            SettingsService::set($key, $value, $group);
        }

        Notification::make()
            ->title('Settings saved successfully.')
            ->success()
            ->send();
    }
}