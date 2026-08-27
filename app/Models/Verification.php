<?php

namespace App\Models;

use App\Enums\VerificationStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Verification extends Model
{
    use HasFactory, ManagesStateTransitions; // ADDED STATE TRANSITION TRAIT

    protected $guarded = ['id'];

    protected $casts = [
        'status' => VerificationStatus::class,
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ADDED STATE MACHINE RULES
    public function allowedTransitions(): array
    {
        return [
            'pending'      => ['submitted', 'not_required'],
            'submitted'    => ['under_review', 'verified', 'rejected'],
            'under_review' => ['verified', 'rejected', 'suspended'],
            'verified'     => ['expired', 'suspended'],
            'rejected'     => ['submitted', 'pending'],
            'expired'      => ['submitted', 'pending'],
            'suspended'    => ['verified', 'rejected'],
            'not_required' => ['pending'],
        ];
    }
}
