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

    protected $guarded = [
        'id',
        'email_verified_at',
        'mobile_verified_at',
        'suspension_reason',
    ];

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

    public function providerProfile(): HasOne
    {
        return $this->hasOne(ProviderProfile::class, 'user_id');
    }

    public function providerServices(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'provider_id');
    }

    public function providerServiceAreas(): HasMany
    {
        return $this->hasMany(ProviderServiceArea::class, 'provider_id');
    }

    public function providerSkills(): HasMany
    {
        return $this->hasMany(ProviderSkill::class, 'provider_id');
    }

    public function providerAvailabilities(): HasMany
    {
        return $this->hasMany(ProviderAvailability::class, 'provider_id');
    }

    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Using ?->value safely checks the string value regardless of the Enum case name
        return $this->role?->value === 'admin';
    }

    // ==================================================
    // BATCH 20.1: PROVIDER READINESS & ONBOARDING LOGIC
    // ==================================================
    
    public function isEligibleForLeads(): bool
    {
        $roleCheck = $this->role instanceof \BackedEnum ? $this->role->value : $this->role;
        if ($roleCheck !== 'provider') return false;
        
        if (!$this->providerProfile) return false;
        
        // Must have at least one approved/active service
        $hasServices = $this->providerServices()->whereIn('status', ['active', 'approved'])->exists();
        if (!$hasServices) return false;
        
        if ($this->providerServiceAreas()->count() === 0) return false;
        
        // Depending on strict business logic, availability might be required
        if ($this->providerAvailabilities()->count() === 0) return false;
        
        return true;
    }

    public function onboardingProgress(): array
    {
        return [
            'profile' => (bool) $this->providerProfile,
            'services' => $this->providerServices()->exists(),
            'skills' => $this->providerSkills()->exists(),
            'areas' => $this->providerServiceAreas()->exists(),
            'availability' => $this->providerAvailabilities()->exists(),
        ];
    }
}