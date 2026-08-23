<?php

namespace App\Models;

use App\Enums\ServiceJobStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceJob extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $table = 'service_jobs';

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ServiceJobStatus::class,
        'completion_evidence' => 'array',
        'arrived_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'verified_at' => 'datetime',
        'closed_at' => 'datetime',
        'final_service_value' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

public function allowedTransitions(): array
    {
        return [
            'created'   => ['scheduled', 'arrived', 'started', 'cancelled'], // 'started' যোগ করা হলো
            'scheduled' => ['arrived', 'started', 'cancelled'],
            'arrived'   => ['started', 'cancelled'],
            'started'   => ['completed', 'cancelled'],
            'completed' => ['verified', 'closed', 'disputed'],
            'verified'  => ['closed'],
            'closed'    => [],
            'disputed'  => ['closed'],
            'cancelled' => [],
        ];
    }
}