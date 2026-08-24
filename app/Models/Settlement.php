<?php

namespace App\Models;

use App\Enums\SettlementStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settlement extends Model
{
    // MISSING TRAIT ADDED
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => SettlementStatus::class,
        'gross_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'settled_at' => 'datetime',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    // MISSING STATE MACHINE LOGIC ADDED
    public function allowedTransitions(): array
    {
        return [
            'pending'    => ['processing', 'settled', 'failed'],
            'processing' => ['settled', 'failed'],
            'settled'    => [],
            'failed'     => ['pending'],
        ];
    }
}