<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => PaymentStatus::class,
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }


    public function allowedTransitions(): array
    {
        return [
            'initiated'          => ['pending', 'processing', 'cancelled', 'failed'],
            'pending'            => ['processing', 'paid', 'cancelled', 'failed'],
            'processing'         => ['paid', 'failed'],
            'paid'               => ['refund_pending'],
            'failed'             => ['pending'], // Retry scenario
            'refund_pending'     => ['refunded', 'partially_refunded'],
            // Terminal states
            'cancelled'          => [],
            'refunded'           => [],
            'partially_refunded' => [],
        ];
    }
}