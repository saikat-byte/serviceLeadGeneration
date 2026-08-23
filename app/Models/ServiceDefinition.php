<?php

namespace App\Models;

use App\Enums\PricingType;
use App\Enums\RevenueModel;
use App\Enums\ServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDefinition extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'service_type' => ServiceType::class,
        'pricing_type' => PricingType::class,
        'revenue_model' => RevenueModel::class,
        'commission_rate' => 'decimal:2',
        'fixed_lead_fee' => 'decimal:2',
        'management_fee' => 'decimal:2',
        'requires_provider_verification' => 'boolean',
        'requires_customer_confirmation' => 'boolean',
        'requires_payment_before_booking' => 'boolean',
        'customer_requirements' => 'array',
        'provider_requirements' => 'array',
        'availability_rules' => 'array',
        'cancellation_rules' => 'array',
        'completion_rules' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}