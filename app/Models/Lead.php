<?php

namespace App\Models;

use App\Enums\LeadStatus;
use App\Traits\ManagesStateTransitions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory, ManagesStateTransitions;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => LeadStatus::class,
        'distributed_at' => 'datetime',
        'expires_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function allowedTransitions(): array
    {
        return [
            // 'offered' ebong 'accepted' remove kora holo karon egulo MatchRecord er part
            'created'     => ['qualified', 'cancelled'],
            'qualified'   => ['matching', 'cancelled'],
            'matching'    => ['distributed', 'unfulfilled', 'cancelled'],
            'distributed' => ['responding', 'interested', 'selected', 'expired', 'cancelled'],
            'responding'  => ['interested', 'expired', 'unfulfilled', 'cancelled'],
            'interested'  => ['selected', 'expired', 'cancelled'],
            'selected'    => ['converted', 'cancelled'], // converted add kora holo end state hisebe
            'expired'     => [],
            'converted'   => [],
            'cancelled'   => [],
            'unfulfilled' => [],
        ];
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchRecord::class, 'lead_id');
    }

    public function interests(): HasMany
    {
        return $this->hasMany(Interest::class);
    }

    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }
}