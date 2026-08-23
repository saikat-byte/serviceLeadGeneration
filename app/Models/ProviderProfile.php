<?php

namespace App\Models;

use App\Enums\ProviderAvailabilityStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'availability_status' => ProviderAvailabilityStatus::class,
        'experience_years' => 'integer',
        'rating_average' => 'decimal:2',
        'completed_jobs_count' => 'integer',
        'cancellation_count' => 'integer',
        'response_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}