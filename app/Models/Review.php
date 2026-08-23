<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ReviewStatus::class,
        'rating' => 'integer',
    ];

    public function allowedTransitions(): array
    {
        return [
            'pending'   => ['submitted', 'flagged', 'removed'],
            'submitted' => ['published', 'flagged', 'removed'],
            'published' => ['flagged', 'removed'],
            'flagged'   => ['published', 'removed'],
            'removed'   => [],
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}