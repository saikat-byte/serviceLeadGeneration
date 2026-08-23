<?php

namespace App\Models;

use App\Enums\ReplacementStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Replacement extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ReplacementStatus::class,
    ];

    public function managementPlan(): BelongsTo
    {
        return $this->belongsTo(ManagementPlan::class);
    }

    public function oldProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'old_provider_id');
    }

    public function newProvider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'new_provider_id');
    }
}