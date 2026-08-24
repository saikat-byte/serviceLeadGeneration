<?php

namespace App\Models;

use App\Enums\InterestStatus;
use App\Enums\InterestActorType;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interest extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => InterestStatus::class,
        'actor_type' => InterestActorType::class,
        'expressed_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function allowedTransitions(): array
    {
        return [
            'active'    => ['selected', 'withdrawn', 'rejected', 'expired'],
            'expressed' => ['active', 'withdrawn', 'expired'],
            'withdrawn' => [],
            'rejected'  => [],
            'expired'   => [],
            'selected'  => [],
        ];
    }
}