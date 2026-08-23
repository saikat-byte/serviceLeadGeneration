<?php

namespace App\Models;

use App\Enums\RenewalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Renewal extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => RenewalStatus::class,
        'previous_end_date' => 'date',
        'new_start_date' => 'date',
        'new_end_date' => 'date',
        'amount' => 'decimal:2',
        'management_fee' => 'decimal:2',
    ];

    public function managementPlan(): BelongsTo
    {
        return $this->belongsTo(ManagementPlan::class);
    }
}