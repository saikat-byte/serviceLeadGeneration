<?php

namespace App\Models;

use App\Enums\ComplaintType;
use App\Enums\ComplaintStatus;
use App\Enums\ComplaintPriority;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'type' => ComplaintType::class,
        'status' => ComplaintStatus::class,
        'priority' => ComplaintPriority::class,
        'evidence' => 'array',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function allowedTransitions(): array
    {
        return [
            'created'         => ['acknowledged', 'under_review', 'rejected', 'closed'],
            'acknowledged'    => ['under_review', 'rejected', 'closed'],
            'under_review'    => ['investigating', 'resolved', 'rejected', 'closed'],
            'investigating'   => ['action_required', 'resolved', 'rejected', 'closed'],
            'action_required' => ['resolved', 'closed'],
            'resolved'        => ['closed'],
            'rejected'        => ['closed'],
            'closed'          => [],
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function complainant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'complainant_id');
    }

    public function againstUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'against_user_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}