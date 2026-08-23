<?php

namespace App\Models;

use App\Enums\SkillVerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderSkill extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'verification_status' => SkillVerificationStatus::class,
        'experience_years' => 'integer',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}