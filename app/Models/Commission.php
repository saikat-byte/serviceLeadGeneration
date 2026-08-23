<?php

namespace App\Models;

use App\Enums\CommissionModel;
use App\Enums\CommissionStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Commission extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'model' => CommissionModel::class,
        'status' => CommissionStatus::class,
        'base_amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'earned_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(Settlement::class);
    }


public function allowedTransitions(): array
    {
        return [
            'pending'    => ['calculated', 'earned', 'adjusted', 'reversed'], 
            
            'calculated' => ['earned', 'adjusted', 'reversed'],
            'earned'     => ['settled', 'adjusted', 'reversed'],
            'adjusted'   => ['earned', 'reversed'],
            'reversed'   => [],
            'settled'    => [],
        ];
    }
}