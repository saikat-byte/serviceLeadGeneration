<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => BookingStatus::class,
        'scheduled_at' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    public function serviceJob(): HasOne
    {
        return $this->hasOne(ServiceJob::class);
    }

    public function cancellations(): HasMany
    {
        return $this->hasMany(BookingCancellation::class);
    }


public function allowedTransitions(): array
    {
        return [
            'pending'           => ['confirmed', 'cancelled'],
            'confirmed'         => ['provider_assigned', 'scheduled', 'work_started', 'cancelled'], // 'work_started' যোগ করা হলো
            'provider_assigned' => ['scheduled', 'work_started', 'cancelled'],
            'scheduled'         => ['on_the_way', 'arrived', 'work_started', 'cancelled'],
            'on_the_way'        => ['arrived', 'work_started', 'cancelled'],
            'arrived'           => ['work_started', 'cancelled'],
            'work_started'      => ['work_completed', 'cancelled'],
            'work_completed'    => ['payment_pending', 'paid', 'closed', 'disputed'],
            'payment_pending'   => ['paid', 'failed'],
            'paid'              => ['closed'],
            'closed'            => [],
            'cancelled'         => [],
            'no_show'           => [],
            'disputed'          => ['closed'],
            'failed'            => [],
        ];
    }
}