<?php

namespace App\Models;

use App\Enums\ConnectionStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Connection extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ConnectionStatus::class,
        'unlocked_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }


    public function allowedTransitions(): array
    {
        return [
            'pending'  => ['unlocked', 'closed', 'blocked'],
            'unlocked' => ['active', 'closed', 'blocked'],
            'active'   => ['closed', 'blocked'],
            'closed'   => [],
            'blocked'  => [],
        ];
    }
}