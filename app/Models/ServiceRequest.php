<?php

namespace App\Models;

use App\Enums\ServiceRequestStatus;
use App\Enums\ServiceRequestUrgency;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ServiceRequestStatus::class,
        'urgency' => ServiceRequestUrgency::class,
        'requirements' => 'array',
        'preferred_at' => 'datetime',
        'submitted_at' => 'datetime',
        'qualified_at' => 'datetime',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
    ];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function serviceVariant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function lead(): HasOne
    {
        return $this->hasOne(Lead::class);
    }


    public function allowedTransitions(): array
    {
        return [
            'draft'      => ['submitted', 'cancelled'],
            'submitted'  => ['validating', 'qualified', 'cancelled'],
            'validating' => ['qualified', 'rejected', 'cancelled'],
            'qualified'  => ['expired', 'cancelled'],
            'rejected'   => [],
            'cancelled'  => [],
            'expired'    => [],
        ];
    }
}