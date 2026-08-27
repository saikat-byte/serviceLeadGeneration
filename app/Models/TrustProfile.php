<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'trust_score' => 'decimal:2',
        'rating_average' => 'decimal:2',
        'response_rate' => 'decimal:2',
        'cancellation_rate' => 'decimal:2',
        'completed_jobs' => 'integer',
        'complaints_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
