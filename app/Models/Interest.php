<?php

namespace App\Models;

use App\Enums\InterestActorType;
use App\Traits\ManagesStateTransitions;
use App\Enums\InterestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interest extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'actor_type' => InterestActorType::class,
        'status' => InterestStatus::class,
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
            'expressed' => ['active', 'withdrawn', 'rejected', 'expired', 'selected'],
            'active'    => ['selected', 'rejected', 'expired'],
            'withdrawn' => [],
            'rejected'  => [],
            'expired'   => [],
            'selected'  => [],
        ];
    }
}