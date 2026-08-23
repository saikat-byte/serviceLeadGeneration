<?php

namespace App\Models;

use App\Enums\MatchStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchRecord extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $table = 'matches';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => MatchStatus::class,
        'match_score' => 'decimal:2',
        'location_score' => 'decimal:2',
        'availability_score' => 'decimal:2',
        'skill_score' => 'decimal:2',
        'trust_score' => 'decimal:2',
        'price_score' => 'decimal:2',
        'offered_at' => 'datetime',
        'responded_at' => 'datetime',
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
            'created'   => ['ranked', 'offered'],
            'ranked'    => ['offered', 'expired'],
            'offered'   => ['responded', 'accepted', 'rejected', 'expired'],
            'responded' => ['accepted', 'rejected'],
            'accepted'  => ['selected', 'not_selected'],
            // Terminal states
            'rejected'     => [],
            'expired'      => [],
            'selected'     => [],
            'not_selected' => [],
        ];
    }
}