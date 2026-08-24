<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserStatus::class,
        ];
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function providerProfile()
    {
        return $this->hasOne(ProviderProfile::class, 'user_id');
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class, 'provider_id');
    }

    public function providerServiceAreas()
    {
        return $this->hasMany(ProviderServiceArea::class, 'provider_id');
    }

    public function providerSkills()
    {
        return $this->hasMany(ProviderSkill::class, 'provider_id');
    }

    public function providerAvailabilities()
    {
        return $this->hasMany(ProviderAvailability::class, 'provider_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyRole(array_column(\App\Enums\AdminRole::cases(), 'value'));
    }
}